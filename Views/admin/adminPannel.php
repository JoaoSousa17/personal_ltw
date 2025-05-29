<?php
// Verificar permissões de acesso
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");

// Verificar autenticação e permissões de admin
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

drawHeader("Handee - Painel de Admin", ["/Styles/admin.css"]);
?>

<main class="admin-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Painel de Admin", "Gerenciamento da plataforma"); ?>
    
    <!-- Cards de navegação -->
    <div class="admin-panels-container">
        <div class="admin-row main-row">
            <?php drawAdminCard("Gerir Usuários", "users", "users.php", false, "wide"); ?>
            <?php drawAdminCard("Desbloquear Acessos", "unlock", "blockedUsers.php", false, "narrow"); ?>
        </div>
        
        <div class="admin-row">    
            <?php drawAdminCard("Subscrições Newsletter", "newsletter", "newsletter.php", false, "narrow"); ?>
            <?php drawAdminCard("Gerir Categorias", "categories", "category_management.php", false, "wide"); ?>
        </div>
        
        <div class="admin-row">   
            <?php drawAdminCard("Contactos", "contacts", "admin_contacts.php", false, "wide"); ?>
            <?php drawAdminCard("Controlo Admin", "admin", "adminUsers.php", false, "narrow"); ?>
        </div>
        
        <div class="admin-row">
            <?php drawAdminCard("Data Analysis", "analytics", "analytics.php", true); ?>
        </div>
    </div>
</main>

<?php drawFooter(); ?>
