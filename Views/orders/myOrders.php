<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/serviceController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos seus pedidos.';
    header("Location: /Views/auth.php");
    exit();
}

$currentUserId = getCurrentUserId();

// Obter pedidos do utilizador
$orders = getUserOrders($currentUserId);
$stats = getOrderStats($currentUserId);

drawHeader("Handee - Os Meus Pedidos", ["/Styles/MyRequest&Items.css"]);
?>

<main>
    <section class="section-header">
        <h2>Os Meus Pedidos</h2>
        <p>Consulte o histórico e estado dos seus pedidos de serviços</p>
    </section>

    <!-- Estatísticas dos Pedidos -->
    <section class="orders-stats">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total de Pedidos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['completed_orders']; ?></h3>
                    <p>Pedidos Concluídos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['accepted_orders']; ?></h3>
                    <p>Em Progresso</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_spent'], 2); ?>€</h3>
                    <p>Total Gasto</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Lista de Pedidos -->
    <section class="orders-section">
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <div class="no-orders-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3>Ainda não fez nenhum pedido</h3>
                <p>Explore os nossos serviços e encontre o que precisa!</p>
                <a href="/Views/categories.php" class="btn-explore">Explorar Serviços</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): 
                    // Determinar classe CSS baseada no status
                    $statusClass = '';
                    $statusText = '';
                    switch($order['status_']) {
                        case 'completed':
                            $statusClass = 'status-completed';
                            $statusText = 'Concluído';
                            break;
                        case 'accepted':
                            $statusClass = 'status-accepted';
                            $statusText = 'Aceite';
                            break;
                        case 'paid':
                            $statusClass = 'status-paid';
                            $statusText = 'Pago';
                            break;
                        case 'pending':
                            $statusClass = 'status-pending';
                            $statusText = 'Pendente';
                            break;
                        default:
                            $statusClass = 'status-unknown';
                            $statusText = 'Estado Desconhecido';
                    }
                    
                    $orderDate = date('d/m/Y', strtotime($order['date_']));
                    $orderTime = date('H:i', strtotime($order['time_']));
                ?>
                    <div class="order-card" data-order-id="<?php echo $order['order_id']; ?>">
                        <div class="order-header">
                            <div class="order-info">
                                <h3 class="service-name"><?php echo htmlspecialchars($order['service_name']); ?></h3>
                                <p class="category-name"><?php echo htmlspecialchars($order['category_name']); ?></p>
                            </div>
                            <div class="order-status">
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-body">
                            <div class="order-details">
                                <div class="detail-item">
                                    <span class="detail-label">Prestador:</span>
                                    <span class="detail-value freelancer-name"><?php echo htmlspecialchars($order['freelancer_name']); ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Data do Pedido:</span>
                                    <span class="detail-value order-datetime"><?php echo $orderDate . ' às ' . $orderTime; ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Duração:</span>
                                    <span class="detail-value order-duration"><?php echo $order['duration']; ?> minutos</span>
                                </div>
                                
                                <?php if ($order['travel_fee'] > 0): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Taxa de Deslocação:</span>
                                    <span class="detail-value"><?php echo number_format($order['travel_fee'], 2); ?>€</span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Preço Final:</span>
                                    <span class="detail-value price-highlight"><?php echo number_format($order['final_price'], 2); ?>€</span>
                                </div>
                            </div>
                            
                            <div class="service-description">
                                <h4>Descrição do Serviço:</h4>
                                <p><?php echo htmlspecialchars($order['description_']); ?></p>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <?php if ($order['status_'] === 'completed'): ?>
                                <button class="btn-feedback" onclick="openFeedbackModal(<?php echo $order['service_id']; ?>, '<?php echo htmlspecialchars($order['service_name']); ?>')">
                                    <i class="fas fa-star"></i>
                                    Avaliar Serviço
                                </button>
                            <?php endif; ?>
                            
                            <a href="/Views/messages.php?freelancer=<?php echo $order['freelancer_username']; ?>" class="btn-contact">
                                <i class="fas fa-envelope"></i>
                                Contactar Prestador
                            </a>
                            
                            <?php if ($order['status_'] === 'paid'): ?>
                                <button class="btn-complete" onclick="markAsCompleted(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-check"></i>
                                    Completa
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($order['status_'] === 'accepted'): ?>
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-cart-plus"></i>
                                    Adicionar ao Carrinho
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Modal para Feedback (Avaliação) -->
<div id="feedbackModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Avaliar Serviço</h3>
            <span class="modal-close" onclick="closeFeedbackModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="feedbackForm" action="/Controllers/feedbackController.php" method="POST">
                <input type="hidden" name="action" value="create_feedback">
                <input type="hidden" id="feedbackServiceId" name="service_id">
                
                <div class="form-group">
                    <label>Serviço:</label>
                    <p id="serviceName" class="service-name-display"></p>
                </div>
                
                <div class="form-group">
                    <label for="feedbackTitle">Título:</label>
                    <input type="text" id="feedbackTitle" name="title" required placeholder="Título da sua avaliação">
                </div>
                
                <div class="form-group">
                    <label>Avaliação:</label>
                    <div class="rating-stars">
                        <span class="star" data-rating="0.5">☆</span>
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="1.5">☆</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="2.5">☆</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="3.5">☆</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="4.5">☆</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <input type="hidden" id="ratingValue" name="evaluation" required>
                    <p class="rating-text">Clique nas estrelas para avaliar</p>
                </div>
                
                <div class="form-group">
                    <label for="feedbackDescription">Comentário (opcional):</label>
                    <textarea id="feedbackDescription" name="description" rows="4" placeholder="Partilhe a sua experiência com este serviço..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeFeedbackModal()">Cancelar</button>
                    <button type="submit" class="btn-submit">Enviar Avaliação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script para gestão de avaliações
