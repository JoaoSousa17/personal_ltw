<?php
// Verificar autenticação e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/orderPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/ordersController.php");
require_once(dirname(__FILE__)."/../../Controllers/messageController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos pedidos.';
    header("Location: /Views/auth.php");
    exit();
}

// Obter dados do utilizador
$currentUserId = getCurrentUserId();

// Processar submissão do formulário para atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['new_status'];

    // Validar o status recebido
    $validStatuses = ['pending', 'accepted', 'refused'];
    if (!in_array($newStatus, $validStatuses)) {
        $_SESSION['error'] = 'Status inválido.';
    } else {
        if ($newStatus === 'refused') {
            // Obter o cliente e enviar mensagem
            $receiverId = getClientIdFromRequest($orderId);

            if ($receiverId) {
                $body = "O seu pedido foi recusado";
                sendMessage($currentUserId, $receiverId, $body);
            }

            $deleted = deleteOrderById($orderId, $currentUserId);
            if ($deleted) {
                $_SESSION['success'] = "Pedido eliminado com sucesso.";
            } else {
                $_SESSION['error'] = "Erro ao eliminar o pedido ou não tem permissão.";
            }
        } else {
            // Atualizar status do pedido
            $updated = updateOrderStatus($orderId, $newStatus, $currentUserId);
            if ($updated) {
                // Obter o cliente e enviar mensagem
                $receiverId = getClientIdFromRequest($orderId);
                
                if ($receiverId) {
                    $statusText = [
                        'pending' => 'Pendente',
                        'accepted' => 'Aceite',
                        'refused' => 'Recusado'
                    ];
                    $body = "O estado do seu pedido foi atualizado para: " . ($statusText[$newStatus] ?? ucfirst($newStatus));
                    sendMessage($currentUserId, $receiverId, $body);
                }

                $_SESSION['success'] = "Status do pedido atualizado para " . ucfirst($newStatus) . ".";
            } else {
                $_SESSION['error'] = "Erro ao atualizar o status do pedido ou não tem permissão.";
            }
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obter pedidos dos serviços do utilizador
$orders = getOrdersForUserServices($currentUserId);

drawHeader("Handee - Pedidos dos Meus Serviços", ["/Styles/MyRequest&Items.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2>Pedidos dos Meus Serviços</h2>
        <p>Veja os pedidos que recebeu de outros utilizadores</p>
    </section>

    <!-- Lista de pedidos ou estado vazio -->
    <?php if (empty($orders)): ?>
        <?php drawNoServiceOrdersState(); ?>
    <?php else: ?>
        <section class="services-section">
            <?php drawServiceOrdersList($orders); ?>
        </section>
    <?php endif; ?>
</main>

<?php drawFooter(); ?>
