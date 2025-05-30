<?php
/**
 * Mostra a secção de cabeçalho do carrinho, com título e número de artigos.
 *
 * @param int $itemCount Número de itens no carrinho.
 */
function drawCartHeader($itemCount) { ?>
    <div class="header-row">
        <div class="page-header">
            <h1>O meu carrinho</h1>
            <h3><?= $itemCount ?> artigo<?= $itemCount != 1 ? 's' : '' ?></h3>
        </div>
        <div class="blank"></div>
    </div>
<?php }

/**
 * Mostra os itens no carrinho ou uma mensagem caso esteja vazio.
 *
 * @param array $cartItems Lista dos itens no carrinho.
 * @param array $currencyInfo Informações da moeda do utilizador.
 */
function drawCartItems($cartItems, $currencyInfo = null) { 
    if (!$currencyInfo) {
        $currencyInfo = ['symbol' => '€', 'code' => 'eur'];
    }
    ?>
    <div class="products-containers">
        <?php if (count($cartItems) == 0): ?>
            <div class="carrinho-vazio" style="border: 2px dashed #555; padding: 20px; text-align: center;">
                <img src="../Images/site/staticPages/cart.png" style="width: 100px; height: auto;" />
                <h2>Carrinho vazio</h2>
            </div>
        <?php else: ?>
            <?php foreach ($cartItems as $item):
                // Usar o preço já convertido ou converter se necessário
                $displayPrice = isset($item['price_converted']) ? $item['price_converted'] : 
                               (isset($item['price']) ? floatval($item['price']) : 0);
                
                // Usar o símbolo da moeda do item ou da configuração global
                $currencySymbol = isset($item['currency_symbol']) ? $item['currency_symbol'] : $currencyInfo['symbol'];
                
                // Garantir que temos um ID válido
                $itemId = isset($item['id']) ? htmlspecialchars($item['id']) : 
                         (isset($item['order_id']) ? htmlspecialchars($item['order_id']) : 'unknown');
            ?>
            <div class="product-item clickable"
                 data-id="<?= $itemId ?>"
                 data-price="<?= number_format($displayPrice, 2, '.', '') ?>"
                 data-link="../Views/product.php?id=<?= urlencode($item['id']) ?>">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Produto" />
                <div class="description">
                    <h2><?= htmlspecialchars($item['title']) ?></h2>
                    <h3 class="item-price" data-price="<?= number_format($displayPrice, 2, '.', '') ?>">
                        <?= $currencySymbol ?><?= number_format($displayPrice, 2, ',', '') ?>
                    </h3>
                    <div class="vendedor-info">
                        <h5>Vendido por </h5>
                        <h5><?= htmlspecialchars($item['seller']) ?></h5>
                    </div>
                    <?php if (isset($item['type']) && $item['type'] === 'order'): ?>
                        <div class="order-details">
                            <small>Data: <?= htmlspecialchars($item['date_'] ?? '') ?></small>
                            <small>Hora: <?= htmlspecialchars($item['time_'] ?? '') ?></small>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="remove-btn" data-id="<?= $itemId ?>">
                    <img src="../../Images/site/staticPages/trash.png" alt="Remover" />
                </button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php }

/**
 * Mostra o resumo de preços e o botão de checkout.
 *
 * @param float $total Total da compra.
 * @param array $cartItems Itens no carrinho.
 * @param array $currencyInfo Informações da moeda do utilizador.
 */
function drawCartSummary($total, $cartItems, $currencyInfo = null) { 
    if (!$currencyInfo) {
        $currencyInfo = ['symbol' => '€', 'code' => 'eur'];
    }
    
    // Garantir que o total é um número válido
    $validTotal = is_numeric($total) ? floatval($total) : 0;
    ?>
    <div class="price-container">
        <div class="subtotal">
            <h4>Subtotal</h4>
            <h4 class="subtotal-value"><?= $currencyInfo['symbol'] ?><?= number_format($validTotal, 2, ',', '') ?></h4>
        </div>

        <div class="total">
            <h1>Total</h1>
            <h1 class="Preco-final"><?= $currencyInfo['symbol'] ?><?= number_format($validTotal, 2, ',', '') ?></h1>
        </div>

        <form method="post" action="/Views/checkout.php">
            <input type="hidden" name="total" value="<?= number_format($validTotal, 2, '.', '') ?>">
            <input type="hidden" name="total_price" value="<?= number_format($validTotal, 2, '.', '') ?>">
            <input type="hidden" name="amount_paid" value="<?= number_format($validTotal / 2, 2, '.', '') ?>">
            <input type="hidden" name="currency_code" value="<?= htmlspecialchars($currencyInfo['code']) ?>">
            <input type="hidden" name="currency_symbol" value="<?= htmlspecialchars($currencyInfo['symbol']) ?>">

            <?php foreach ($cartItems as $item): ?>
                <input type="hidden" name="services[]" value="<?= htmlspecialchars($item['title']) ?>">
            <?php endforeach; ?>

            <button type="submit" id="comprar-btn" <?= $validTotal <= 0 ? 'disabled' : '' ?>>
                Comprar
            </button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Tornar os itens clicáveis (exceto o botão de remover)
            document.querySelectorAll(".product-item.clickable").forEach(function (item) {
                item.addEventListener("click", function (e) {
                    if (e.target.closest(".remove-btn")) return;
                    const link = this.getAttribute("data-link");
                    if (link) window.location.href = link;
                });
            });
        });
    </script>
<?php }
?>