function openFeedbackModal(serviceId, serviceName) {
    document.getElementById('feedbackServiceId').value = serviceId;
    document.getElementById('serviceName').textContent = serviceName;
    document.getElementById('feedbackModal').style.display = 'block';
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').style.display = 'none';
    document.getElementById('feedbackForm').reset();
    // Reset rating stars
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
    });
    document.getElementById('ratingValue').value = '';
}

function viewOrderDetails(orderId) {
    // Implementar visualização de detalhes do pedido
    alert('Funcionalidade de detalhes do pedido em desenvolvimento. Order ID: ' + orderId);
}

// Gestão das estrelas de avaliação
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingValue');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseFloat(this.getAttribute('data-rating'));
            ratingInput.value = rating;
            
            // Reset all stars
            stars.forEach(s => s.classList.remove('active'));
            
            // Highlight selected stars
            stars.forEach(s => {
                if (parseFloat(s.getAttribute('data-rating')) <= rating) {
                    s.classList.add('active');
                }
            });
            
            // Update rating text
            document.querySelector('.rating-text').textContent = `Avaliação: ${rating} estrela${rating !== 1 ? 's' : ''}`;
        });
        
        star.addEventListener('mouseover', function() {
            const rating = parseFloat(this.getAttribute('data-rating'));
            
            // Highlight stars on hover
            stars.forEach(s => {
                if (parseFloat(s.getAttribute('data-rating')) <= rating) {
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
        });
    });
    
    document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
        stars.forEach(s => s.classList.remove('hover'));
    });
});

// Fechar modal clicando fora dele
window.addEventListener('click', function(event) {
    const modal = document.getElementById('feedbackModal');
    if (event.target === modal) {
        closeFeedbackModal();
    }
});

// Função para adicionar ao carrinho usando o serviceController
function addToCart(orderId) {
    // Criar FormData para enviar via POST
    const formData = new FormData();
    formData.append('action', 'get_order_for_cart');
    formData.append('order_id', orderId);
    
    // Mostrar loading no botão
    const button = document.querySelector(`button[onclick="addToCart(${orderId})"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
    button.disabled = true;
    
    // Enviar requisição para o serviceController
    fetch('/Controllers/serviceController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Pedido adicionado ao carrinho com sucesso!');
            // Opcional: atualizar contador do carrinho na interface
            updateCartCounter(result.total);
            
            // Remover botão de adicionar ao carrinho após sucesso
            button.style.display = 'none';
        } else {
            alert('Erro ao adicionar ao carrinho: ' + (result.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro ao adicionar ao carrinho:', error);
        alert('Erro inesperado ao adicionar ao carrinho.');
    })
    .finally(() => {
        // Restaurar botão se não foi escondido
        if (button.style.display !== 'none') {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

// Função para atualizar contador do carrinho (opcional)
function updateCartCounter(total) {
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        cartCounter.textContent = total;
        cartCounter.style.display = total > 0 ? 'block' : 'none';
    }
}

// Função para marcar pedido como completo
function markAsCompleted(orderId) {
    if (!confirm('Tem certeza que deseja marcar este pedido como completo?')) {
        return;
    }
    
    // Criar FormData para enviar via POST
    const formData = new FormData();
    formData.append('action', 'mark_completed');
    formData.append('order_id', orderId);
    
    // Mostrar loading no botão
    const button = document.querySelector(`button[onclick="markAsCompleted(${orderId})"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    button.disabled = true;
    
    // Enviar requisição para o serviceController
    fetch('/Controllers/serviceController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Pedido marcado como completo com sucesso!');
            // Recarregar a página para atualizar o status
            window.location.reload();
        } else {
            alert('Erro ao marcar pedido como completo: ' + (result.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro ao marcar pedido como completo:', error);
        alert('Erro inesperado ao processar pedido.');
    })
    .finally(() => {
        // Restaurar botão
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>

<!-- Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>