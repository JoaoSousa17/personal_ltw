<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");
require_once(dirname(__FILE__)."/../../Controllers/ReasonBlockController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

// Verificar se o utilizador é administrador
if (!isUserAdmin()) {
    $_SESSION['error'] = 'Não tem permissão para aceder ao painel de administração.';
    header("Location: /Views/mainPage.php");
    exit();
}

$userController = new UserController();
$reasonBlockController = new ReasonBlockController();

$searchTerm = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $users = $userController->searchUsers($searchTerm);
} else {
    $users = $userController->getAllUsers();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['user_id'])) {
    $userController = new UserController();
    $userId = $_POST['user_id'];
    $action = $_POST['action'];
    $result = false;
    
    switch ($action) {
        case 'block_user':
            $result = $userController->blockUser($userId);
            
            // Se o bloqueio foi bem-sucedido, registrar a razão
            if ($result && isset($_POST['block_reason'])) {
                $reason = $_POST['block_reason'];
                $extraInfo = isset($_POST['block_extra_info']) ? $_POST['block_extra_info'] : '';
                
                $reasonBlockController->addBlockReason($userId, $reason, $extraInfo);
            }
            break;
            
        case 'unblock_user':
            $result = $userController->unblockUser($userId);
            
            // Se o desbloqueio foi bem-sucedido, remover a razão
            if ($result) {
                $reasonBlockController->removeBlockReason($userId);
            }
            break;
            
        case 'delete_user':
            $result = $userController->deleteUser($userId);
            break;
    }
    
    // Recarregar a página após a ação
    header("Location: " . $_SERVER['PHP_SELF'] . ($result ? "?success=true" : "?error=true"));
    exit;
}

drawHeader("Handee - Gestão de Utilizadores", ["/Styles/admin.css", "/Styles/users.css"]);
?>

<main class="adminGeneral-container">
    <?php drawSectionHeader("Gestão de Utilizadores", "Visualize e gerencie todos os utilizadores do sistema", true); ?>

    <!-- Barra de pesquisa -->
    <?php drawSearchBar($searchTerm) ?>

    <!-- Resultados da pesquisa ou mensagem -->
    <?php drawSearchInfoMessageSection($searchTerm) ?>

    <!-- Tabela de usuários -->
    <?php drawUsersTable($searchTerm, $users) ?>
</main>

<?php drawFooter(); ?>