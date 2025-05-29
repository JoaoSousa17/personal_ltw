<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/ordersController.php");
require_once(dirname(__FILE__)."/../../Controllers/messageController.php");

if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos pedidos.';
    header("Location: /Views/auth.php");
    exit();
}

$currentUserId = getCurrentUserId();

// 1) Tratar submissão do formulário para atualizar o status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['new_status'];

    // Validar o status recebido
    $validStatuses = ['pending', 'accepted', 'refused'];
    if (!in_array($newStatus, $validStatuses)) {
        $_SESSION['error'] = 'Status inválido.';
    } else {
        require_once(dirname(__FILE__)."/../../Controllers/ordersController.php");

        if ($newStatus === 'refused') {
            // Obter o cliente (receiver_id)
            $receiverId = getClientIdFromRequest($orderId);

            if ($receiverId) {
                $body = "O seu pedido foi recusado";
                sendMessage($currentUserId, $receiverId, $body);
            }

            $deleted = deleteOrderById($orderId, $currentUserId); // <- Deve deletar da tabela Request
            if ($deleted) {
                $_SESSION['success'] = "Pedido eliminado com sucesso.";
            } else {
                $_SESSION['error'] = "Erro ao eliminar o pedido ou não tem permissão.";
            }
        } else {
            // Atualizar status do pedido
            $updated = updateOrderStatus($orderId, $newStatus, $currentUserId); // <- Atualiza Request.status_
            if ($updated) {
                // Obter o cliente (receiver_id)
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

// 2) Obter pedidos após qualquer alteração
$orders = getOrdersForUserServices($currentUserId);

drawHeader("Handee - Pedidos dos Meus Serviços", ["/Styles/MyRequest&Items.css"]);
?>

<main>
    <section class="section-header">
        <h2>Pedidos dos Meus Serviços</h2>
        <p>Veja os pedidos que recebeu de outros utilizadores</p>
    </section>

    <?php if (empty($orders)): ?>
        <div class="no-services">
            <div class="no-services-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3>Ainda não recebeu nenhum pedido</h3>
            <p>Assim que os utilizadores solicitarem os seus serviços, verá os pedidos aqui.</p>
        </div>
    <?php else: ?>
        <section class="services-section">
            <div class="services-list">
                <?php foreach ($orders as $order): ?>
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-info">
                                <h3 class="service-name"><?php echo htmlspecialchars($order['service_name']); ?></h3>
                                <p class="category-name">Pedido recebido</p>
                            </div>
                            <div class="service-status">
                                <?php
                                    $statusLabels = [
                                        'pending' => 'Pendente',
                                        'accepted' => 'Aceite',
                                        'refused' => 'Recusado'
                                    ];
                                    $statusKey = strtolower($order['status']);
                                    $statusLabel = isset($statusLabels[$statusKey]) ? $statusLabels[$statusKey] : ucfirst($statusKey);
                                ?>
                                <span class="status-badge status-<?php echo $statusKey; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </div>
                        </div>

                        <div class="service-body">
                            <div class="service-description">
                                <p><strong>Mensagem do cliente:</strong> <?php echo htmlspecialchars($order['client_message']); ?></p>
                            </div>

                            <div class="service-stats">
                                <div class="stat-item">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo htmlspecialchars($order['client_name']); ?></span>
                                </div>

                                <div class="stat-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo date("d/m/Y H:i", strtotime($order['created_at'])); ?></span>
                                </div>

                                <div class="stat-item">
                                    <i class="fas fa-euro-sign"></i>
                                    <span><?php echo number_format($order['price'], 2); ?> €</span>
                                </div>
                            </div>

                            <!-- Formulário para alterar status -->
                            <form method="post" class="status-form">
                               <input type="hidden" name="order_id" value="<?php echo $order['request_id']; ?>">
                                <label for="status-<?php echo $order['request_id']; ?>">Alterar status:</label>
                                <select name="new_status" id="status-<?php echo $order['request_id']; ?>" onchange="this.form.submit()">
                                    <option value="pending" <?php if ($order['status'] === 'pending') echo 'selected'; ?>>Pendente</option>
                                    <option value="accepted" <?php if ($order['status'] === 'accepted') echo 'selected'; ?>>Aceite</option>
                                    <option value="refused">Recusado</option>
                                </select>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php drawFooter(); ?>
