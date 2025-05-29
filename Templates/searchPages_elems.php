<?php
// searchPages_elems.php - Funções para páginas de pesquisa e categorias - VERSÃO CORRIGIDA

/**
 * Desenha o banner de resultados de pesquisa
 */
function drawResultsBanner($pageTitle, $serviceCount, $currencyInfo) {
?>
<section class="results-banner">
    <div class="banner-content">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p><?php echo $serviceCount; ?> serviços encontrados</p>
        <?php if (isUserLoggedIn() && $currencyInfo): ?>
            <p class="currency-info">Preços exibidos em <?php echo $currencyInfo['name']; ?> (<?php echo $currencyInfo['symbol']; ?>)</p>
        <?php endif; ?>
    </div>
</section>
<?php
}

/**
 * Desenha a seção de filtros - CORRIGIDA
 */
function drawFiltersSection($query, $categories, $category_id, $min_price, $max_price, $currencyInfo) {
    // Obter símbolo da moeda
    $currencySymbol = $currencyInfo['symbol'] ?? '€';
?>
<section class="filters-section">
    <div class="filters-container">
        <h2>Filtros</h2>
        <form method="GET" action="" id="filter-form">
            <!-- Manter query original se existir -->
            <?php if (!empty($query)): ?>
                <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
            <?php endif; ?>
            
            <div class="filter-row">
                <!-- Filtro de Categoria -->
                <div class="filter-group">
                    <label for="category">Categoria:</label>
                    <select name="category" id="category" class="filter-select">
                        <option value="">Todas as Categorias</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <?php $selected = ($category_id == $cat['id']) ? 'selected' : ''; ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtros de Preço -->
                <div class="filter-group price-filter">
                    <label>Preço (<?php echo $currencySymbol; ?>/hora):</label>
                    <div class="price-range">
                        <input type="number" 
                               name="min_price" 
                               id="min_price" 
                               placeholder="Mín" 
                               value="<?php echo $min_price ? number_format($min_price, 2, '.', '') : ''; ?>" 
                               min="0" 
                               step="0.01"
                               class="price-input">
                        <span class="price-separator">até</span>
                        <input type="number" 
                               name="max_price" 
                               id="max_price" 
                               placeholder="Máx" 
                               value="<?php echo $max_price ? number_format($max_price, 2, '.', '') : ''; ?>" 
                               min="0" 
                               step="0.01"
                               class="price-input">
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="filter-actions">
                    <button type="submit" class="filter-button btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                    <button type="button" id="reset-filters" class="filter-button btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </button>
                </div>
            </div>

            <!-- Indicador de filtros ativos -->
            <?php 
            $active_filters = [];
            if (!empty($query)) {
                $active_filters[] = "Pesquisa: \"" . $query . "\"";
            }
            if ($category_id) {
                $cat_name = '';
                foreach ($categories as $cat) {
                    if ($cat['id'] == $category_id) {
                        $cat_name = $cat['name'];
                        break;
                    }
                }
                if ($cat_name) {
                    $active_filters[] = "Categoria: " . $cat_name;
                }
            }
            if ($min_price) {
                $active_filters[] = "Preço mín: " . $currencySymbol . number_format($min_price, 2);
            }
            if ($max_price) {
                $active_filters[] = "Preço máx: " . $currencySymbol . number_format($max_price, 2);
            }
            ?>
            
            <?php if (!empty($active_filters)): ?>
            <div class="active-filters">
                <span class="active-filters-label">Filtros ativos:</span>
                <?php foreach ($active_filters as $filter): ?>
                    <span class="filter-tag"><?php echo htmlspecialchars($filter); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</section>
<?php
}

/**
 * Desenha um card de serviço
 */
function drawServiceCard($service, $currencySymbol) {
    // Usar preços convertidos se disponíveis
    $pricePerHour = isset($service['price_per_hour_converted']) ? $service['price_per_hour_converted'] : $service['price_per_hour'];
    
    // Calcular preço com desconto
    $discounted_price = isset($service['discounted_price_converted']) ? 
        $service['discounted_price_converted'] : 
        calculateDiscountedPrice($pricePerHour, $service['promotion']);
    
    // Buscar informações da categoria
    $category = getCategoryById($service['category_id']);
    $category_name = $category ? $category['name'] : 'Categoria Desconhecida';
    
    // Processar imagem
    $imagePaths = explode(",", $service['image_paths'] ?? '');
    $firstImage = trim($imagePaths[0] ?? '');
    $finalImage = $firstImage !== '' ? '/Images/services/' . ltrim($firstImage, '/') : 'https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png';
?>
<div class="service-card">
    <div class="service-image">
        <img src="<?php echo htmlspecialchars($finalImage); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
        <?php if ($service['promotion'] > 0): ?>
            <div class="promotion-badge"><?php echo $service['promotion']; ?>% OFF</div>
        <?php endif; ?>
    </div>
    <div class="service-info">
        <div class="category-tag"><?php echo htmlspecialchars($category_name); ?></div>
        <h3><a href="/Views/product.php?id=<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></a></h3>
        <p class="service-description"><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</p>
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
<?php
}

