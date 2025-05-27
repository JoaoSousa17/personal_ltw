<?php
require_once(dirname(__FILE__) . '/../Models/Category.php');

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
        return null;
    }
}

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
 * Adiciona uma nova categoria com upload de imagem.
 * 
 * @param string $name Nome da categoria
 * @param array $imageFile Array do $_FILES para a imagem
 * @return bool True se adicionado com sucesso, False caso contrário
 */
function addCategory($name, $imageFile) {
    try {
        $db = getDatabaseConnection();
        
        // Validar nome da categoria
        if (empty(trim($name))) {
            return false;
        }
        
        // Verificar se categoria já existe
        $checkStmt = $db->prepare("SELECT id FROM Category WHERE name_ = :name");
        $checkStmt->execute([':name' => trim($name)]);
        if ($checkStmt->fetch()) {
            return false; // Categoria já existe
        }
        
        // Processar upload da imagem
        $uploadResult = processCategoryImageUpload($imageFile);
        if (!$uploadResult['success']) {
            return false;
        }
        
        // Inserir na tabela Media
        $mediaStmt = $db->prepare("INSERT INTO Media (service_id, path_, title) VALUES (NULL, :path, :title)");
        $mediaResult = $mediaStmt->execute([
            ':path' => $uploadResult['path'],
            ':title' => 'Imagem da categoria: ' . trim($name)
        ]);
        
        if (!$mediaResult) {
            // Eliminar ficheiro se a inserção na BD falhou
            if (file_exists(dirname(__DIR__) . '/' . $uploadResult['path'])) {
                unlink(dirname(__DIR__) . '/' . $uploadResult['path']);
            }
            return false;
        }
        
        $mediaId = $db->lastInsertId();
        
        // Inserir na tabela Category
        $categoryStmt = $db->prepare("INSERT INTO Category (name_, photo_id) VALUES (:name, :photo_id)");
        $categoryResult = $categoryStmt->execute([
            ':name' => trim($name),
            ':photo_id' => $mediaId
        ]);
        
        if (!$categoryResult) {
            // Reverter: eliminar da tabela Media e ficheiro
            $db->prepare("DELETE FROM Media WHERE id = :id")->execute([':id' => $mediaId]);
            if (file_exists(dirname(__DIR__) . '/' . $uploadResult['path'])) {
                unlink(dirname(__DIR__) . '/' . $uploadResult['path']);
            }
            return false;
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar categoria: " . $e->getMessage());
        return false;
    }
}

/**
 * Processa o upload da imagem da categoria.
 * 
 * @param array $file Array do $_FILES
 * @return array Resultado do processamento
 */
function processCategoryImageUpload($file) {
    try {
        // Validar erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erro no upload do ficheiro.'];
        }
        
        // Validar tamanho (máximo 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ficheiro muito grande. Máximo 5MB.'];
        }
        
        // Validar tipo MIME
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.'];
        }
        
        // Verificar se é realmente uma imagem
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'Ficheiro não é uma imagem válida.'];
        }
        
        // Criar diretório se não existir
        $uploadDir = dirname(__DIR__) . '/Images/site/categories/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Erro ao criar diretório.'];
            }
        }
        
        // Gerar nome único para o ficheiro
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = 'category_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        $relativePath = '/Images/site/categories/' . $fileName;
        
        // Mover ficheiro
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Erro ao guardar o ficheiro.'];
        }
        
        // Redimensionar imagem se necessário (opcional)
        resizeCategoryImage($filePath, 500, 500);
        
        return [
            'success' => true,
            'path' => $relativePath,
            'full_path' => $filePath
        ];
        
    } catch (Exception $e) {
        error_log("Erro no upload da imagem da categoria: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno no processamento da imagem.'];
    }
}

/**
 * Redimensiona uma imagem da categoria mantendo a proporção.
 * 
 * @param string $filePath Caminho do ficheiro
 * @param int $maxWidth Largura máxima
 * @param int $maxHeight Altura máxima
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
        error_log("Erro ao redimensionar imagem da categoria: " . $e->getMessage());
    }
}

/**
 * Conta o número de serviços por categoria.
 * 
 * @param int $categoryId ID da categoria
 * @return int Número de serviços
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