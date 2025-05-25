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
