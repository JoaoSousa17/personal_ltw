<?php
// Verificar autenticação e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/orderPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/serviceController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos seus pedidos.';
    header("Location: /Views/auth.php");
    exit();
}

// Obter dados do utilizador
$currentUserId = getCurrentUserId();
$orders = getUserOrders($currentUserId);
$stats = getOrderStats($currentUserId);

drawHeader("Handee - Os Meus Pedidos", ["/Styles/MyRequest&Items.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2>Os Meus Pedidos</h2>
        <p>Consulte o histórico e estado dos seus pedidos de serviços</p>
    </section>

    <!-- Estatísticas dos pedidos -->
    <?php drawOrdersStats($stats); ?>

    <!-- Lista de pedidos -->
    <section class="orders-section">
        <?php if (empty($orders)): ?>
            <?php drawNoOrdersState(); ?>
        <?php else: ?>
            <?php drawOrdersList($orders); ?>
        <?php endif; ?>
    </section>
</main>

<!-- Modal de feedback -->
<?php drawFeedbackModal(); ?>

<!-- Scripts necessários -->
<script src="/Scripts/orders.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
