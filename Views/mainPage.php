<?php
// Incluir o arquivo de gestão de sessões
require_once(dirname(__FILE__)."/../Utils/session.php");
require_once(dirname(__FILE__)."/../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../Templates/special_elems.php");

// Verificar e obter dados da sessão usando as funções do utils
$currentUser = getCurrentUser();
$isLoggedIn = isUserLoggedIn();
$isAdmin = isUserAdmin();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

drawHeader("Handee - Main Page", ["/Styles/main_page.css"]);

?>
<main>
    <!-- Banner Principal -->
    <section class="main-banner">
        <div>
            <h1>Bem-vindo à Handee</h1>
            <p>Soluções inovadoras para os seus desafios do dia a dia!</p>
            <a href="staticPages/services.php" class="banner-button">Descobrir Serviços</a>
        </div>
    </section>

    <!-- Categorias Existentes - Carrossel -->
    <section class="featured-services">
        <?php drawSectionHeader("Categorias em Destaque", "Explore nossos serviços por categoria") ?>
        <?php drawCaroussel(); ?>
    </section>
</main>
<?php drawFooter(); ?>

<script src="/Scripts/carousel.js"></script>