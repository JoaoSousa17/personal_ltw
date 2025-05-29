<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/checkout_elems.php");
require_once("../Controllers/distancesCalculationController.php");

drawHeader("Finalizar compra", ["../Styles/Cart&Checkout.css"]);

if (!isset($_POST['total'])) {
    echo "<p>Erro: total em falta.</p>";
    drawFooter();
    exit;
}

$services = $_POST['services'] ?? [];
$total = floatval($_POST['total']);
$currencyCode = $_POST['currency_code'] ?? 'eur';
$currencySymbol = $_POST['currency_symbol'] ?? '€';

// Se não há informação de moeda, obter do utilizador atual
if (!isset($_POST['currency_code'])) {
    $currencyInfo = getUserCurrencyInfo();
    $currencyCode = $currencyInfo['code'];
    $currencySymbol = $currencyInfo['symbol'];
    
    // Converter o total se necessário (caso venha em EUR)
    if ($currencyCode !== 'eur') {
        $total = convertCurrency($total, $currencyCode);
    }
}

$amountToPay = $total / 2;
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

    <input type="hidden" name="total_price" value="<?= $total ?>" />
    <input type="hidden" name="amount_paid" value="<?= $amountToPay ?>" />
    <input type="hidden" name="currency_code" value="<?= $currencyCode ?>" />
    <input type="hidden" name="currency_symbol" value="<?= $currencySymbol ?>" />

    <?php foreach ($services as $s): ?>
      <input type="hidden" name="services[]" value="<?= htmlspecialchars($s) ?>">
    <?php endforeach; ?>

    <button type="submit">Pagar <?= $currencySymbol ?><?= number_format($amountToPay, 2, ',', '') ?></button>
  </form>
</div>

<script src="/Scripts/Cart&Checkout.js"></script>

<?php drawFooter(); ?>
