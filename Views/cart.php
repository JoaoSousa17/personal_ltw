<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/cart_elems.php");

drawHeader("O meu carrinho", ["../Styles/cart.css"], ["../Scripts/cart_scripts.js"]);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
$total = 0;
foreach ($cartItems as $item) {
    $total += floatval($item['price']);
}
?>

<div class="page-container">
    <?php drawCartHeader(count($cartItems)); ?>

    <div class="content-row">
        <?php drawCartItems($cartItems); ?>
        <?php drawCartSummary($total, $cartItems); ?>
    </div>
</div>

<script src="../Scripts/cart_scripts.js"></script>

<?php drawFooter(); ?>
