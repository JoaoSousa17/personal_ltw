<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/newsletterController.php");
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
