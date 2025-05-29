<?php
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/categoriesController.php");

// Verificar autenticação e permissões
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

if (!isUserAdmin()) {
    $_SESSION['error'] = 'Não tem permissão para aceder ao painel de administração.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Processar ações do formulário
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
    
    if (!isset($_FILES['category_image']) || $_FILES['category_image']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Erro: Deve selecionar uma imagem para a categoria.';
        $messageType = 'error';
    } else {
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

// Obter categorias existentes
$categories = getAllCategories();

drawHeader("Handee - Gestão de Categorias", ["/Styles/admin.css", "/Styles/users.css", "/Styles/category_management.css"]);
?>

<main class="category-management-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Gestão de Categorias", "Visualize, adicione e remova categorias do sistema", true); ?>

    <!-- Mensagens de feedback -->
    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    
    <!-- Categorias existentes -->
    <?php drawSectionTitle('Categorias Existentes') ?>
    <?php drawCategoriesTable($categories) ?>
    
    <!-- Adicionar nova categoria -->
    <?php drawSectionTitle('Adicionar Nova Categoria') ?>
    <?php drawAddCategoryForm() ?> 
</main>

<?php drawFooter(); ?>
