<!----------------
Usados na MainPage
----------------->

<?php
/**
 * Gera um carrossel visual com as categorias existentes no sistema.
 * Caso não existam categorias, apresenta um item de placeholder.
 * A distribuição visual é feita com classes posicionais (prev-2, prev, active, next, next-2).
 */
function drawCaroussel() {
    require_once(dirname(__FILE__)."/../Controllers/categoriesController.php");

    /* Gestão Backend para obtenção de todas as categorias */
    $categories = getAllCategories();
    if (!is_array($categories)) {
        $categories = [];
    }
    $total = count($categories);
    ?>

    <div class="carousel-container">
        <div class="items">
            <?php if ($total > 0):
                $classes = ['prev-2', 'prev', 'active', 'next', 'next-2'];  /* 5 Categorias surgirão na tela de cada vez */
                $startIdx = 0;
                if ($total < 5) {   /* Caso especial, para existência de menos de 5 categorias */
                    $startIdx = 2 - floor($total / 2);
                }

                foreach ($categories as $index => $category):
                    /* Cálculo da classe posicional no carrossel para cada categoria */
                    $classIdx = ($index - $startIdx + 5) % 5;
                    $class = isset($classes[$classIdx]) ? $classes[$classIdx] : '';
            ?>

                <!-- Desenho do cartão da categoria, a ser exibido no carrossel -->
                <div class="item <?= $class ?>">
                    <img src="<?= htmlspecialchars($category['photo_url']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                    <div class="item-info">
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                    </div>
                </div>
            <?php endforeach; else: ?>

            <!-- Caso não existam categorias -->
            <div class="item active">
                <img src="http://via.placeholder.com/500x500" alt="Categoria não encontrada">
                <div class="item-info">
                    <h3>Sem categorias disponíveis</h3>
                </div>
            </div>
            <?php endif; ?>

            <!-- Botões de Controlo do Carrossel -->
            <div class="button-container">
                <!-- Botão de Retroceder -->
                <div class="button button-prev">
                    <div class="arrow-icon">&lt;</div>
                </div>

                <!-- Botão de Avançar -->
                <div class="button button-next">
                    <div class="arrow-icon">&gt;</div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
