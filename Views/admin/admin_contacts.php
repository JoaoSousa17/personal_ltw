<?php 
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/contactController.php");

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

// Obter todos os contactos
$contacts = getAllContacts();

drawHeader("Handee - Gestão de Contactos", ["/Styles/admin.css"]);
?>

<main class="contacts-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Gestão de Contactos", "Visualize e gerencie as mensagens de contacto recebidas", true); ?>
    
    <!-- Mensagens de feedback -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert-success">
            <?php echo htmlspecialchars($_SESSION['admin_success']); ?>
        </div>
        <?php unset($_SESSION['admin_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert-error">
            <?php echo htmlspecialchars($_SESSION['admin_error']); ?>
        </div>
        <?php unset($_SESSION['admin_error']); ?>
    <?php endif; ?>
    
    <!-- Tabela de contactos -->
    <?php drawContactsTable($contacts) ?>
</main>

<?php drawFooter() ?>
