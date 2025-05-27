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

$message = '';
$messageType = '';

// Processar remoção de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_category'])) {
    $categoryId = $_POST['remove_category'];
    
    $result = deleteCategory($categoryId);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}

// Processar adição de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name']);
    
    // Verificar se o ficheiro foi enviado
    if (!isset($_FILES['category_image']) || $_FILES['category_image']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Erro: Deve selecionar uma imagem para a categoria.';
        $messageType = 'error';
    } else {
        // Tentar adicionar a categoria
        $result = addCategory($categoryName, $_FILES['category_image']);
        
        if ($result) {
            $message = 'Categoria adicionada com sucesso!';
            $messageType = 'success';
        } else {
            $message = 'Erro ao adicionar categoria. Verifique se o nome não está duplicado e se a imagem é válida.';
            $messageType = 'error';
        }
    }
}

// Obter todas as categorias
$categories = getAllCategories();

drawHeader("Handee - Gestão de Categorias", ["/Styles/admin.css", "/Styles/users.css", "/Styles/category_management.css"]);
?>

<main class="category-management-container">
    <?php drawSectionHeader("Gestão de Categorias", "Visualize, adicione e remova categorias do sistema", true); ?>

    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    
    <?php drawSectionTitle('Categorias Existentes') ?>

    <!-- Tabela de categorias -->
    <?php drawCategoriesTable($categories) ?>
    
    <?php drawSectionTitle('Adicionar Nova Categoria') ?>

    <!-- Formulário para adicionar categoria -->
    <?php drawAddCategoryForm() ?> 
</main>

<?php drawFooter(); ?>