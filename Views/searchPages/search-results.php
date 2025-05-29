<?php
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/searchBarController.php");
require_once(dirname(__FILE__)."/../../Controllers/serviceController.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");
require_once(dirname(__FILE__)."/../../Controllers/distancesCalculationController.php");

// Obter parâmetros da pesquisa
$query = isset($_GET['query']) ? htmlspecialchars(strip_tags($_GET['query'])) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

// Realizar a pesquisa
$services = search($query, $category_id, $min_price, $max_price);

// Aplicar conversão de moeda aos serviços
$services = convertServicesPrice($services);

// Obter informações da moeda do utilizador
$currencyInfo = getUserCurrencyInfo();

// Obter todas as categorias
$categories = getAllCategories();

// Título da página
$pageTitle = empty($query) ? "Todos os Serviços" : "Resultados para: " . $query;

drawHeader($pageTitle, ["/Styles/search_results.css"]);
?>

<main>
    <!-- Banner de Resultados -->
    <section class="results-banner">
        <div class="banner-content">
            <h1><?php echo $pageTitle; ?></h1>
            <p><?php echo count($services); ?> serviços encontrados</p>
            <?php if (isUserLoggedIn()): ?>
                <p class="currency-info">Preços exibidos em <?php echo $currencyInfo['name']; ?> (<?php echo $currencyInfo['symbol']; ?>)</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Seção de Filtros -->
    <section class="filters-section">
        <div class="filters-container">
            <h2>Filtros</h2>
            <form action="/Views/searchPages/search-results.php" method="get" id="filter-form">
                <!-- Manter a pesquisa original -->
                <input type="hidden" name="query" value="<?php echo $query; ?>">
                
                <!-- Filtro por Categoria -->
                <div class="filter-group">
                    <label for="category">Categoria:</label>
                    <select name="category" id="category">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php $selected = ($category_id == $cat['id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filtro por Preço -->
                <div class="filter-group">
                    <label for="min_price">Preço (<?php echo $currencyInfo['symbol']; ?>/hora):</label>
                    <div class="price-range">
                        <input type="number" name="min_price" id="min_price" placeholder="Min" value="<?php echo $min_price; ?>" min="0" step="0.01">
                        <span>até</span>
                        <input type="number" name="max_price" id="max_price" placeholder="Max" value="<?php echo $max_price; ?>" min="0" step="0.01">
                    </div>
                </div>
                
                <!-- Botão de Aplicar Filtros -->
                <button type="submit" class="filter-button">Aplicar Filtros</button>
            </form>
        </div>
    </section>

    <!-- Resultados da Pesquisa -->
    <section class="search-results">
        <?php if (empty($services)): ?>
            <div class="no-results">
                <h2>Nenhum serviço encontrado</h2>
                <p>Tente modificar sua pesquisa ou remover alguns filtros.</p>
            </div>
        <?php else: ?>
            <div class="results-grid">
                <?php foreach ($services as $service): 
                    // Usar preços convertidos se disponíveis, senão usar originais
                    $pricePerHour = isset($service['price_per_hour_converted']) ? $service['price_per_hour_converted'] : $service['price_per_hour'];
                    $currencySymbol = isset($service['currency_symbol']) ? $service['currency_symbol'] : '€';
                    
                    // Calcular preço com desconto usando preço convertido
                    $discounted_price = isset($service['discounted_price_converted']) ? 
                        $service['discounted_price_converted'] : 
                        calculateDiscountedPrice($pricePerHour, $service['promotion']);
                    
                    // Calcular preço total com base na duração usando preço convertido
                    $total_price = $discounted_price * ($service['duration'] / 60);
                    
                    // Buscar informações da categoria
                    $category = getCategoryById($service['category_id']);
                    $category_name = $category ? $category['name'] : 'Categoria Desconhecida';
                ?>
                    <div class="service-card">
                        <div class="service-image">
                            <!-- Imagem do serviço (placeholder ou dinâmica) -->
                            <img src="/Images/services/placeholder.jpg" alt="<?php echo $service['name']; ?>">
                            <?php if ($service['promotion'] > 0): ?>
                                <div class="promotion-badge"><?php echo $service['promotion']; ?>% OFF</div>
                            <?php endif; ?>
                        </div>
                        <div class="service-info">
                            <div class="category-tag"><?php echo $category_name; ?></div>
                            <h3><a href="/Views/product.php?id=<?php echo $service['id']; ?>"><?php echo $service['name']; ?></a></h3>
                            <p class="service-description"><?php echo substr($service['description'], 0, 100); ?>...</p>
                            <div class="service-meta">
                                <span class="duration"><i class="clock-icon"></i> ~<?php echo $service['duration']; ?> hrs</span>
                                <div class="price">
                                    <?php if ($service['promotion'] > 0): ?>
                                        <span class="original-price"><?php echo $currencySymbol; ?><?php echo number_format($pricePerHour, 2, ',', ''); ?>/h</span>
                                        <span class="discounted-price"><?php echo $currencySymbol; ?><?php echo number_format($discounted_price, 2, ',', ''); ?>/h</span>
                                    <?php else: ?>
                                        <span class="price-value"><?php echo $currencySymbol; ?><?php echo number_format($pricePerHour, 2, ',', ''); ?>/h</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="/Views/product.php?id=<?php echo $service['id']; ?>" class="view-service-btn">Ver Detalhes</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginação (opcional) -->
            <?php 
            // Calcular o total de páginas necessárias
            $total_services = count($services);
            $services_per_page = 12; // Número de serviços por página
            $total_pages = ceil($total_services / $services_per_page);
            
            // Obter a página atual
            $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            
            // Mostrar a paginação apenas se houver mais de uma página
            if ($total_pages > 1):
            ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?query=<?php echo urlencode($query); ?>&category=<?php echo $category_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&page=<?php echo $current_page - 1; ?>" class="prev-page">&laquo;</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?query=<?php echo urlencode($query); ?>&category=<?php echo $category_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?query=<?php echo urlencode($query); ?>&category=<?php echo $category_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&page=<?php echo $current_page + 1; ?>" class="next-page">&raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php
// Incluir o rodapé
drawFooter();
?>

<script>
    // Script para atualizar automaticamente quando os filtros são alterados (opcional)
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        
        // Quando a categoria for alterada, atualizar automaticamente os resultados
        // Isso é opcional e pode ser removido se não quiser atualização automática
        categorySelect.addEventListener('change', function() {
            // document.getElementById('filter-form').submit();
        });
    });
</script>