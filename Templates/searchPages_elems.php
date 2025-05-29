<?php
// searchPages_elems.php - Funções para páginas de pesquisa e categorias

/**
 * Desenha o banner de resultados de pesquisa
 */
function drawResultsBanner($pageTitle, $serviceCount, $currencyInfo) {
    ?>
    <section class="results-banner">
        <div class="banner-content">
            <h1><?php echo $pageTitle; ?></h1>
            <p><?php echo $serviceCount; ?> serviços encontrados</p>
            <?php if (isUserLoggedIn() && $currencyInfo): ?>
                <p class="currency-info">Preços exibidos em <?php echo $currencyInfo['name']; ?> (<?php echo $currencyInfo['symbol']; ?>)</p>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Desenha a seção de filtros
 */
function drawFiltersSection($query, $categories, $category_id, $min_price, $max_price, $currencyInfo) {
    ?>
    <section class="filters-section">
        <div class="filters-container">
            <h2>Filtros</h2>
            <form action="/Views/searchPages/search-results.php" method="get" id="filter-form">
                <input type="hidden" name="query" value="<?php echo $query; ?>">
                
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
                
                <div class="filter-group">
                    <label for="min_price">Preço (<?php echo $currencyInfo['symbol']; ?>/hora):</label>
                    <div class="price-range">
                        <input type="number" name="min_price" id="min_price" placeholder="Min" value="<?php echo $min_price; ?>" min="0" step="0.01">
                        <span>até</span>
                        <input type="number" name="max_price" id="max_price" placeholder="Max" value="<?php echo $max_price; ?>" min="0" step="0.01">
                    </div>
                </div>
                
                <button type="submit" class="filter-button">Aplicar Filtros</button>
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
        <h2>Nenhum serviço encontrado</h2>
        <p>Tente modificar sua pesquisa ou remover alguns filtros.</p>
    </div>
    <?php
}

/**
 * Desenha paginação
 */
function drawPagination($query, $category_id, $min_price, $max_price, $current_page, $total_pages) {
    if ($total_pages <= 1) return;
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
            <a href="searchPages/search-results.php?category=<?= $category['id'] ?>" class="btn-view-category">Ver Produtos</a>
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
