<?php
// Verificar autenticação e incluir dependências
require_once('../../Controllers/serviceController.php');
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/orderPages_elems.php");
require_once('../../Utils/session.php');
require_once('../../Controllers/categoriesController.php');

// Verificar se o utilizador está logado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para editar um serviço.';
    header("Location: /Views/auth.php");
    exit();
}

// Verificar se foi especificado um ID
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Serviço não especificado.';
    header("Location: /Views/orders/myServices.php");
    exit();
}

// Obter e validar dados do serviço
$serviceId = intval($_GET['id']);
$service = getServiceById($serviceId);
$currentUserId = getCurrentUserId();

if (!$service || $service['freelancer_id'] !== $currentUserId) {
    $_SESSION['error'] = 'Serviço não encontrado ou não tem permissão para editá-lo.';
    header("Location: /Views/orders/myServices.php");
    exit();
}

// Obter dados necessários
$categories = getAllCategories();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

drawHeader("Handee - Editar Serviço", ["/Styles/product.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2>Editar Serviço</h2>
        <p>Atualize os detalhes do seu serviço</p>
        <a href="/Views/orders/myServices.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Voltar" class="back-button">
        </a>
    </section>

    <!-- Container principal -->
    <div class="create-service-container">
        <!-- Mensagens de feedback -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Formulário de edição -->
        <form method="POST" action="/Controllers/serviceController.php" class="create-service-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_service">
            <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

            <div class="form-sections">
                <!-- Informações básicas -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>

                    <div class="form-group">
                        <label for="name">Nome do Serviço *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($service['name']) ?>"
                               minlength="3" maxlength="100">
                        <small>Mínimo 3 caracteres, máximo 100</small>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoria *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $service['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Descrição *</label>
                        <textarea id="description" name="description" required rows="6" 
                                  minlength="10" maxlength="1000"><?= htmlspecialchars($service['description']) ?></textarea>
                        <small>Mínimo 10 caracteres, máximo 1000. Atual: <span id="char-count"><?= strlen($service['description']) ?></span></small>
                    </div>
                </div>

                <!-- Preços e duração -->
                <div class="form-section">
                    <h3><i class="fas fa-euro-sign"></i> Preços e Duração</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price_per_hour">Preço por Hora (€) *</label>
                            <input type="number" id="price_per_hour" name="price_per_hour" 
                                   min="5" max="500" step="0.01" required
                                   value="<?= number_format($service['price_per_hour'], 2, '.', '') ?>">
                            <small>Entre €5.00 e €500.00</small>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duração Estimada (minutos) *</label>
                            <input type="number" id="duration" name="duration" 
                                   min="15" max="480" required
                                   value="<?= $service['duration'] ?>">
                            <small>Entre 15 minutos e 8 horas</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="promotion">Desconto Promocional (%)</label>
                        <input type="number" id="promotion" name="promotion" 
                               min="0" max="50" step="1"
                               value="<?= $service['promotion'] ?>">
                        <small>Entre 0% e 50%</small>
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
                <?php drawConfigSection($service['is_active']); ?>
            </div>

            <!-- Botões de ação -->
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

<!-- Scripts necessários -->
<script src="/Scripts/orders.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
