<?php
require_once(dirname(__FILE__)."/../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../Templates/special_elems.php");
drawHeader("Handee - Main Page", ["/Styles/main_page.css", "/Styles/carousel.css"]);
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
