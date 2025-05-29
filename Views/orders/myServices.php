<?php
// Verificar autenticação e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/orderPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/serviceController.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder aos seus serviços.';
    header("Location: /Views/auth.php");
    exit();
}

// Obter dados do utilizador
$currentUserId = getCurrentUserId();

// Processar ações de toggle de status
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

// Obter dados necessários
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
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2>Os Meus Serviços</h2>
        <p>Gerencie os seus anúncios de serviços e monitorize o desempenho</p>
    </section>

    <!-- Mensagens de feedback -->
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Estatísticas dos serviços -->
    <?php drawServicesStats($stats); ?>

    <!-- Ações rápidas -->
    <?php drawQuickActions(); ?>

    <!-- Lista de serviços -->
    <section class="services-section">
        <?php if (empty($services)): ?>
            <?php drawNoServicesState(); ?>
        <?php else: ?>
            <?php drawServicesList($services); ?>
        <?php endif; ?>
    </section>
</main>

<!-- Scripts necessários -->
<script src="/Scripts/orders.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
