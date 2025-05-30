<?php

// Verificar dependências e incluir arquivos necessários
require_once("../../Templates/common_elems.php");
require_once("../../Templates/searchPages_elems.php");
require_once("../../Controllers/searchBarController.php");
require_once("../../Controllers/serviceController.php");
require_once("../../Controllers/categoriesController.php");
require_once("../../Controllers/distancesCalculationController.php");
require_once("../../Utils/session.php");

// Obter e processar parâmetros da pesquisa
$query = isset($_GET['query']) ? trim(htmlspecialchars(strip_tags($_GET['query']))) : '';
$category_id = isset($_GET['category']) && $_GET['category'] !== '' && $_GET['category'] !== '0' ? intval($_GET['category']) : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;

// Realizar a pesquisa
try {
    $services = search($query, $category_id, $min_price, $max_price);
    
    if (!is_array($services)) {
        $services = [];
    }
    
    $services = convertServicesPrice($services);
} catch (Exception $e) {
    // Em caso de erro na pesquisa, definir array vazio
    $services = [];
    error_log("Erro na pesquisa: " . $e->getMessage());
}

// Obter dados necessários
$currencyInfo = getUserCurrencyInfo();
$categories = getAllCategories();

// Garantir que categories é um array
if (!is_array($categories)) {
    $categories = [];
}

// Determinar título da página
if (!empty($query)) {
    $pageTitle = "Resultados para: " . $query;
} elseif ($category_id) {
    $categoryName = '';
    foreach ($categories as $cat) {
        if ($cat['id'] == $category_id) {
            $categoryName = $cat['name'];
            break;
        }
    }
    $pageTitle = $categoryName ? "Categoria: " . $categoryName : "Serviços por Categoria";
} else {
    $pageTitle = "Todos os Serviços";
}

// Configurar paginação
$total_services = count($services);
$services_per_page = 12;
$total_pages = ceil($total_services / $services_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Determinar símbolo da moeda
$currencySymbol = '€'; // valor padrão
if (!empty($services) && isset($services[0]['currency_symbol'])) {
    $currencySymbol = $services[0]['currency_symbol'];
} elseif ($currencyInfo && isset($currencyInfo['symbol'])) {
    $currencySymbol = $currencyInfo['symbol'];
}

// Incluir CSS e iniciar HTML
drawHeader($pageTitle, ["/Styles/search_results.css"]);
?>

<main>
    <!-- Banner de resultados usando a função existente -->
    <?php drawResultsBanner($pageTitle, count($services), $currencyInfo); ?>

    <!-- Seção de filtros usando a função corrigida -->
    <?php drawFiltersSection($query, $categories, $category_id, $min_price, $max_price, $currencyInfo); ?>

    <!-- Resultados da pesquisa -->
    <section class="search-results">
        <div class="container">
            <?php if (empty($services)): ?>
                <!-- Usar função existente para "sem resultados" -->
                <?php drawNoResults(); ?>
            <?php else: ?>
                <!-- Usar função existente para grid de serviços -->
                <?php drawServicesGrid($services, $currencySymbol); ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Incluir o JavaScript corrigido -->
<script src="/Scripts/search.js"></script>

<?php drawFooter(); ?>
