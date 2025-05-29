<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para criar um serviço.';
    header("Location: /Views/auth.php");
    exit();
}

// Obter categorias para o formulário
$categories = getAllCategories();

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$warning = $_SESSION['warning'] ?? '';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['warning']);

drawHeader("Handee - Criar Novo Serviço", ["/Styles/product.css"]);
?>

<main>
    <section class="section-header">
        <h2>Criar Novo Serviço</h2>
        <p>Publique o seu serviço e comece a ganhar dinheiro hoje mesmo</p>
        <a href="/Views/orders/myServices.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Voltar" class="back-button">
        </a>
    </section>

    <div class="create-service-container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($warning): ?>
            <div class="alert alert-warning"><?php echo $warning; ?></div>
        <?php endif; ?>

        <!-- Formulário de criação de serviço -->
        <form method="post" action="/Controllers/serviceController.php" enctype="multipart/form-data" class="create-service-form">
            <input type="hidden" name="action" value="create_service">
            
            <div class="form-sections">
                
                <!-- Informações Básicas -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>
                    
                    <div class="form-group">
                        <label for="name">Nome do Serviço *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               placeholder="Ex: Limpeza doméstica completa">
                        <small>Escolha um nome claro e descritivo para o seu serviço</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Categoria *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small>Escolha a categoria que melhor representa o seu serviço</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descrição do Serviço *</label>
                        <textarea id="description" name="description" required rows="6"
                                  placeholder="Descreva detalhadamente o que inclui no seu serviço, os benefícios para o cliente, e qualquer informação importante..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        <small>Uma boa descrição aumenta as suas hipóteses de ser contratado</small>
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
                                   value="<?php echo htmlspecialchars($_POST['price_per_hour'] ?? ''); ?>"
                                   placeholder="25.00">
                            <small>Defina um preço competitivo mas justo</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="duration">Duração Estimada (minutos) *</label>
                            <input type="number" id="duration" name="duration" 
                                   min="15" max="480" required
                                   value="<?php echo htmlspecialchars($_POST['duration'] ?? ''); ?>"
                                   placeholder="120">
                            <small>Tempo médio necessário para completar o serviço</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="promotion">Desconto Promocional (%)</label>
                        <input type="number" id="promotion" name="promotion" 
                               min="0" max="50" 
                               value="<?php echo htmlspecialchars($_POST['promotion'] ?? '0'); ?>"
                               placeholder="0">
                        <small>Ofereça um desconto para atrair mais clientes (opcional)</small>
                    </div>
                    
                    <!-- Cálculo automático do preço -->
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
                
                <!-- Imagens -->
                <div class="form-section">
                    <h3><i class="fas fa-images"></i> Imagens do Serviço</h3>
                    
                    <div class="form-group">
                        <label for="images">Adicionar Imagens (opcional)</label>
                        <input type="file" id="images" name="images[]" multiple 
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <small>Adicione até 5 imagens para mostrar o seu trabalho (JPEG, PNG, GIF, WebP - máx. 5MB cada)</small>
                    </div>
                    
                    <div class="image-preview" id="image-preview"></div>
                </div>
                
                <!-- Configurações -->
                <div class="form-section">
                    <h3><i class="fas fa-cog"></i> Configurações</h3>
                    
                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" name="is_active" value="1" 
                                   <?php echo (($_POST['is_active'] ?? '1') == '1') ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Publicar serviço imediatamente
                        </label>
                        <small>Se desmarcado, o serviço ficará como rascunho</small>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Criar Serviço
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
    
    calculatePrices(); // Calcular inicialmente
});

// Preview de imagens (múltiplas)
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (index >= 5) return; // Máximo 5 imagens
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'preview-image-container';
            imgContainer.setAttribute('data-index', index);
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeImage(index);
            };
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            preview.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
    });
});

function removeImage(indexToRemove) {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();
    
    // Recriar a lista de arquivos sem o arquivo removido
    Array.from(fileInput.files).forEach((file, index) => {
        if (index !== indexToRemove) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    
    // Atualizar preview
    updateImagePreview();
}

function updateImagePreview() {
    const fileInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(fileInput.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'preview-image-container';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeImage(index);
            };
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            preview.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<!-- Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>