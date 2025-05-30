<?php
/**
 * Desenha os campos de contacto do utilizador.
 */
function drawContactFields() { ?>
  <li>
    <label>Nome completo:</label>
    <input type="text" name="name" required />
  </li>
  <li>
    <label>Email de contacto:</label>
    <input type="email" name="email" required />
  </li>
  <li>
    <label>Telefone:</label>
    <input type="tel" name="phone" />
  </li>
<?php }

/**
 * Desenha a secção de endereço comum (caso as moradas não sejam diferentes).
 */
function drawCommonAddress() { ?>
  <div id="commonAddress">
    <label>Morada:</label>
    <input type="text" name="address" />
  </div>
<?php }

/**
 * Desenha os campos específicos para cada serviço.
 */
function drawServiceFields($services) {
  foreach ($services as $index => $service): ?>
    <?php if ($index > 0): ?>
      <hr class="service-separator" />
    <?php endif; ?>
    <div class="service-details" id="service-<?= $index ?>">
      <h3>Serviço: <?= htmlspecialchars($service) ?></h3>

      <div class="service-date">
        <label>Data preferida para o serviço:</label>
        <input type="date" name="date_<?= $index ?>" />
      </div>

      <div class="specific-address" id="specificAddress-<?= $index ?>" style="display: none;">
        <label>Morada para este serviço:</label>
        <input type="text" name="address_<?= $index ?>" />
      </div>

      <div class="service-comments">
        <label>Notas adicionais:</label>
        <textarea name="notes<?= $index ?>"></textarea>
      </div>
    </div>
  <?php endforeach;
}

/**
 * Desenha a secção de resumo de pagamento.
 *
 * @param float $total Total da compra (já convertido).
 * @param float $amountToPay Valor a pagar agora (já convertido).
 * @param string $currencySymbol Símbolo da moeda.
 */
function drawPaymentSummary($total, $amountToPay, $currencySymbol = '€') { ?>
  <div class="resumo-pagamento">
    <p><strong>Total:</strong> <?= $currencySymbol ?><?= number_format($amountToPay, 2, ',', '') ?></p>
  </div>
<?php }
