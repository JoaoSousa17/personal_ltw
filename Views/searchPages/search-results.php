<?php
// Verificar dependências e incluir arquivos necessários
require_once("../../Templates/common_elems.php");
require_once("../../Templates/searchPages_elems.php");
require_once("../../Controllers/searchBarController.php");
require_once("../../Controllers/serviceController.php");
require_once("../../Controllers/categoriesController.php");
require_once("../../Controllers/distancesCalculationController.php");
require_once("../../Utils/session.php");

// Obter parâmetros da pesquisa
$query = isset($_GET['query']) ? htmlspecialchars(strip_tags($_GET['query'])) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

// Realizar a pesquisa
$services = search($query, $category_id, $min_price, $max_price);
$services = convertServicesPrice($services);

// Obter dados necessários
$currencyInfo = getUserCurrencyInfo();
$categories = getAllCategories();
$pageTitle = empty($query) ? "Todos os Serviços" : "Resultados para: " . $query;

// Configurar paginação
$total_services = count($services);
$services_per_page = 12;
$total_pages = ceil($total_services / $services_per_page);
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Determinar símbolo da moeda
$currencySymbol = isset($services[0]['currency_symbol']) ? $services[0]['currency_symbol'] : '€';

drawHeader($pageTitle, ["/Styles/search_results.css"]);
?>

<main>
    <!-- Banner de resultados -->
    <?php drawResultsBanner($pageTitle, count($services), $currencyInfo); ?>

    <!-- Seção de filtros -->
    <?php drawFiltersSection($query, $categories, $category_id, $min_price, $max_price, $currencyInfo); ?>

    <!-- Resultados da pesquisa -->
    <section class="search-results">
        <?php if (empty($services)): ?>
            <?php drawNoResults(); ?>
        <?php else: ?>
            <?php drawServicesGrid($services, $currencySymbol); ?>
            
            <!-- Paginação -->
            <?php drawPagination($query, $category_id, $min_price, $max_price, $current_page, $total_pages); ?>
        <?php endif; ?>
    </section>
</main>

<!-- Scripts necessários -->
<script src="/Scripts/search.js"></script>

<?php drawFooter(); ?>
