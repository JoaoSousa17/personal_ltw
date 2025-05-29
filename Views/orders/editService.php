<?php
require_once('../../Controllers/serviceController.php');
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once('../../Utils/session.php');
require_once('../../Controllers/categoriesController.php');

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Serviço não especificado.';
    header("Location: /Views/orders/myServices.php");
    exit();
}

$serviceId = intval($_GET['id']);
$service = getServiceById($serviceId);
/*
if (!$service || $service['freelancer_id'] !== getCurrentUserId()) {
    $_SESSION['error'] = 'Serviço não encontrado ou sem permissão.';
    header("Location: /Views/orders/myServices.php");
    exit();
}*/

$categories = getAllCategories();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

drawHeader("Handee - Editar Serviço", ["/Styles/product.css"]);
?>

<main>
    <section class="section-header">
        <h2>Editar Serviço</h2>
        <p>Atualize os detalhes do seu serviço</p>
        <a href="/Views/orders/myServices.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Voltar" class="back-button">
        </a>
    </section>

    <div class="create-service-container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="/Controllers/serviceController.php" class="create-service-form">
            <input type="hidden" name="action" value="update_service">
            <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

            <div class="form-sections">

                <!-- Informações Básicas -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>

                    <div class="form-group">
                        <label for="name">Nome do Serviço *</label>
                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($service['name']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoria *</label>
                        <select id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $service['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Descrição *</label>
                        <textarea id="description" name="description" required rows="6"><?= htmlspecialchars($service['description']) ?></textarea>
                    </div>
                </div>

                <!-- Preços e Duração -->
                <div class="form-section">
                    <h3><i class="fas fa-euro-sign"></i> Preços e Duração</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price_per_hour">Preço por Hora (€) *</label>
                            <input type="number" id="price_per_hour" name="price_per_hour" 
                                   min="5" max="500" step="0.01" required
                                   value="<?= $service['price_per_hour'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="duration">Duração Estimada (minutos) *</label>
                            <input type="number" id="duration" name="duration" 
                                   min="15" max="480" required
                                   value="<?= $service['duration'] ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="promotion">Desconto Promocional (%)</label>
                        <input type="number" id="promotion" name="promotion" 
                               min="0" max="50"
                               value="<?= $service['promotion'] ?>">
                    </div>

                    <div class="price-preview">
                        <h4>Previsão de Preços:</h4>
                        <div class="price-calculation">
                            <span class="price-label">Preço base:</span>
                            <span class="price-value" id="base-price">€0.00</span>
                        </div>
                        <div class="price-calculation" id="discounted-section" style="display: none;">
                            <span class="price-label">Com desconto:</span>
                            <span class="price-value discount" id="discounted-price">€0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="form-section">
                    <h3><i class="fas fa-cog"></i> Configurações</h3>

                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" name="is_active" value="1" <?= $service['is_active'] ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Publicar serviço imediatamente
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-create">
                    <i class="fas fa-save"></i>
                    Guardar Alterações
                </button>
                <a href="/Views/orders/myServices.php" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<script>
// Cálculo automático de preços
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price_per_hour');
    const durationInput = document.getElementById('duration');
    const promotionInput = document.getElementById('promotion');
    const basePriceDisplay = document.getElementById('base-price');
    const discountedPriceDisplay = document.getElementById('discounted-price');
    const discountedSection = document.getElementById('discounted-section');

    function calculatePrices() {
        const pricePerHour = parseFloat(priceInput.value) || 0;
        const duration = parseInt(durationInput.value) || 0;
        const promotion = parseInt(promotionInput.value) || 0;

        const basePrice = (pricePerHour * duration) / 60;
        const discountedPrice = basePrice * (1 - promotion / 100);

        basePriceDisplay.textContent = '€' + basePrice.toFixed(2);

        if (promotion > 0) {
            discountedPriceDisplay.textContent = '€' + discountedPrice.toFixed(2);
            discountedSection.style.display = 'flex';
        } else {
            discountedSection.style.display = 'none';
        }
    }

    priceInput.addEventListener('input', calculatePrices);
    durationInput.addEventListener('input', calculatePrices);
    promotionInput.addEventListener('input', calculatePrices);

    calculatePrices(); // Inicial
});
</script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php drawFooter(); ?>
