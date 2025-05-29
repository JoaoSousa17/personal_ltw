<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$idToRemove = $_POST['id'];
$cart = $_SESSION['cart'];

foreach ($cart as $index => $item) {
    if ($item['id'] == $idToRemove) {
        array_splice($cart, $index, 1);
        $_SESSION['cart'] = $cart;
        echo json_encode(['success' => true, 'message' => 'Item removido']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
