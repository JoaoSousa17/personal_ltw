<?php
require_once(dirname(__FILE__) . '/../Models/User.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Regista um novo utilizador no sistema.
 *
 * @param string $username Username do utilizador.
 * @param string $email Email do utilizador.
 * @param string $password Palavra-passe do utilizador.
 * @param string $name Nome do utilizador.
 * @param bool $isAdmin Define se o utilizador é administrador (por omissão: false).
 * @return bool|User Objeto User se registado com sucesso, False caso contrário.
 */
function registerUser($username, $email, $password, $name, $isAdmin = false) {
    $db = getDatabaseConnection();

    if (User::findByUsername($db, $username) || User::findByEmail($db, $email)) {
        return false;
    }

    $user = new User($db);
    $user->setUsername($username);
    $user->setEmail($email);
    $user->setPassword($password);
    $user->setName($name);
    $user->setIsAdmin($isAdmin);
    $user->setIsBlocked(false);

    return $user->save() ? $user : false;
}

/**
 * Autentica um utilizador no sistema.
 *
 * @param string $username Username do utilizador.
 * @param string $password Palavra-passe.
 * @return bool|User Objeto User se autenticado com sucesso, False caso contrário.
 */
function loginUser($username, $password) {
    $db = getDatabaseConnection();
    $user = User::authenticate($db, $username, $password);

    if ($user && !$user->getIsBlocked()) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['is_admin'] = $user->getIsAdmin();

        return $user;
    }

    return false;
}

/**
 * Termina a sessão do utilizador atual.
 *
 * @return bool True se sessão terminada com sucesso.
 */
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $_SESSION = [];
    session_destroy();
    return true;
}

/**
 * Obtém todos os utilizadores registados.
 *
 * @return array Lista de objetos User.
 */
function getAllUsers() {
    $db = getDatabaseConnection();
    return User::getAllUsers($db);
}

/**
 * Obtém todos os utilizadores bloqueados.
 *
 * @return array Lista de objetos User bloqueados.
 */
function getAllBlockedUsers() {
    $db = getDatabaseConnection();
    return User::getAllBlockedUsers($db);
}

/**
 * Obtém um utilizador através do seu ID.
 *
 * @param int $id ID do utilizador.
 * @return User|null Objeto User se encontrado, null caso contrário.
 */
function getUserById($id) {
    $db = getDatabaseConnection();
    return User::findById($db, $id);
}

/**
 * Obtém um utilizador através do seu username.
 *
 * @param string $username Username do utilizador.
 * @return User|null Objeto User se encontrado, null caso contrário.
 */
function getUserByUsername($username) {
    $db = getDatabaseConnection();
    return User::findByUsername($db, $username);
}

/**
 * Obtém um utilizador através do seu email.
 *
 * @param string $email Email do utilizador.
 * @return User|null Objeto User se encontrado, null caso contrário.
 */
function getUserByEmail($email) {
    $db = getDatabaseConnection();
    return User::findByEmail($db, $email);
}

/**
 * Atualiza os dados de um utilizador.
 *
 * @param int $id ID do utilizador.
 * @param array $data Dados a atualizar.
 * @return bool True se atualizado com sucesso, False caso contrário.
 */
function updateUser($id, $data) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    if (!$user) return false;

    if (isset($data['username'])) $user->setUsername($data['username']);
    if (isset($data['email'])) $user->setEmail($data['email']);
    if (isset($data['password'])) $user->setPassword($data['password']);
    if (isset($data['name'])) $user->setName($data['name']);

    return $user->save();
}

/**
 * Bloqueia um utilizador.
 *
 * @param int $id ID do utilizador.
 * @return bool True se bloqueado com sucesso, False caso contrário.
 */
function blockUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->blockUser() : false;
}

/**
 * Desbloqueia um utilizador.
 *
 * @param int $id ID do utilizador.
 * @return bool True se desbloqueado com sucesso, False caso contrário.
 */
function unblockUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->unblockUser() : false;
}

/**
 * Elimina um utilizador do sistema.
 *
 * @param int $id ID do utilizador.
 * @return bool True se eliminado com sucesso, False caso contrário.
 */
function deleteUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->delete() : false;
}

/**
 * Pesquisa utilizadores por email ou username.
 *
 * @param string $term Termo de pesquisa.
 * @return array Lista de objetos User.
 */
