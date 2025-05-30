<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/checkout_elems.php");
require_once("../Controllers/distancesCalculationController.php");
require_once("../Utils/session.php");

// Verificar se o utilizador está logado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = "Deve fazer login para finalizar a compra.";
    header("Location: auth.php");
    exit;
}

// Verificar se há itens no carrinho
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = "Carrinho vazio. Adicione itens antes de finalizar a compra.";
    header("Location: cart.php");
    exit;
}

$cartItems = $_SESSION['cart'];
$currencyInfo = getUserCurrencyInfo();

// Calcular total do carrinho
$total = 0;
$services = [];
foreach ($cartItems as $item) {
    $total += floatval($item['price']);
    $services[] = $item['title'];
}

// Se vier de POST (do formulário do carrinho), usar esses dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['total'])) {
    $total = floatval($_POST['total']);
    $currencyCode = $_POST['currency_code'] ?? $currencyInfo['code'];
    $currencySymbol = $_POST['currency_symbol'] ?? $currencyInfo['symbol'];
    $services = $_POST['services'] ?? $services;
} else {
    // Usar dados do carrinho direto
    $currencyCode = $currencyInfo['code'];
    $currencySymbol = $currencyInfo['symbol'];
}

$amountToPay = $total / 2;

drawHeader("Finalizar compra", ["../Styles/Cart&Checkout.css"]);
?>

<div class="page-container">
  <h1>Finalizar Encomenda</h1>

  <form id="checkoutForm" method="post" action="../Controllers/CheckoutController.php" class="checkout-form">
    <ul class="form-list">
      <?php drawContactFields(); ?>

      <?php if (count($services) > 1): ?>
        <div>
          <label>
            <input type="checkbox" id="differentAddresses" />
            As moradas são diferentes para cada serviço?
          </label>
        </div>
      <?php endif; ?>

      <?php drawCommonAddress(); ?>

      <?php if (!empty($services)) drawServiceFields($services); ?>

      <li>
        <label>
          <input type="checkbox" name="terms" required />
          Aceito os <a href="staticPages/terms.php" target="_blank">termos e condições</a>
        </label>
      </li>
    </ul>

    <?php drawPaymentSummary($total, $amountToPay, $currencySymbol); ?>

    <!-- Campos ocultos com dados do checkout -->
    <input type="hidden" name="total_price" value="<?= number_format($total, 2, '.', '') ?>" />
    <input type="hidden" name="amount_paid" value="<?= number_format($amountToPay, 2, '.', '') ?>" />
    <input type="hidden" name="currency_code" value="<?= htmlspecialchars($currencyCode) ?>" />
    <input type="hidden" name="currency_symbol" value="<?= htmlspecialchars($currencySymbol) ?>" />

    <!-- Serializar dados do carrinho para processamento -->
    <input type="hidden" name="cart_data" value="<?= htmlspecialchars(json_encode($cartItems)) ?>" />

    <?php foreach ($services as $s): ?>
      <input type="hidden" name="services[]" value="<?= htmlspecialchars($s) ?>">
    <?php endforeach; ?>

    <button type="submit">Pagar <?= $currencySymbol ?><?= number_format($amountToPay, 2, ',', '') ?></button>
  </form>
</div>

<script src="/Scripts/checkout.js"></script>

<?php drawFooter(); ?>
