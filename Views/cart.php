<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/cart_elems.php");
require_once("../Controllers/distancesCalculationController.php");

drawHeader("O meu carrinho", ["../Styles/cart.css"], ["../Scripts/cart_scripts.js"]);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];

// Aplicar conversão de moeda aos itens do carrinho
$currencyInfo = getUserCurrencyInfo();
$total = 0;
$convertedItems = [];

foreach ($cartItems as $item) {
    $originalPrice = floatval($item['price']);
    $convertedPrice = convertCurrency($originalPrice, $currencyInfo['code']);
    
    $convertedItem = $item;
    $convertedItem['price_original'] = $originalPrice;
    $convertedItem['price_converted'] = $convertedPrice;
    $convertedItem['currency_symbol'] = $currencyInfo['symbol'];
    
    $convertedItems[] = $convertedItem;
    $total += $convertedPrice;
}
?>

<div class="page-container">
    <?php drawCartHeader(count($cartItems)); ?>

    <div class="content-row">
        <?php drawCartItems($convertedItems, $currencyInfo); ?>
        <?php drawCartSummary($total, $convertedItems, $currencyInfo); ?>
    </div>
</div>

<script src="../Scripts/cart_scripts.js"></script>

<?php drawFooter(); ?>