function searchUsers($term) {
    $db = getDatabaseConnection();
    $term = "%$term%";

    $stmt = $db->prepare("SELECT * FROM User_ WHERE email LIKE :term OR username LIKE :term ORDER BY id ASC");
    $stmt->execute([':term' => $term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = [];
    foreach ($results as $row) {
        $user = new User($db);
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setName($row['name_']);
        if (isset($row['creation_date'])) $user->setRegisterDate($row['creation_date']);
        if (isset($row['web_link'])) $user->setWebLink($row['web_link']);
        if (isset($row['is_freelancer'])) $user->setIsFreelancer($row['is_freelancer']);
        $user->setIsAdmin($row['is_admin']);
        $user->setIsBlocked($row['is_blocked']);
        $users[] = $user;
    }

    return $users;
}

/**
 * Obtém dados completos do utilizador incluindo todos os campos da tabela User_.
 *
 * @param int $userId ID do utilizador
 * @return array|null Dados do utilizador ou null se não encontrado
 */
function getUserCompleteData($userId) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT id, name_, password_, email, username, web_link, 
                   phone_number, profile_photo, is_admin, creation_date, 
                   is_freelancer, currency, is_blocked, night_mode
            FROM User_ 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null;
        }

        // Garantir que campos opcionais têm valores padrão
        $userData['web_link'] = $userData['web_link'] ?? '';
        $userData['phone_number'] = $userData['phone_number'] ?? '';
        $userData['profile_photo'] = $userData['profile_photo'] ?? null;
        
        // Converter valores booleanos
        $userData['is_admin'] = (bool)$userData['is_admin'];
        $userData['is_blocked'] = (bool)$userData['is_blocked'];
        $userData['is_freelancer'] = (bool)$userData['is_freelancer'];
        $userData['night_mode'] = (bool)$userData['night_mode'];

        return $userData;
    } catch (PDOException $e) {
        error_log("Erro ao obter dados completos do utilizador: " . $e->getMessage());
        return null;
    }
}

/**
 * Verifica se o utilizador tem permissão para editar um perfil.
 *
 * @param int $currentUserId ID do utilizador atual
 * @param int $targetUserId ID do utilizador a ser editado
 * @param bool $isAdmin Se o utilizador atual é admin
 * @return bool True se tem permissão, false caso contrário
 */
function canEditProfile($currentUserId, $targetUserId, $isAdmin = false) {
    // Pode editar o próprio perfil ou se for admin
    return ($currentUserId == $targetUserId) || $isAdmin;
}

/**
 * Obtém as moedas disponíveis no sistema.
 *
 * @return array Array associativo com código e nome das moedas
 */
function getAvailableCurrencies() {
    return [
        'eur' => 'Euro (€)',
        'usd' => 'Dólar Americano ($)',
        'gbp' => 'Libra Esterlina (£)',
        'brl' => 'Real Brasileiro (R$)'
    ];
}

/**
 * Atualiza o perfil completo de um utilizador.
 *
 * @param int $userId ID do utilizador
 * @param array $data Dados a serem atualizados
 * @return array Resultado da operação com status e mensagem
 */
