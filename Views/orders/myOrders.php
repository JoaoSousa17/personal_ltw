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

// Obter pedidos do utilizador (será implementado no serviceController)
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
                        default:
                            $statusClass = 'status-pending';
                            $statusText = 'Pendente';
                    }
                    
                    $orderDate = date('d/m/Y', strtotime($order['date_']));
                    $orderTime = date('H:i', strtotime($order['time_']));
                ?>
                    <div class="order-card">
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
                                    <span class="detail-value"><?php echo htmlspecialchars($order['freelancer_name']); ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Data do Pedido:</span>
                                    <span class="detail-value"><?php echo $orderDate . ' às ' . $orderTime; ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="detail-label">Duração:</span>
                                    <span class="detail-value"><?php echo $order['duration']; ?> minutos</span>
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
                                <button class="btn-feedback" onclick="openFeedbackModal(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-star"></i>
                                    Avaliar Serviço
                                </button>
                            <?php endif; ?>
                            
                            <a href="/Views/messages.php" class="btn-contact">
                                <i class="fas fa-envelope"></i>
                                Contactar Prestador
                            </a>
                            
                            <button class="btn-details" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                <i class="fas fa-info-circle"></i>
                                Ver Detalhes
                            </button>
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
            <form id="feedbackForm">
                <input type="hidden" id="feedbackOrderId" name="order_id">
                
                <div class="form-group">
                    <label>Avaliação:</label>
                    <div class="rating-stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <input type="hidden" id="ratingValue" name="rating" required>
                </div>
                
                <div class="form-group">
                    <label for="feedbackTitle">Título (opcional):</label>
                    <input type="text" id="feedbackTitle" name="title" placeholder="Resumo da sua experiência">
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
function openFeedbackModal(orderId) {
    document.getElementById('feedbackOrderId').value = orderId;
    document.getElementById('feedbackModal').style.display = 'block';
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').style.display = 'none';
    document.getElementById('feedbackForm').reset();
    // Reset rating stars
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
    });
}

function viewOrderDetails(orderId) {
    // Implementar visualização de detalhes do pedido
    alert('Funcionalidade de detalhes do pedido em desenvolvimento');
}

// Gestão das estrelas de avaliação
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingValue');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            
            // Reset all stars
            stars.forEach(s => s.classList.remove('active'));
            
            // Highlight selected stars
            for (let i = 0; i < rating; i++) {
                stars[i].classList.add('active');
            }
        });
        
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            
            // Highlight stars on hover
            stars.forEach((s, index) => {
                if (index < rating) {
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
    
    // Gestão do formulário de feedback
    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Aqui implementaria o envio para o servidor
        // Por agora, apenas simular sucesso
        alert('Avaliação enviada com sucesso!');
        closeFeedbackModal();
    });
});

// Fechar modal clicando fora dele
window.addEventListener('click', function(event) {
    const modal = document.getElementById('feedbackModal');
    if (event.target === modal) {
        closeFeedbackModal();
    }
});
</script>

<!-- Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>