<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Templates/cart_elems.php");
require_once("../Controllers/distancesCalculationController.php");

drawHeader("O meu carrinho", ["../Styles/Cart&Checkout.css"]);

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];

// Obter informações da moeda do utilizador
$currencyInfo = getUserCurrencyInfo();
$total = 0;
$processedItems = [];

// Log para debug
error_log("Processando " . count($cartItems) . " itens no carrinho");

// Processar cada item do carrinho
foreach ($cartItems as $index => $item) {
    try {
        // Garantir que o item tem os campos necessários
        $processedItem = [
            'id' => $item['id'] ?? 'unknown_' . $index,
            'title' => $item['title'] ?? 'Produto sem nome',
            'seller' => $item['seller'] ?? 'Vendedor desconhecido',
            'image' => $item['image'] ?? '/Images/site/staticPages/placeholder.jpg',
            'type' => $item['type'] ?? 'service'
        ];
        
        // Usar o preço tal como está salvo (já deve estar correto)
        $price = floatval($item['price'] ?? 0);
        $processedItem['price_converted'] = $price;
        $processedItem['currency_symbol'] = $item['currency_symbol'] ?? $currencyInfo['symbol'];
        
        // Log para debug
        error_log("Item " . $index . ": " . $item['title'] . " - Preço: " . $price);
        
        // Adicionar campos específicos para pedidos
        if ($processedItem['type'] === 'order') {
            $processedItem['date_'] = $item['date_'] ?? '';
            $processedItem['time_'] = $item['time_'] ?? '';
            $processedItem['order_id'] = $item['order_id'] ?? '';
        }
        
        $processedItems[] = $processedItem;
        $total += $price;
        
    } catch (Exception $e) {
        error_log("Erro ao processar item do carrinho: " . $e->getMessage());
        // Pular item com erro
        continue;
    }
}

// Debug para verificar se os preços estão corretos
error_log("Total calculado no cart.php: " . $total);
error_log("Número de itens processados: " . count($processedItems));
?>

<div class="page-container">
    <?php drawCartHeader(count($processedItems)); ?>

    <div class="content-row">
        <?php drawCartItems($processedItems, $currencyInfo); ?>
        <?php drawCartSummary($total, $processedItems, $currencyInfo); ?>
    </div>
</div>

<script src="../Scripts/cart_scripts.js"></script>

<?php drawFooter(); ?>
