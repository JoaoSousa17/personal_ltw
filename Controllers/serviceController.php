<?php
require_once(dirname(__FILE__)."/../Models/Service.php");
require_once(dirname(__FILE__)."/../Database/connection.php");

// Obter todos os serviços ativos
function getAllServices() {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ WHERE is_active = 1 ORDER BY name_";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return formatResults($stmt);
}

// Obter serviço por ID
function getServiceById($id) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return [
            'id' => $row['id'],
            'freelancer_id' => $row['freelancer_id'],
            'name' => $row['name_'],
            'description' => $row['description_'],
            'duration' => $row['duration'],
            'is_active' => $row['is_active'],
            'price_per_hour' => $row['price_per_hour'],
            'promotion' => $row['promotion'],
            'category_id' => $row['category_id']
        ];
    }
    return null;
}

// Pesquisar serviços por termo
function searchServices($keyword) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ 
              WHERE is_active = 1 AND 
              (name_ LIKE ? OR description_ LIKE ?) 
              ORDER BY name_";
    
    $keyword = "%{$keyword}%";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $keyword);
    $stmt->bindParam(2, $keyword);
    $stmt->execute();

    return formatResults($stmt);
}

// Buscar sugestões de serviços baseado no termo
function getServiceSuggestions($keyword) {
    $db = getDatabaseConnection();
    $query = "SELECT id, name_ FROM Service_ 
              WHERE is_active = 1 AND name_ LIKE ? 
              ORDER BY name_ LIMIT 5";
    
    $keyword = "%{$keyword}%";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $keyword);
    $stmt->execute();

    return formatSuggestions($stmt);
}

// Obter serviços por categoria
function getServicesByCategory($category_id) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ 
              WHERE is_active = 1 AND category_id = ? 
              ORDER BY name_";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $category_id);
    $stmt->execute();

    return formatResults($stmt);
}

// Filtrar serviços com várias condições
function filterServices($category_id = null, $min_price = null, $max_price = null, $keyword = null) {
    $db = getDatabaseConnection();
    $conditions = ["is_active = 1"];
    $params = [];
    
    if ($category_id) {
        $conditions[] = "category_id = ?";
        $params[] = $category_id;
    }
    
    if ($min_price !== null) {
        $conditions[] = "price_per_hour >= ?";
        $params[] = $min_price;
    }
    
    if ($max_price !== null) {
        $conditions[] = "price_per_hour <= ?";
        $params[] = $max_price;
    }
    
    if ($keyword) {
        $conditions[] = "(name_ LIKE ? OR description_ LIKE ?)";
        $keyword = "%{$keyword}%";
        $params[] = $keyword;
        $params[] = $keyword;
    }
    
    $query = "SELECT * FROM Service_ WHERE " . implode(" AND ", $conditions) . " ORDER BY name_";
    $stmt = $db->prepare($query);
    
    for ($i = 0; $i < count($params); $i++) {
        $stmt->bindParam($i + 1, $params[$i]);
    }
    
    $stmt->execute();
    return formatResults($stmt);
}

// Formatar resultados em um array
function formatResults($stmt) {
    $services = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $service_item = [
            'id' => $row['id'],
            'freelancer_id' => $row['freelancer_id'],
            'name' => $row['name_'],
            'description' => $row['description_'],
            'duration' => $row['duration'],
            'is_active' => $row['is_active'],
            'price_per_hour' => $row['price_per_hour'],
            'promotion' => $row['promotion'],
            'category_id' => $row['category_id']
        ];
        $services[] = $service_item;
    }
    return $services;
}

// Formatar sugestões em um array simples
function formatSuggestions($stmt) {
    $suggestions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['name_']
        ];
    }
    return $suggestions;
}

// Calcular preço com desconto
function calculateDiscountedPrice($price, $promotion) {
    if ($promotion > 0) {
        return $price - ($price * ($promotion / 100));
    }
    return $price;
}

// Calcular preço total baseado na duração
function calculateTotalPrice($price_per_hour, $promotion, $duration) {
    $discounted_price = calculateDiscountedPrice($price_per_hour, $promotion);
    return $discounted_price * ($duration / 60); // Convertendo duração de minutos para horas
}
?>
