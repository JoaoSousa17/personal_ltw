<?php
// orderPages_elems.php - Funções para páginas de pedidos e serviços

/**
 * Desenha seção de estatísticas de pedidos
 */
function drawOrdersStats($stats) {
    ?>
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
    <?php
}

/**
 * Desenha seção de estatísticas de serviços
 */
function drawServicesStats($stats) {
    ?>
    <section class="services-stats">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_services']; ?></h3>
                    <p>Total de Serviços</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['active_services']; ?></h3>
                    <p>Serviços Ativos</p>
                </div>
            </div>
            
            <a class="stat-card" href="/Views/orders/serviceOrders.php">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total de Pedidos</p>
                </div>
            </a>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_earned'], 2); ?>€</h3>
                    <p>Total Ganho</p>
                </div>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Desenha estado vazio para pedidos
 */
function drawNoOrdersState() {
    ?>
    <div class="no-orders">
        <div class="no-orders-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <h3>Ainda não fez nenhum pedido</h3>
        <p>Explore os nossos serviços e encontre o que precisa!</p>
        <a href="/Views/categories.php" class="btn-explore">Explorar Serviços</a>
    </div>
    <?php
}

/**
 * Desenha estado vazio para serviços
 */
function drawNoServicesState() {
    ?>
    <div class="no-services">
        <div class="no-services-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <h3>Ainda não tem serviços publicados</h3>
        <p>Comece a ganhar dinheiro publicando os seus serviços!</p>
        <button class="btn-explore" onclick="openAddServiceModal()">
            <i class="fas fa-plus"></i>
            Publicar Primeiro Serviço
        </button>
    </div>
    <?php
}

/**
 * Desenha lista de pedidos
 */
function drawOrdersList($orders) {
    ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): 
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
                        <button class="btn-feedback" onclick="openFeedbackModal(<?php echo $order['order_id']; ?>)">
                            <i class="fas fa-star"></i>
                            Avaliar Serviço
                        </button>
                    <?php endif; ?>
                    
                    <a href="/Views/messages.php" class="btn-contact">
                        <i class="fas fa-envelope"></i>
                        Contactar Prestador
                    </a>
                    
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
    <?php
}

/**
 * Desenha lista de serviços
 */
