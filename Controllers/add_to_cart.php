<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = [
        "id" => $_POST['id'] ?? '',
        "title" => $_POST['title'] ?? '',
        "price" => floatval($_POST['price'] ?? 0),
        "image" => $_POST['image'] ?? '',
        "seller" => $_POST['seller'] ?? ''
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $product;

    echo json_encode(["status" => "success", "total" => count($_SESSION['cart'])]);
    exit;
}

echo json_encode(["status" => "error"]);