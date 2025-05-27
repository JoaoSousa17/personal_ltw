<?php
require_once(dirname(__FILE__) . '/../Models/Category.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Devolve todas as categorias existentes na base de dados.
 * 
 * @return array Lista de categorias em formato array associativo.
 */
function getAllCategories() {
    try {
        $categories = Category::getAllCategories();
        $result = [];

        // Converte cada objeto Category num array associativo
        foreach ($categories as $category) {
            $result[] = $category->toArray();
        }

        return $result;
    } 
    
    // Em caso de erro, devolve um array vazio
    catch (PDOException $e) {
        error_log("Erro ao obter categorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Procura uma categoria pelo seu ID.
 * 
 * @param int $id ID da categoria a procurar.
 * @return array|null Categoria em formato associativo ou null se não encontrada.
 */
function getCategoryById($id) {
    try {
        $category = Category::getCategoryById($id);

        if ($category) {
            return $category->toArray();
        }
        return null;
    } 
    
    // Em caso de erro, devolve null
    catch (PDOException $e) {
        error_log("Erro ao obter categoria por ID: " . $e->getMessage());
        return null;
    }
}

/**
 * Conta o número de serviços associados a uma categoria.
 *
 * @param int $categoryId ID da categoria.
 * @return int Número de serviços na categoria.
 */
function getServiceCountByCategory($categoryId) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM Service_ WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    } catch (PDOException $e) {
        error_log("Erro ao contar serviços da categoria: " . $e->getMessage());
        return 0;
    }
}

/**
 * Cria uma nova categoria com upload de imagem.
 *
 * @param string $name Nome da categoria.
 * @param array $imageFile Array do $_FILES['image'].
 * @return array Resultado da operação com status e mensagem.
 */
function createCategory($name, $imageFile) {
    try {
        error_log("=== INÍCIO CREATE CATEGORY ===");
        error_log("Nome recebido: " . $name);
        error_log("Ficheiro recebido: " . print_r($imageFile, true));
        
        $db = getDatabaseConnection();
        
        // Validar nome da categoria
        if (empty(trim($name))) {
            error_log("Nome da categoria vazio");
            return ['success' => false, 'message' => 'Nome da categoria é obrigatório.'];
        }
        
        // Verificar se a categoria já existe
        $stmt = $db->prepare("SELECT id FROM Category WHERE name_ = :name");
        $stmt->execute([':name' => trim($name)]);
        if ($stmt->fetch()) {
            error_log("Categoria já existe: " . $name);
            return ['success' => false, 'message' => 'Já existe uma categoria com este nome.'];
        }
        
        // Processar upload da imagem
        error_log("A processar upload da imagem...");
        $uploadResult = uploadCategoryImage($imageFile);
        if (!$uploadResult['success']) {
            error_log("Erro no upload: " . $uploadResult['message']);
            return $uploadResult;
        }
        
        error_log("Upload bem-sucedido: " . $uploadResult['path']);
        
        // Inserir na tabela Media
        $stmt = $db->prepare("INSERT INTO Media (path_, title) VALUES (:path, :title)");
        $result = $stmt->execute([
            ':path' => $uploadResult['path'],
            ':title' => 'Categoria: ' . trim($name)
        ]);
        
        if (!$result) {
            error_log("Erro ao inserir na tabela Media");
            return ['success' => false, 'message' => 'Erro ao guardar dados da imagem.'];
        }
        
        $mediaId = $db->lastInsertId();
        error_log("Media ID criado: " . $mediaId);
        
        // Inserir na tabela Category
        $stmt = $db->prepare("INSERT INTO Category (name_, photo_id) VALUES (:name, :photo_id)");
        $result = $stmt->execute([
            ':name' => trim($name),
            ':photo_id' => $mediaId
        ]);
        
        if ($result) {
            $categoryId = $db->lastInsertId();
            error_log("Categoria criada com sucesso. ID: " . $categoryId);
            return ['success' => true, 'message' => 'Categoria criada com sucesso!'];
        } else {
            error_log("Erro ao inserir categoria na base de dados");
            // Se falhou a criação da categoria, limpar a imagem
            if (file_exists(dirname(__DIR__) . '/' . $uploadResult['path'])) {
                unlink(dirname(__DIR__) . '/' . $uploadResult['path']);
            }
            $stmt = $db->prepare("DELETE FROM Media WHERE id = :id");
            $stmt->execute([':id' => $mediaId]);
            return ['success' => false, 'message' => 'Erro ao criar categoria.'];
        }
        
    } catch (PDOException $e) {
        error_log("Erro PDO ao criar categoria: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema: ' . $e->getMessage()];
    } catch (Exception $e) {
        error_log("Erro geral ao criar categoria: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema: ' . $e->getMessage()];
    }
}

/**
 * Remove uma categoria e todos os serviços associados em cascata.
 *
 * @param int $categoryId ID da categoria a remover.
 * @return array Resultado da operação com status e mensagem.
 */
function deleteCategory($categoryId) {
    try {
        $db = getDatabaseConnection();
        
        // Verificar se a categoria existe e obter dados
        $stmt = $db->prepare("
            SELECT c.name_, c.photo_id, m.path_ 
            FROM Category c 
            LEFT JOIN Media m ON c.photo_id = m.id 
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            return ['success' => false, 'message' => 'Categoria não encontrada.'];
        }
        
        // Contar serviços associados
        $serviceCount = getServiceCountByCategory($categoryId);
        
        // Iniciar transação para garantir consistência
        $db->beginTransaction();
        
        try {
            // 1. Eliminar todos os serviços associados (se existirem)
            if ($serviceCount > 0) {
                // Primeiro, eliminar dados relacionados aos serviços
                $stmt = $db->prepare("
                    DELETE FROM Service_Data 
                    WHERE service_id IN (SELECT id FROM Service_ WHERE category_id = :category_id)
                ");
                $stmt->execute([':category_id' => $categoryId]);
                
                // Eliminar feedback dos serviços
                $stmt = $db->prepare("
                    DELETE FROM Feedback 
                    WHERE service_id IN (SELECT id FROM Service_ WHERE category_id = :category_id)
                ");
                $stmt->execute([':category_id' => $categoryId]);
                
                // Eliminar media dos serviços
                $stmt = $db->prepare("
                    DELETE FROM Media 
                    WHERE service_id IN (SELECT id FROM Service_ WHERE category_id = :category_id)
                ");
                $stmt->execute([':category_id' => $categoryId]);
                
                // Finalmente, eliminar os serviços
                $stmt = $db->prepare("DELETE FROM Service_ WHERE category_id = :category_id");
                $stmt->execute([':category_id' => $categoryId]);
            }
            
            // 2. Eliminar a categoria
            $stmt = $db->prepare("DELETE FROM Category WHERE id = :id");
            $result = $stmt->execute([':id' => $categoryId]);
            
            if (!$result) {
                throw new Exception("Erro ao eliminar categoria da base de dados");
            }
            
            // 3. Eliminar imagem do sistema de ficheiros
            if ($category['path_']) {
                $fullPath = dirname(__DIR__) . '/' . $category['path_'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            // 4. Eliminar entrada da tabela Media
            if ($category['photo_id']) {
                $stmt = $db->prepare("DELETE FROM Media WHERE id = :id");
                $stmt->execute([':id' => $category['photo_id']]);
            }
            
            // Confirmar transação
            $db->commit();
            
            $message = "Categoria '{$category['name_']}' eliminada com sucesso!";
            if ($serviceCount > 0) {
                $message .= " ({$serviceCount} serviço(s) também foram eliminados)";
            }
            
            return ['success' => true, 'message' => $message];
            
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            $db->rollBack();
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao eliminar categoria: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    } catch (Exception $e) {
        error_log("Erro ao eliminar categoria: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao eliminar categoria: ' . $e->getMessage()];
    }
}

/**
 * Faz upload de uma imagem para a pasta de categorias.
 *
 * @param array $file Array do $_FILES['image'].
 * @return array Resultado do upload com path relativo.
 */
function uploadCategoryImage($file) {
    try {
        error_log("=== INÍCIO UPLOAD ===");
        error_log("Ficheiro recebido: " . print_r($file, true));
        
        // Verificar se há erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log("Erro no upload: " . $file['error']);
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
            error_log("Ficheiro muito grande: " . $file['size']);
            return ['success' => false, 'message' => 'Ficheiro muito grande. Máximo 5MB.'];
        }
        
        // Verificar tipo MIME
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            error_log("Tipo não permitido: " . $file['type']);
            return ['success' => false, 'message' => 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.'];
        }
        
        // Verificar se é realmente uma imagem
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            error_log("Não é uma imagem válida");
            return ['success' => false, 'message' => 'Ficheiro não é uma imagem válida.'];
        }
        
        // Criar diretório se não existir
        $uploadDir = dirname(__DIR__) . '/Images/site/categories/';
        error_log("Diretório de upload: " . $uploadDir);
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Erro ao criar diretório: " . $uploadDir);
                return ['success' => false, 'message' => 'Erro ao criar diretório.'];
            }
            error_log("Diretório criado: " . $uploadDir);
        }
        
        // Gerar nome único para o ficheiro
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = 'category_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        $relativePath = 'Images/site/categories/' . $fileName;
        
        error_log("Nome do ficheiro: " . $fileName);
        error_log("Caminho completo: " . $filePath);
        error_log("Caminho relativo: " . $relativePath);
        
        // Mover ficheiro
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            error_log("Erro ao mover ficheiro de " . $file['tmp_name'] . " para " . $filePath);
            return ['success' => false, 'message' => 'Erro ao guardar o ficheiro.'];
        }
        
        error_log("Ficheiro movido com sucesso");
        
        // Redimensionar imagem se necessário (máximo 800x600)
        resizeCategoryImage($filePath, 800, 600);
        
        error_log("=== FIM UPLOAD ===");
        
        return [
            'success' => true,
            'path' => $relativePath,
            'full_path' => $filePath
        ];
        
    } catch (Exception $e) {
        error_log("Erro no upload da imagem: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao processar o ficheiro: ' . $e->getMessage()];
    }
}

/**
 * Redimensiona uma imagem mantendo a proporção.
 *
 * @param string $filePath Caminho do ficheiro.
 * @param int $maxWidth Largura máxima.
 * @param int $maxHeight Altura máxima.
 */
function resizeCategoryImage($filePath, $maxWidth, $maxHeight) {
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
 * Caso o ficheiro seja acedido diretamente (em vez de incluído), 
 * trata a requisição como uma API REST simples (método GET).
 */
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {

    // Garante que o cabeçalho da resposta será JSON
    header('Content-Type: application/json');

    // Apenas permite método GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // Verifica se foi passado um ID específico via query string
        if (isset($_GET['id'])) {
            $category = getCategoryById($_GET['id']);

            if ($category) {
                echo json_encode(['status' => 'success', 'data' => $category]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Categoria não encontrada']);
            }
        } else {
            $categories = getAllCategories();
            echo json_encode(['status' => 'success', 'data' => $categories]);
        }
        exit;
    }

    // Caso o método HTTP não seja GET, devolve erro 405
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}
?>