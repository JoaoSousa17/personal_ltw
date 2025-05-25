<?php 
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/newsletterController.php");

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

// Criar uma instância do controlador
$newsletterController = new NewsletterController();
// Chamar o método no objeto, não como função global
$subscriptions = $newsletterController->getAllSubscriptions();
drawHeader("Handee - Newsletter", ["/Styles/admin.css"]);
?>

<main class="newsletter-container">
    <?php drawSectionHeader("Subscrições da NewsLetter", "Gerencia os emails para os quais enviar a newsletter", true); ?>
    <?php drawNewsletterTable($subscriptions) ?>
</main>
<?php drawFooter() ?>