function drawServicesList($services) {
    ?>
    <div class="services-list">
        <?php foreach ($services as $service): 
            $isActive = $service['is_active'];
            $activeClass = $isActive ? 'service-active' : 'service-inactive';
            $discountedPrice = calculateDiscountedPrice($service['price_per_hour'], $service['promotion']);
            $avgRating = $service['avg_rating'] ? round($service['avg_rating'], 1) : 0;
        ?>
            <div class="service-card <?php echo $activeClass; ?>">
                <div class="service-header">
                    <div class="service-info">
                        <h3 class="service-name"><?php echo htmlspecialchars($service['name_']); ?></h3>
                        <p class="category-name"><?php echo htmlspecialchars($service['category_name']); ?></p>
                    </div>
                    <div class="service-status">
                        <span class="status-badge <?php echo $isActive ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $isActive ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="service-body">
                    <div class="service-description">
                        <p><?php echo htmlspecialchars($service['description_']); ?></p>
                    </div>
                    
                    <div class="service-pricing">
                        <div class="price-info">
                            <?php if ($service['promotion'] > 0): ?>
                                <span class="original-price">€<?php echo number_format($service['price_per_hour'], 2); ?>/h</span>
                                <span class="discounted-price">€<?php echo number_format($discountedPrice, 2); ?>/h</span>
                                <span class="discount-badge"><?php echo $service['promotion']; ?>% OFF</span>
                            <?php else: ?>
                                <span class="current-price">€<?php echo number_format($service['price_per_hour'], 2); ?>/h</span>
                            <?php endif; ?>
                        </div>
                        <div class="duration-info">
                            <i class="fas fa-clock"></i>
                            <?php echo $service['duration']; ?> minutos
                        </div>
                    </div>
                    
                    <div class="service-stats">
                        <div class="stat-item">
                            <i class="fas fa-shopping-cart"></i>
                            <span><?php echo $service['total_orders']; ?> pedidos</span>
                        </div>
                        
                        <div class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $service['completed_orders']; ?> concluídos</span>
                        </div>
                        
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <span><?php echo $avgRating; ?>/5 (<?php echo $service['total_reviews']; ?>)</span>
                        </div>
                    </div>
                </div>
                
                <div class="service-actions">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                        <input type="hidden" name="action" value="toggle_status">
                        <button type="submit" class="btn-toggle <?php echo $isActive ? 'btn-deactivate' : 'btn-activate'; ?>">
                            <i class="fas fa-<?php echo $isActive ? 'eye-slash' : 'eye'; ?>"></i>
                            <?php echo $isActive ? 'Desativar' : 'Ativar'; ?>
                        </button>
                    </form>
                    
                    <button class="btn-edit" onclick="editService(<?php echo $service['id']; ?>)">
                        <i class="fas fa-edit"></i>
                        Editar
                    </button>
                    
                    <a href="/Views/product.php?id=<?php echo $service['id']; ?>" class="btn-view" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        Ver Anúncio
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Desenha ações rápidas para serviços
 */
function drawQuickActions() {
    ?>
    <section class="quick-actions">
        <div class="actions-container">
            <button class="btn-primary" onclick="openAddServiceModal()">
                <i class="fas fa-plus"></i>
                Adicionar Novo Serviço
            </button>
        </div>
    </section>
    <?php
}

/**
 * Desenha modal de feedback
 */
function drawFeedbackModal() {
    ?>
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
    <?php
}

/**
 * Desenha seção de informações básicas do formulário
 */
function drawBasicInfoSection($categories, $formData = []) {
    ?>
    <div class="form-section">
        <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>
        
        <div class="form-group">
            <label for="name">Nome do Serviço *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>"
                   placeholder="Ex: Limpeza doméstica completa">
            <small>Escolha um nome claro e descritivo para o seu serviço</small>
        </div>
        
        <div class="form-group">
            <label for="category_id">Categoria *</label>
            <select id="category_id" name="category_id" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo (($formData['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Escolha a categoria que melhor representa o seu serviço</small>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição do Serviço *</label>
            <textarea id="description" name="description" required rows="6"
                      placeholder="Descreva detalhadamente o que inclui no seu serviço, os benefícios para o cliente, e qualquer informação importante..."><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
            <small>Uma boa descrição aumenta as suas hipóteses de ser contratado</small>
        </div>
    </div>
    <?php
}

/**
 * Desenha seção de preços e duração
 */
function drawPricingSection($formData = []) {
    ?>
    <div class="form-section">
        <h3><i class="fas fa-euro-sign"></i> Preços e Duração</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price_per_hour">Preço por Hora (€) *</label>
                <input type="number" id="price_per_hour" name="price_per_hour" 
                       min="5" max="500" step="0.01" required
                       value="<?php echo htmlspecialchars($formData['price_per_hour'] ?? ''); ?>"
                       placeholder="25.00">
                <small>Defina um preço competitivo mas justo</small>
            </div>
            
            <div class="form-group">
                <label for="duration">Duração Estimada (minutos) *</label>
                <input type="number" id="duration" name="duration" 
                       min="15" max="480" required
                       value="<?php echo htmlspecialchars($formData['duration'] ?? ''); ?>"
                       placeholder="120">
                <small>Tempo médio necessário para completar o serviço</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="promotion">Desconto Promocional (%)</label>
            <input type="number" id="promotion" name="promotion" 
                   min="0" max="50" 
                   value="<?php echo htmlspecialchars($formData['promotion'] ?? '0'); ?>"
                   placeholder="0">
            <small>Ofereça um desconto para atrair mais clientes (opcional)</small>
        </div>
        
        <div class="price-preview">
            <h4>Previsão de Preços:</h4>
            <div class="price-calculation">
                <span class="price-label">Preço base:</span>
                <span class="price-value" id="base-price">€0.00</span>
            </div>
            <div class="price-calculation" id="discounted-section" style="display: none;">
                <span class="price-label">Com desconto:</span>
                <span class="price-value discount" id="discounted-price">€0.00</span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Desenha seção de configurações
 */
function drawConfigSection($isActive = true) {
    ?>
    <div class="form-section">
        <h3><i class="fas fa-cog"></i> Configurações</h3>
        
        <div class="form-group">
            <label class="checkbox-container">
                <input type="checkbox" name="is_active" value="1" 
                       <?php echo $isActive ? 'checked' : ''; ?>>
                <span class="checkmark"></span>
                Publicar serviço imediatamente
            </label>
            <small>Se desmarcado, o serviço ficará como rascunho</small>
        </div>
    </div>
    <?php
}

/**
 * Desenha lista de pedidos de serviços (para freelancers)
 */
function drawServiceOrdersList($orders) {
    ?>
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
                                'completed' => 'Completo',
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
    <?php
}

/**
 * Desenha estado vazio para pedidos de serviços
 */
function drawNoServiceOrdersState() {
    ?>
    <div class="no-services">
        <div class="no-services-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>Ainda não recebeu nenhum pedido</h3>
        <p>Assim que os utilizadores solicitarem os seus serviços, verá os pedidos aqui.</p>
    </div>
    <?php
}
?>
