<?php
require_once(dirname(__FILE__)."/serviceController.php");

// Buscar sugestões para o autocompletar da barra de pesquisa
function getSuggestions($keyword) {
    if (empty($keyword) || strlen($keyword) < 2) {
        return [];
    }

    return getServiceSuggestions($keyword);
}

// Executar a pesquisa completa
function search($keyword, $category_id = null, $min_price = null, $max_price = null) {
    // Se não houver palavra-chave, retornar todos os serviços ou filtrados por outros parâmetros
    if (empty($keyword)) {
        if ($category_id || $min_price !== null || $max_price !== null) {
            return filterServices($category_id, $min_price, $max_price, null);
        }
        return getAllServices();
    }

    // Se houver palavra-chave e outros filtros
    if ($category_id || $min_price !== null || $max_price !== null) {
        return filterServices($category_id, $min_price, $max_price, $keyword);
    }

    // Se houver apenas palavra-chave
    return searchServices($keyword);
}

// Processar requisição AJAX para sugestões
function handleSuggestionsRequest() {
    header('Content-Type: application/json');
    
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $keyword = htmlspecialchars(strip_tags($_GET['query']));
        $suggestions = getSuggestions($keyword);
        echo json_encode($suggestions);
    } else {
        echo json_encode([]);
    }
}

// Processar requisição AJAX para pesquisa completa (opcional)
function handleSearchRequest() {
    header('Content-Type: application/json');
    
    $keyword = isset($_GET['query']) ? htmlspecialchars(strip_tags($_GET['query'])) : '';
    $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
    
    $results = search($keyword, $category_id, $min_price, $max_price);
    echo json_encode($results);
}

// Verificar se é uma requisição AJAX para sugestões
if (isset($_GET['action']) && $_GET['action'] == 'suggestions') {
    handleSuggestionsRequest();
    exit;
}

// Verificar se é uma requisição AJAX para pesquisa completa
if (isset($_GET['action']) && $_GET['action'] == 'search') {
    handleSearchRequest();
    exit;
}
?>