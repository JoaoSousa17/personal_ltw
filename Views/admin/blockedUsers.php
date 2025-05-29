<?php
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");
require_once(dirname(__FILE__)."/../../Controllers/UnblockAppealController.php");
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

// Obter dados necessários
$blockedUsers = getAllBlockedUsers();
$appeals = getAllAppeals();

drawHeader("Handee - Utilizadores Bloqueados", ["/Styles/admin.css", "/Styles/appeals.css"]);
?>

<main class="adminGeneral-container">
    <!-- Utilizadores bloqueados -->
    <?php drawSectionHeader("Utilizadores Bloqueados", "Gerencia os utilizadores que estão bloqueados no sistema", true); ?>
    <?php drawBlockedUsersTable($blockedUsers) ?>

    <!-- Pedidos de desbloqueio -->
    <?php drawSectionHeader("Pedidos de Desbloqueio", "Gerencie os pedidos de desbloqueio de usuários"); ?>
    
    <!-- Mensagens de feedback -->
    <?php drawAppealMessages(); ?>
    
    <!-- Filtros dos pedidos -->
    <?php drawAppealFilter(); ?>
    
    <!-- Grid dos pedidos -->
    <?php drawAppealsGrid($appeals); ?>
</main>

<script src='/Scripts/blockAppealFilter.js'></script>

<?php drawFooter(); ?>
