<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

// Verificar se o utilizador é administrador
if (!isUserAdmin()) {
    $_SESSION['error'] = 'Não tem permissão para aceder ao painel de administração.';
    header("Location: /Views/mainPage.php");
    exit();
}

$successMessage = '';
$errorMessage = '';

// Processar remoção de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_category'])) {
    $categoryId = $_POST['remove_category'];
    
    $result = deleteCategory($categoryId);
    
    if ($result['success']) {
        $successMessage = $result['message'];
    } else {
        $errorMessage = $result['message'];
    }
    
    // Redirecionar para evitar reenvio do formulário
    $_SESSION['category_success'] = $successMessage;
    $_SESSION['category_error'] = $errorMessage;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Processar adição de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    error_log("=== PROCESSAMENTO CATEGORIA ===");
    error_log("POST recebido: " . print_r($_POST, true));
    error_log("FILES recebido: " . print_r($_FILES, true));
    
    $categoryName = trim($_POST['category_name']);
    
    // Verificar se foi enviado um ficheiro de imagem
    if (!isset($_FILES['category_image']) || $_FILES['category_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errorMessage = 'Por favor, selecione uma imagem para a categoria.';
        error_log("Erro: Nenhuma imagem selecionada");
    } else {
        error_log("A processar categoria: " . $categoryName);
        $result = createCategory($categoryName, $_FILES['category_image']);
        error_log("Resultado: " . print_r($result, true));
        
        if ($result['success']) {
            $successMessage = $result['message'];
        } else {
            $errorMessage = $result['message'];
        }
    }
    
    // Redirecionar para evitar reenvio do formulário
    $_SESSION['category_success'] = $successMessage;
    $_SESSION['category_error'] = $errorMessage;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Verificar mensagens da sessão
if (isset($_SESSION['category_success']) && !empty($_SESSION['category_success'])) {
    $successMessage = $_SESSION['category_success'];
    unset($_SESSION['category_success']);
}

if (isset($_SESSION['category_error']) && !empty($_SESSION['category_error'])) {
    $errorMessage = $_SESSION['category_error'];
    unset($_SESSION['category_error']);
}

// Obter todas as categorias - usando a função do controller
$categories = getAllCategories();

drawHeader("Handee - Gestão de Categorias", ["/Styles/admin.css"]);
?>

<main class="category-management-container">
    <?php drawSectionHeader("Gestão de Categorias", "Visualize, adicione e remova categorias do sistema", true); ?>

    <!-- Mensagens de sucesso/erro -->
    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($errorMessage); ?>
    </div>
    <?php endif; ?>

    <?php drawSectionTitle('Categorias Existentes') ?>

    <!-- Tabela de categorias -->
    <?php drawCategoriesTable($categories) ?>
    
    <?php drawSectionTitle('Adicionar Nova Categoria') ?>

    <!-- Formulário para adicionar categoria -->
    <?php drawAddCategoryForm() ?> 
</main>

<!-- JavaScript para melhorar a experiência do utilizador -->
<script src="/Scripts/categoryManagement.js"></script>

<?php drawFooter(); ?>