function updateUserProfile($userId, $data) {
    try {
        $db = getDatabaseConnection();
        
        // Verificar se o utilizador existe
        $stmt = $db->prepare("SELECT id FROM User_ WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Utilizador não encontrado.'];
        }

        // Validar dados
        $validationResult = validateProfileData($data, $userId);
        if (!$validationResult['success']) {
            return $validationResult;
        }

        // Preparar query de atualização
        $updateFields = [];
        $params = [':id' => $userId];

        // Campos que podem ser atualizados
        if (isset($data['name']) && !empty($data['name'])) {
            $updateFields[] = "name_ = :name";
            $params[':name'] = trim($data['name']);
        }

        if (isset($data['username']) && !empty($data['username'])) {
            $updateFields[] = "username = :username";
            $params[':username'] = trim($data['username']);
        }

        if (isset($data['email']) && !empty($data['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = trim($data['email']);
        }

        if (isset($data['web_link'])) {
            $updateFields[] = "web_link = :web_link";
            $params[':web_link'] = !empty(trim($data['web_link'])) ? trim($data['web_link']) : null;
        }

        if (isset($data['phone_number'])) {
            $updateFields[] = "phone_number = :phone_number";
            $params[':phone_number'] = !empty(trim($data['phone_number'])) ? trim($data['phone_number']) : null;
        }

        if (isset($data['currency']) && !empty($data['currency'])) {
            $updateFields[] = "currency = :currency";
            $params[':currency'] = trim($data['currency']);
        }

        if (isset($data['night_mode'])) {
            $updateFields[] = "night_mode = :night_mode";
            $params[':night_mode'] = (bool)$data['night_mode'] ? 1 : 0;
        }

        // Password (se fornecida e confirmada)
        if (isset($data['password']) && !empty($data['password'])) {
            $updateFields[] = "password_ = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($updateFields)) {
            return ['success' => false, 'message' => 'Nenhum dado para atualizar.'];
        }

        // Executar atualização
        $query = "UPDATE User_ SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute($params)) {
            return ['success' => true, 'message' => 'Perfil atualizado com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao atualizar o perfil.'];
        }

    } catch (PDOException $e) {
        error_log("Erro ao atualizar perfil: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    }
}

/**
 * Valida os dados do perfil antes da atualização.
 *
 * @param array $data Dados a serem validados
 * @param int $userId ID do utilizador atual
 * @return array Resultado da validação
 */
function validateProfileData($data, $userId) {
    $db = getDatabaseConnection();

    // Validar email se fornecido
    if (isset($data['email']) && !empty($data['email'])) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido.'];
        }

        // Verificar se email já existe (excluindo o utilizador atual)
        $stmt = $db->prepare("SELECT id FROM User_ WHERE email = :email AND id != :user_id");
        $stmt->execute([':email' => $data['email'], ':user_id' => $userId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Este email já está em uso.'];
        }
    }

    // Validar username se fornecido
    if (isset($data['username']) && !empty($data['username'])) {
        if (strlen($data['username']) < 3) {
            return ['success' => false, 'message' => 'Username deve ter pelo menos 3 caracteres.'];
        }

        // Verificar se username já existe (excluindo o utilizador atual)
        $stmt = $db->prepare("SELECT id FROM User_ WHERE username = :username AND id != :user_id");
        $stmt->execute([':username' => $data['username'], ':user_id' => $userId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Este username já está em uso.'];
        }
    }

    // Validar nome se fornecido
    if (isset($data['name']) && !empty($data['name'])) {
        if (strlen($data['name']) < 2) {
            return ['success' => false, 'message' => 'Nome deve ter pelo menos 2 caracteres.'];
        }
    }

    // Validar password se fornecida
    if (isset($data['password']) && !empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password deve ter pelo menos 6 caracteres.'];
        }

        // Validar confirmação de password
        if (isset($data['password_confirm'])) {
            if ($data['password'] !== $data['password_confirm']) {
                return ['success' => false, 'message' => 'As passwords não coincidem.'];
            }
        }
    }

    // Validar web_link se fornecido
    if (isset($data['web_link']) && !empty($data['web_link'])) {
        if (!filter_var($data['web_link'], FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'URL do website inválida.'];
        }
    }

    // Validar telefone se fornecido
    if (isset($data['phone_number']) && !empty($data['phone_number'])) {
        if (strlen($data['phone_number']) < 9) {
            return ['success' => false, 'message' => 'Número de telefone deve ter pelo menos 9 dígitos.'];
        }
    }

    // Validar moeda se fornecida
    if (isset($data['currency']) && !empty($data['currency'])) {
        $validCurrencies = getAvailableCurrencies();
        if (!in_array($data['currency'], array_keys($validCurrencies))) {
            return ['success' => false, 'message' => 'Moeda selecionada inválida.'];
        }
    }

    return ['success' => true, 'message' => 'Dados válidos.'];
}

/**
 * Atualiza apenas a moeda do utilizador.
 *
 * @param int $userId ID do utilizador
 * @param string $currency Código da moeda
 * @return array Resultado da operação
 */
function updateUserCurrency($userId, $currency) {
    try {
        $db = getDatabaseConnection();
        $validCurrencies = getAvailableCurrencies();
        if (!array_key_exists($currency, $validCurrencies)) {
            return ['success' => false, 'message' => 'Moeda inválida.'];
        }

        $stmt = $db->prepare("UPDATE User_ SET currency = :currency WHERE id = :id");
        $result = $stmt->execute([':currency' => $currency, ':id' => $userId]);

        if ($result) {
            return ['success' => true, 'message' => 'Moeda atualizada com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao atualizar a moeda.'];
        }
    } catch (PDOException $e) {
        error_log("Erro ao atualizar moeda: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    }
}

// ===== FUNÇÕES DE GESTÃO DE FOTOS DE PERFIL =====

/**
 * Faz upload de uma nova foto de perfil para um utilizador
 * 
 * @param int $userId ID do utilizador
 * @param array $file Array do $_FILES['photo']
 * @return array Resultado da operação
 */
function uploadProfilePhoto($userId, $file) {
    try {
        $db = getDatabaseConnection();
        
        // Validar se o utilizador existe
        $stmt = $db->prepare("SELECT id, profile_photo FROM User_ WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Utilizador não encontrado.'];
        }
        
        // Validar o ficheiro
        $validation = validateProfileImage($file);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Eliminar foto anterior se existir
        if ($user['profile_photo']) {
            deleteProfilePhoto($userId);
        }
        
        // Processar upload
        $uploadResult = processPhotoUpload($userId, $file);
        if (!$uploadResult['success']) {
            return $uploadResult;
        }
        
        // Guardar na tabela Media
        $stmt = $db->prepare("INSERT INTO Media (path_, title) VALUES (:path, :title)");
        $stmt->execute([
            ':path' => $uploadResult['path'],
            ':title' => 'Foto de perfil - User ' . $userId
        ]);
        
        $mediaId = $db->lastInsertId();
        
        // Atualizar tabela User_
        $stmt = $db->prepare("UPDATE User_ SET profile_photo = :photo_id WHERE id = :id");
        $stmt->execute([
            ':photo_id' => $mediaId,
            ':id' => $userId
        ]);
        
        return [
            'success' => true, 
            'message' => 'Foto de perfil atualizada com sucesso!',
            'media_id' => $mediaId,
            'path' => $uploadResult['path']
        ];
        
    } catch (Exception $e) {
        error_log("Erro no upload da foto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    }
}

/**
 * Elimina a foto de perfil atual de um utilizador
 * 
 * @param int $userId ID do utilizador
 * @return array Resultado da operação
 */
function deleteProfilePhoto($userId) {
    try {
        $db = getDatabaseConnection();
        
        // Obter dados da foto atual
        $stmt = $db->prepare("
            SELECT u.profile_photo, m.path_ 
            FROM User_ u 
            LEFT JOIN Media m ON u.profile_photo = m.id 
            WHERE u.id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !$user['profile_photo']) {
            return ['success' => true, 'message' => 'Não há foto para eliminar.'];
        }
        
        // Eliminar ficheiro físico
        if ($user['path_']) {
            $fullPath = dirname(__DIR__) . '/' . $user['path_'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        // Remover da tabela Media
        $stmt = $db->prepare("DELETE FROM Media WHERE id = :id");
        $stmt->execute([':id' => $user['profile_photo']]);
        
        // Atualizar tabela User_
        $stmt = $db->prepare("UPDATE User_ SET profile_photo = NULL WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        
        return ['success' => true, 'message' => 'Foto eliminada com sucesso.'];
        
    } catch (Exception $e) {
        error_log("Erro ao eliminar foto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao eliminar a foto.'];
    }
}

/**
 * Valida se o ficheiro é uma imagem válida
 * 
 * @param array $file Array do $_FILES
 * @return array Resultado da validação
 */
function validateProfileImage($file) {
    // Verificar se há erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'message' => 'Ficheiro muito grande.'];
            case UPLOAD_ERR_PARTIAL:
                return ['success' => false, 'message' => 'Upload incompleto.'];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'message' => 'Nenhum ficheiro selecionado.'];
            default:
                return ['success' => false, 'message' => 'Erro no upload.'];
        }
    }
    
    // Verificar tamanho (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Ficheiro muito grande. Máximo 5MB.'];
    }
    
    // Verificar tipo MIME
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.'];
    }
    
    // Verificar se é realmente uma imagem
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'message' => 'Ficheiro não é uma imagem válida.'];
    }
    
    return ['success' => true];
}

/**
 * Processa o upload e move o ficheiro para o destino
 * 
 * @param int $userId ID do utilizador
 * @param array $file Array do $_FILES
 * @return array Resultado do processamento
 */
function processPhotoUpload($userId, $file) {
    try {
        // Criar diretório se não existir
        $uploadDir = dirname(__DIR__) . '/Images/profile/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Erro ao criar diretório.'];
            }
        }
        
        // Gerar nome do ficheiro
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'user_' . $userId . '.' . strtolower($extension);
        $filePath = $uploadDir . $fileName;
        $relativePath = 'Images/profile/' . $fileName;
        
        // Mover ficheiro
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Erro ao guardar o ficheiro.'];
        }
        
        // Redimensionar imagem se necessário
        resizeProfileImage($filePath, 400, 400);
        
        return [
            'success' => true,
            'path' => $relativePath,
            'full_path' => $filePath
        ];
        
    } catch (Exception $e) {
        error_log("Erro no processamento do upload: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao processar o ficheiro.'];
    }
}

