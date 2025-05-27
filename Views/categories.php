<?php
// Include necessary files
require_once(dirname(__FILE__)."/../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../Controllers/categoriesController.php");
drawHeader("Handee - Main Page", ["/Styles/main_page.css", "/Styles/carousel.css", "/Styles/categories.css"]);

// Get all categories
$categories = getAllCategories();

// Check if categories is a valid array
if (!is_array($categories)) {
    $categories = [];
}

// Get total number of categories
$total = count($categories);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header will be included from header.php -->
    
    <main class="categories-container">
        <div>
            <?php drawSectionHeader("Categorias", "") ?>
        </div>
        <!-- Categories Grid -->
        <section class="categories-grid">
            <div class="categories-list">
                <?php if ($total > 0): ?>
                    <?php foreach ($categories as $category): ?>
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
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-categories">
                        <i class="fas fa-folder-open"></i>
                        <p>Nenhuma categoria disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($total > 12): ?>
                <div class="pagination">
                    <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <span class="pagination-info">Página 1 de <?= ceil($total / 12) ?></span>
                    <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            <?php endif; ?>
        </section>
    </main>
    
    <!-- Footer will be included from footer.php -->
    <?php drawFooter() ?>
</body>
</html>