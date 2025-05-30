<?php
session_start();
require_once("../Templates/common_elems.php");
require_once("../Controllers/orderProcessingController.php");
require_once("../Utils/session.php");

// Verificar se existe dados de sucesso do checkout
$checkoutData = getCheckoutSuccessData();

if (!$checkoutData) {
    $_SESSION['error'] = "Nenhuma informação de checkout encontrada.";
    header("Location: mainPage.php");
    exit;
}

$customerData = $checkoutData['customer_data'];
$processedOrders = $checkoutData['processed_orders'];
$totalItems = $checkoutData['total_items'];

drawHeader("Checkout Concluído", ["../Styles/Cart&Checkout.css"]);
?>

<div class="page-container checkout-success">
    <div class="success-header">
        <div class="success-icon">
            <img src="../Images/site/staticPages/success-check.png" alt="Sucesso" style="width: 80px; height: 80px;">
        </div>
        <h1>Encomenda Processada com Sucesso!</h1>
        <p class="success-subtitle">Obrigado pela sua compra, <?php echo htmlspecialchars($customerData['name']); ?>!</p>
    </div>

    <div class="order-summary">
        <h2>Resumo da Encomenda</h2>
        
        <div class="customer-info">
            <h3>Informações do Cliente</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($customerData['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customerData['email']); ?></p>
            <?php if (!empty($customerData['phone'])): ?>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($customerData['phone']); ?></p>
            <?php endif; ?>
        </div>

        <div class="orders-list">
            <h3>Serviços Adquiridos (<?php echo $totalItems; ?>)</h3>
            
            <?php foreach ($processedOrders as $index => $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <h4><?php echo htmlspecialchars($order['service_name']); ?></h4>
                        <p class="order-type">
                            <?php if ($order['type'] === 'accepted_order'): ?>
                                <span class="badge badge-info">Pedido Aceito</span>
                            <?php else: ?>
                                <span class="badge badge-success">Novo Serviço</span>
                            <?php endif; ?>
                        </p>
                        <p class="order-price">
                            <strong>
                                <?php echo $customerData['currency_symbol']; ?>
                                <?php echo number_format($order['price'], 2, ',', ''); ?>
                            </strong>
                        </p>
                        <p class="order-status">
                            <span class="status-paid">✓ Pago</span>
                        </p>
                    </div>
                    <div class="order-meta">
                        <small>
                            Request ID: #<?php echo $order['request_id']; ?> | 
                            Service Data ID: #<?php echo $order['service_data_id']; ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="payment-summary">
            <h3>Total Pago</h3>
            <p class="total-amount">
                <?php echo $customerData['currency_symbol']; ?>
                <?php echo number_format($customerData['amount_paid'], 2, ',', ''); ?>
                <small>(50% do total de <?php echo $customerData['currency_symbol']; ?><?php echo number_format($customerData['total_price'], 2, ',', ''); ?>)</small>
            </p>
        </div>
    </div>

    <div class="next-steps">
        <h3>Próximos Passos</h3>
        <ul>
            <li>Receberá um email de confirmação em breve</li>
            <li>Os prestadores de serviços serão notificados</li>
            <li>Poderá acompanhar o progresso dos seus pedidos na área <a href="orders/myOrders.php">Meus Pedidos</a></li>
            <li>O pagamento restante (50%) será processado após a conclusão dos serviços</li>
        </ul>
    </div>

    <div class="action-buttons">
        <a href="orders/myOrders.php" class="btn btn-primary">Ver Meus Pedidos</a>
        <a href="mainPage.php" class="btn btn-secondary">Voltar ao Início</a>
        <a href="categories.php" class="btn btn-outline">Explorar Mais Serviços</a>
    </div>
</div>

<?php drawFooter(); ?>