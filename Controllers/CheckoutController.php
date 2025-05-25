<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}

// Validação
if (
    empty($_POST['name']) ||
    empty($_POST['email']) ||
    empty($_POST['terms']) ||
    !isset($_POST['total_price'], $_POST['amount_paid'])
) {
    $_SESSION['error'] = "Preencha todos os campos obrigatórios.";
    header("Location: ../Views/checkout.php");
    exit;
}

// Recolher dados
$order = [
    'name'         => $_POST['name'],
    'email'        => $_POST['email'],
    'phone'        => $_POST['phone'] ?? '',
    'address'      => $_POST['address'] ?? '',
    'services'     => $_POST['services'] ?? [],
    'total_price'  => floatval($_POST['total_price']),
    'amount_paid'  => floatval($_POST['amount_paid']),
    'notes'        => $_POST['notes'] ?? '',
    'created_at'   => date('Y-m-d H:i:s')
];

// Limpa o carrinho
unset($_SESSION['cart']);

// Redireciona com sucesso
$_SESSION['success'] = "Encomenda recebida com sucesso!";
header("Location: ../Views/mainPage.php");
exit;
