<?php
session_start();
require_once('../Controllers/distancesCalculationController.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalPrice = floatval($_POST['price'] ?? 0);
    
    // Obter informações da moeda do utilizador
    $currencyInfo = getUserCurrencyInfo();
    $convertedPrice = convertCurrency($originalPrice, $currencyInfo['code']);
    
    $product = [
        "id" => $_POST['id'] ?? '',
        "title" => $_POST['title'] ?? '',
        "price" => $convertedPrice, // Preço convertido
        "price_original" => $originalPrice, // Preço original em EUR
        "currency_code" => $currencyInfo['code'],
        "currency_symbol" => $currencyInfo['symbol'],
        "image" => $_POST['image'] ?? '',
        "seller" => $_POST['seller'] ?? ''
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $product;

    echo json_encode([
        "status" => "success", 
        "total" => count($_SESSION['cart']),
        "converted_price" => $convertedPrice,
        "currency_symbol" => $currencyInfo['symbol']
    ]);
    exit;
}

echo json_encode(["status" => "error"]);