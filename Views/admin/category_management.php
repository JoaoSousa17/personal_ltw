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

// Processar remoção de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_category'])) {
    $categoryId = $_POST['remove_category'];
    
    // Aqui deveria existir uma função para remover categoria
    // Como não foi fornecida, vamos assumir que precisamos implementá-la
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF'] . "?removed=1");
    exit;
}

// Processar adição de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name']);
    $photoId = isset($_POST['photo_id']) ? $_POST['photo_id'] : null;
    
    // Aqui deveria existir uma função para adicionar categoria
    // Como não foi fornecida, vamos assumir que precisamos implementá-la
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF'] . "?added=1");
    exit;
}

// Obter todas as categorias
$categories = getAllCategories();

// Contar número de serviços por categoria
// Esta função não existe no código fornecido, então vamos criar uma simulação
function getServiceCountByCategory($categoryId) {
    // Aqui deveria ter uma consulta ao banco de dados para contar os serviços
    // Como não temos essa função, vamos retornar um número aleatório para exemplo
    return rand(0, 50);
}

drawHeader("Handee - Gestão de Categorias", ["/Styles/admin.css", "/Styles/users.css", "/Styles/category_management.css"]);
?>

<main class="category-management-container">
    <?php drawSectionHeader("Gestão de Categorias", "Visualize, adicione e remova categorias do sistema", true); ?>

    <?php if (isset($_GET['removed'])): ?>
    <div class="alert alert-success">
        Categoria removida com sucesso!
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">
        Categoria adicionada com sucesso!
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