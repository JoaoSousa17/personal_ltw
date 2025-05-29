<?php
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");
require_once(dirname(__FILE__)."/../../Controllers/ReasonBlockController.php");

// Verificar autenticação e permissões
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

if (!isUserAdmin()) {
    $_SESSION['error'] = 'Não tem permissão para aceder ao painel de administração.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Obter utilizadores (com ou sem pesquisa)
$searchTerm = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $users = searchUsers($searchTerm);
} else {
    $users = getAllUsers();
}

// Processar ações POST (bloquear, desbloquear, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];
    $result = false;
    
    switch ($action) {
        case 'block_user':
            $result = blockUser($userId);
            
            if ($result && isset($_POST['block_reason'])) {
                $reason = $_POST['block_reason'];
                $extraInfo = isset($_POST['block_extra_info']) ? $_POST['block_extra_info'] : '';
                addBlockReason($userId, $reason, $extraInfo);
            }
            break;
            
        case 'unblock_user':
            $result = unblockUser($userId);
            
            if ($result) {
                removeBlockReason($userId);
            }
            break;
            
        case 'delete_user':
            $result = deleteUser($userId);
            break;
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . ($result ? "?success=true" : "?error=true"));
    exit;
}

drawHeader("Handee - Gestão de Utilizadores", ["/Styles/admin.css", "/Styles/users.css"]);
?>

<main class="adminGeneral-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Gestão de Utilizadores", "Visualize e gerencie todos os utilizadores do sistema", true); ?>

    <!-- Barra de pesquisa -->
    <?php drawSearchBar($searchTerm) ?>

    <!-- Informações da pesquisa -->
    <?php drawSearchInfoMessageSection($searchTerm) ?>

    <!-- Tabela de utilizadores -->
    <?php drawUsersTable($searchTerm, $users) ?>
</main>

<?php drawFooter(); ?>
