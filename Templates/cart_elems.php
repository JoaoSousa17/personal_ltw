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
 */
function drawCartItems($cartItems) { ?>
    <div class="products-containers">
        <?php if (count($cartItems) == 0): ?>
            <div class="carrinho-vazio" style="border: 2px dashed #555; padding: 20px; text-align: center;">
                <img src="../Images/site/staticPages/cart.png" style="width: 100px; height: auto;" />
                <h2>Carrinho vazio</h2>
            </div>
        <?php else: ?>
            <?php foreach ($cartItems as $item):
                $id = md5($item['title'] . $item['price'] . $item['seller']);
            ?>
            <div class="product-item clickable"
     data-id="<?= htmlspecialchars($item['id']) ?>"
     data-link="../Views/product.php?id=<?= urlencode($item['id']) ?>">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Produto" />
                <div class="description">
                    <h2><?= htmlspecialchars($item['title']) ?></h2>
                    <h3>€<?= number_format($item['price'], 2, ',', '') ?></h3>
                    <div class="vendedor-info">
                        <h5>Vendido por </h5>
                        <h5><?= htmlspecialchars($item['seller']) ?></h5>
                    </div>
                </div>
                <button class="remove-btn" data-id="<?= $id ?>">
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
 */
function drawCartSummary($total, $cartItems) { ?>
    <div class="price-container">
        <div class="subtotal">
            <h4>Subtotal</h4>
            <h4>€<?= number_format($total, 2, ',', '') ?></h4>
        </div>

        <div class="custos-deslocação">
            <h4>Custos de Deslocação</h4>
            <h4>€0,<span class="decimais">00</span></h4>
        </div>

        <div class="total">
            <h1>Total</h1>
            <h1 class="Preco-final">€<?= number_format($total, 2, ',', '') ?></h1>
        </div>

        <form method="post" action="/Views/checkout.php">
            <input type="hidden" name="total" value="<?= $total ?>">

            <?php foreach ($cartItems as $item): ?>
                <input type="hidden" name="services[]" value="<?= htmlspecialchars($item['title']) ?>">
            <?php endforeach; ?>

            <button type="submit" id="comprar-btn">Comprar</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
