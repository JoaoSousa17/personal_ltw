<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/checkout_elems.php");

drawHeader("Finalizar compra", ["../Styles/checkout.css"]);

if (!isset($_POST['total'])) {
    echo "<p>Erro: total em falta.</p>";
    drawFooter();
    exit;
}

$services = $_POST['services'] ?? [];
$total = floatval($_POST['total']);
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

      <?php if (count($services) <= 1): ?>
        <li>
          <label>Notas adicionais:</label>
          <textarea name="notes"></textarea>
        </li>
      <?php endif; ?>

      <li>
        <label>
          <input type="checkbox" name="terms" required />
          Aceito os <a href="staticPages/terms.php" target="_blank">termos e condições</a>
        </label>
      </li>
    </ul>

    <?php drawPaymentSummary($total, $amountToPay); ?>

    <input type="hidden" name="total_price" value="<?= $total ?>" />
    <input type="hidden" name="amount_paid" value="<?= $amountToPay ?>" />

    <?php foreach ($services as $s): ?>
      <input type="hidden" name="services[]" value="<?= htmlspecialchars($s) ?>">
    <?php endforeach; ?>

    <button type="submit">Pagar €<?= number_format($amountToPay, 2, ',', '') ?></button>
  </form>
</div>

<script src="/Scripts/checkout.js"></script>

<?php drawFooter(); ?>