/**
 * Redimensiona uma imagem mantendo a proporção
 * 
 * @param string $filePath Caminho do ficheiro
 * @param int $maxWidth Largura máxima
 * @param int $maxHeight Altura máxima
 */
function resizeProfileImage($filePath, $maxWidth, $maxHeight) {
    try {
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) return;
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Se a imagem já é pequena, não redimensionar
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }
        
        // Calcular novas dimensões mantendo proporção
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
        
        // Criar imagem de origem
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filePath);
                break;
            default:
                return;
        }
        
        if (!$source) return;
        
        // Criar imagem de destino
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preservar transparência para PNG e GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensionar
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Guardar imagem redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filePath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filePath, 6);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filePath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $filePath, 85);
                break;
        }
        
        // Libertar memória
        imagedestroy($source);
        imagedestroy($destination);
        
    } catch (Exception $e) {
        error_log("Erro ao redimensionar imagem: " . $e->getMessage());
    }
}

/**
 * Obtém o URL da foto de perfil de um utilizador
 * 
 * @param int $userId ID do utilizador
 * @return string|null URL da foto ou null se não existir
 */
function getProfilePhotoUrl($userId) {
    try {
        $db = getDatabaseConnection();
        
        $stmt = $db->prepare("
            SELECT m.path_ 
            FROM User_ u 
            LEFT JOIN Media m ON u.profile_photo = m.id 
            WHERE u.id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['path_'] ? '/' . $result['path_'] : null;
        
    } catch (Exception $e) {
        error_log("Erro ao obter URL da foto: " . $e->getMessage());
        return null;
    }
}

/**
 * Promove um utilizador a administrador.
 *
 * @param int $id ID do utilizador.
 * @return bool True se promovido com sucesso, False caso contrário.
 */
function promoteUserToAdmin($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    if (!$user) return false;

    return $user->promoteToAdmin();
}

/**
 * Processamento de requisições HTTP para atualizações de perfil.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Utilizador não autenticado.';
        header("Location: /Views/auth.php");
        exit;
    }

    $currentUserId = $_SESSION['user_id'];
    $isAdmin = $_SESSION['is_admin'] ?? false;
    $targetUserId = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : $currentUserId;

    if (!canEditProfile($currentUserId, $targetUserId, $isAdmin)) {
        $_SESSION['error'] = 'Não tem permissão para editar este perfil.';
        header("Location: /Views/profile/profile.php");
        exit;
    }

    $data = [
        'name' => $_POST['name'] ?? '',
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'web_link' => $_POST['web_link'] ?? '',
        'phone_number' => $_POST['phone_number'] ?? '',
        'currency' => $_POST['currency'] ?? '',
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? ''
    ];

    // Adicionar night_mode apenas se for o próprio perfil
    if ($targetUserId == $currentUserId) {
        $data['night_mode'] = $_POST['night_mode'] ?? 0;
    }

    // Filtrar campos vazios (exceto night_mode e campos opcionais)
    $data = array_filter($data, function($value, $key) {
        return $key === 'night_mode' || $key === 'web_link' || $key === 'phone_number' || !empty($value);
    }, ARRAY_FILTER_USE_BOTH);

    $result = updateUserProfile($targetUserId, $data);

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        if ($targetUserId == $currentUserId && isset($data['username'])) {
            $_SESSION['username'] = $data['username'];
        }
    } else {
        $_SESSION['error'] = $result['message'];
    }

    $redirectUrl = "/Views/profile/editProfile.php";
    if ($targetUserId != $currentUserId) {
        $redirectUrl .= "?id=" . $targetUserId;
    }
    
    header("Location: " . $redirectUrl);
    exit;
}

// Processamento de requisições AJAX para fotos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se é uma requisição para gestão de fotos
    if (isset($_POST['action']) && in_array($_POST['action'], ['upload_photo', 'delete_photo'])) {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }
        
        $currentUserId = $_SESSION['user_id'];
        $isAdmin = $_SESSION['is_admin'] ?? false;
        $targetUserId = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : $currentUserId;
        
        // Verificar permissões
        if (!canEditProfile($currentUserId, $targetUserId, $isAdmin)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            exit;
        }
        
        $action = $_POST['action'];
        
        switch ($action) {
            case 'upload_photo':
                if (!isset($_FILES['photo'])) {
                    echo json_encode(['success' => false, 'message' => 'Nenhum ficheiro enviado.']);
                    exit;
                }
                
                $result = uploadProfilePhoto($targetUserId, $_FILES['photo']);
                echo json_encode($result);
                break;
                
            case 'delete_photo':
                $result = deleteProfilePhoto($targetUserId);
                echo json_encode($result);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
        }
        exit;
    }
}
?>