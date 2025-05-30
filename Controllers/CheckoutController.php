<?php
session_start();
require_once(dirname(__FILE__) . '/../Controllers/userController.php');
require_once(dirname(__FILE__) . '/../Controllers/serviceController.php');
require_once(dirname(__FILE__) . '/../Controllers/distancesCalculationController.php');
require_once(dirname(__FILE__) . '/../Controllers/orderProcessingController.php');
require_once(dirname(__FILE__) . '/../Utils/session.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}

// Validação dos campos obrigatórios
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

// Verificar se o utilizador está logado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = "Deve fazer login para finalizar a compra.";
    header("Location: ../Views/auth.php");
    exit;
}

$userId = getCurrentUserId();

// Verificar se existe carrinho
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = "Carrinho vazio. Adicione itens antes de finalizar a compra.";
    header("Location: ../Views/cart.php");
    exit;
}

// Recolher dados do formulário
$customerData = [
    'name' => trim($_POST['name']),
    'email' => trim($_POST['email']),
    'phone' => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'total_price' => floatval($_POST['total_price']),
    'amount_paid' => floatval($_POST['amount_paid']),
    'currency_code' => $_POST['currency_code'] ?? 'eur',
    'currency_symbol' => $_POST['currency_symbol'] ?? '€',
    'notes' => trim($_POST['notes'] ?? '')
];

// Processar checkout usando o controller dedicado
$result = processCheckout($userId, $_SESSION['cart'], $customerData);

if ($result['success']) {
    // Limpar o carrinho
    unset($_SESSION['cart']);
    
    // Guardar dados para a página de sucesso
    $_SESSION['checkout_success'] = $result['data'];
    $_SESSION['success'] = $result['message'];
    
    header("Location: ../Views/checkout_success.php");
    exit;
} else {
    $_SESSION['error'] = $result['message'];
    header("Location: ../Views/checkout.php");
    exit;
}
?>
