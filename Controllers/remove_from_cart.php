<?php
session_start();

if (!isset($_SESSION['cart']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$id = $_POST['id'];

// Procurar o item no carrinho usando o ID único
foreach ($_SESSION['cart'] as $index => $item) {
    $itemId = md5($item['title'] . $item['price'] . $item['seller']); // Gerar ID único
    if ($itemId === $id) {
        // Remover o item do carrinho
        array_splice($_SESSION['cart'], $index, 1);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
