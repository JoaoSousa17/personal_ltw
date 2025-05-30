<?php
session_start();
require_once('../Controllers/distancesCalculationController.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // O preço vem em EUR (preço base) do frontend
    $priceEUR = floatval($_POST['price'] ?? 0);
    $baseCurrency = $_POST['currency'] ?? 'eur';
    
    // Obter informações da moeda do utilizador
    $currencyInfo = getUserCurrencyInfo();
    
    // Converter o preço de EUR para a moeda do utilizador
    $convertedPrice = convertCurrency($priceEUR, $currencyInfo['code']);
    
    // Log para debug
    error_log("Preço recebido (EUR): " . $priceEUR);
    error_log("Moeda do utilizador: " . $currencyInfo['code']);
    error_log("Preço convertido: " . $convertedPrice);
    
    $product = [
        "id" => $_POST['id'] ?? '',
        "title" => $_POST['title'] ?? '',
        "price" => $convertedPrice, // Preço convertido para a moeda do utilizador
        "price_original" => $priceEUR, // Preço original em EUR
        "currency_code" => $currencyInfo['code'],
        "currency_symbol" => $currencyInfo['symbol'],
        "image" => $_POST['image'] ?? '',
        "seller" => $_POST['seller'] ?? '',
        "type" => $_POST['type'] ?? 'service'
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $product;

    echo json_encode([
        "status" => "success", 
        "total" => count($_SESSION['cart']),
        "converted_price" => number_format($convertedPrice, 2, '.', ''),
        "currency_symbol" => $currencyInfo['symbol'],
        "message" => "Item adicionado ao carrinho com sucesso!",
        "debug" => [
            "price_eur" => $priceEUR,
            "converted_price" => $convertedPrice,
            "currency" => $currencyInfo['code']
        ]
    ]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Método não permitido"]);
?>
