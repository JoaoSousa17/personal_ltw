<?php
// Verificar autenticação e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/orderPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para criar um serviço.';
    header("Location: /Views/auth.php");
    exit();
}

// Obter dados necessários
$categories = getAllCategories();

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$warning = $_SESSION['warning'] ?? '';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['warning']);

drawHeader("Handee - Criar Novo Serviço", ["/Styles/product.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2>Criar Novo Serviço</h2>
        <p>Publique o seu serviço e comece a ganhar dinheiro hoje mesmo</p>
        <a href="/Views/orders/myServices.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Voltar" class="back-button">
        </a>
    </section>

    <!-- Container principal -->
    <div class="create-service-container">
        <!-- Mensagens de feedback -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($warning): ?>
            <div class="alert alert-warning"><?php echo $warning; ?></div>
        <?php endif; ?>

        <!-- Formulário de criação -->
        <form method="post" action="/Controllers/serviceController.php" enctype="multipart/form-data" class="create-service-form">
            <input type="hidden" name="action" value="create_service">
            
            <div class="form-sections">
                <!-- Informações básicas -->
                <?php drawBasicInfoSection($categories, $_POST ?? []); ?>
                
                <!-- Preços e duração -->
                <?php drawPricingSection($_POST ?? []); ?>
                
                <!-- Seção de imagens -->
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
                <?php drawConfigSection(); ?>
            </div>
            
            <!-- Botões de ação -->
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

<!-- Scripts necessários -->
<script src="/Scripts/orders.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