/**
 * Desenha a grid de resultados de serviços
 */
function drawServicesGrid($services, $currencySymbol) {
?>
<div class="results-grid">
    <?php foreach ($services as $service): ?>
        <?php drawServiceCard($service, $currencySymbol); ?>
    <?php endforeach; ?>
</div>
<?php
}

/**
 * Desenha mensagem quando não há resultados
 */
function drawNoResults() {
?>
<div class="no-results">
    <div class="no-results-icon">
        <i class="fas fa-search"></i>
    </div>
    <h2>Nenhum serviço encontrado</h2>
    <p>Tente modificar sua pesquisa ou remover alguns filtros.</p>
    <div class="no-results-suggestions">
        <h4>Sugestões:</h4>
        <ul>
            <li>Verifique se todas as palavras estão escritas corretamente</li>
            <li>Tente usar palavras-chave diferentes</li>
            <li>Remova alguns filtros para ampliar a pesquisa</li>
            <li>Experimente categorias relacionadas</li>
        </ul>
    </div>
</div>
<?php
}

/**
 * Desenha paginação
 */
function drawPagination($query, $category_id, $min_price, $max_price, $current_page, $total_pages) {
    if ($total_pages <= 1) return;
    
    // Construir parâmetros base
    $params = [];
    if (!empty($query)) $params['query'] = $query;
    if ($category_id) $params['category'] = $category_id;
    if ($min_price) $params['min_price'] = $min_price;
    if ($max_price) $params['max_price'] = $max_price;
?>
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <?php $params['page'] = $current_page - 1; ?>
        <a href="?<?php echo http_build_query($params); ?>" class="prev-page">&laquo; Anterior</a>
    <?php endif; ?>
    
    <?php
    // Mostrar páginas próximas
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++):
    ?>
        <?php if ($i == $current_page): ?>
            <span class="current"><?php echo $i; ?></span>
        <?php else: ?>
            <?php $params['page'] = $i; ?>
            <a href="?<?php echo http_build_query($params); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>
    
    <?php if ($current_page < $total_pages): ?>
        <?php $params['page'] = $current_page + 1; ?>
        <a href="?<?php echo http_build_query($params); ?>" class="next-page">Próxima &raquo;</a>
    <?php endif; ?>
</div>
<?php
}

/**
 * Desenha um card de categoria
 */
function drawCategoryCard($category) {
?>
<div class="category-card">
    <div class="category-image">
        <img src="<?= htmlspecialchars($category['photo_url'] ?: 'assets/images/placeholder.jpg') ?>" 
             alt="<?= htmlspecialchars($category['name']) ?>">
    </div>
    <div class="category-details">
        <h3><?= htmlspecialchars($category['name']) ?></h3>
        <?php if (isset($category['product_count'])): ?>
            <span class="product-count"><?= $category['product_count'] ?> produtos</span>
        <?php endif; ?>
        <a href="search-results.php?category=<?= $category['id'] ?>" class="btn-view-category">Ver Produtos</a>
    </div>
</div>
<?php
}

/**
 * Desenha a grid de categorias
 */
function drawCategoriesGrid($categories) {
?>
<section class="categories-grid">
    <div class="categories-list">
        <?php if (count($categories) > 0): ?>
            <?php foreach ($categories as $category): ?>
                <?php drawCategoryCard($category); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php drawNoCategoriesState(); ?>
        <?php endif; ?>
    </div>
</section>
<?php
}

/**
 * Desenha estado quando não há categorias
 */
function drawNoCategoriesState() {
?>
<div class="no-categories">
    <i class="fas fa-folder-open"></i>
    <p>Nenhuma categoria disponível no momento.</p>
</div>
<?php
}

/**
 * Desenha paginação para categorias
 */
function drawCategoriesPagination($total, $itemsPerPage = 12) {
    if ($total <= $itemsPerPage) return;
    
    $totalPages = ceil($total / $itemsPerPage);
?>
<div class="pagination">
    <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
    <span class="pagination-info">Página 1 de <?= $totalPages ?></span>
    <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
</div>
<?php
}
?>
