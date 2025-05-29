<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/serviceController.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");
require_once(dirname(__FILE__)."/../../Controllers/distancesCalculationController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos seus serviços.';
    header("Location: /Views/auth.php");
    exit();
}

$currentUserId = getCurrentUserId();

// Processar ações (ativar/desativar serviço)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['service_id'])) {
    $serviceId = intval($_POST['service_id']);
    $action = $_POST['action'];
    
    if ($action === 'toggle_status') {
        $result = toggleServiceStatus($serviceId, $currentUserId);
        if ($result) {
            $_SESSION['success'] = 'Estado do serviço atualizado com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar o serviço ou não tem permissão.';
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obter serviços e estatísticas do utilizador (serão implementados no serviceController)
$services = getUserServices($currentUserId);
$stats = getServiceStats($currentUserId);
$categories = getAllCategories();

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

drawHeader("Handee - Os Meus Serviços", ["/Styles/MyRequest&Items.css"]);
?>

<main>
    <section class="section-header">
        <h2>Os Meus Serviços</h2>
        <p>Gerencie os seus anúncios de serviços e monitorize o desempenho</p>
    </section>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Estatísticas dos Serviços -->
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
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total de Pedidos</p>
                </div>
            </div>
            
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

    <!-- Ações Rápidas -->
    <section class="quick-actions">
        <div class="actions-container">
            <button class="btn-primary" onclick="openAddServiceModal()">
                <i class="fas fa-plus"></i>
                Adicionar Novo Serviço
            </button>
        </div>
    </section>

    <!-- Lista de Serviços -->
    <section class="services-section">
        <?php if (empty($services)): ?>
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
        <?php else: ?>
            <div class="services-list">
            <?php foreach ($services as $service): 
                $discountedPrice = calculateDiscountedPrice($service['price_per_hour'], $service['promotion']);
                
                // Converter preços
                $userId = getCurrentUserId();
                $displayPricePerHour = convertAndFormatPrice($service['price_per_hour'], $userId);
                $displayDiscountedPrice = convertAndFormatPrice($discountedPrice, $userId);
                ?>
                    <div class="service-card">
                        <div class="service-pricing">
                            <div class="price-info">
                                <?php if ($service['promotion'] > 0): ?>
                                    <span class="original-price"><?php echo $displayPricePerHour; ?>/h</span>
                                    <span class="discounted-price"><?php echo $displayDiscountedPrice; ?>/h</span>
                                    <span class="discount-badge"><?php echo $service['promotion']; ?>% OFF</span>
                                <?php else: ?>
                                    <span class="current-price"><?php echo $displayPricePerHour; ?>/h</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
function openAddServiceModal() {
    alert('Funcionalidade de adicionar serviço em desenvolvimento');
}

function editService(serviceId) {
    alert('Funcionalidade de editar serviço em desenvolvimento. ID: ' + serviceId);
}

function viewServiceStats(serviceId) {
    alert('Funcionalidade de estatísticas do serviço em desenvolvimento. ID: ' + serviceId);
}

function exportServices() {
    alert('Funcionalidade de exportar dados em desenvolvimento');
}
</script>

<!-- Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>