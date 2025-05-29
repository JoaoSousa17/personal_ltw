<?php
// Verificar dependências e incluir arquivos necessários
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/searchPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Obter todas as categorias
$categories = getAllCategories();

// Verificar se categorias é um array válido
if (!is_array($categories)) {
    $categories = [];
}

// Obter total de categorias
$total = count($categories);

drawHeader("Handee - Categories", ["/Styles/Categories&Product.css"]);
?>

<main class="categories-container">
    <!-- Cabeçalho da página -->
    <div>
        <?php drawSectionHeader("Categorias", "") ?>
    </div>
    
    <!-- Grid de categorias -->
    <?php drawCategoriesGrid($categories); ?>
    
    <!-- Paginação se necessária -->
    <?php drawCategoriesPagination($total); ?>
</main>

<!-- Scripts necessários -->
<script src="/Scripts/search.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter() ?>
