<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");

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

// Processar promoção de usuário para admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_id'])) {
    $userId = intval($_POST['promote_id']);
    
    $result = promoteUserToAdmin($userId);
    
    if ($result) {
        $_SESSION['success'] = 'Usuário promovido a administrador com sucesso!';
        header("Location: " . $_SERVER['PHP_SELF'] . "?promoted=1");
        exit;
    } else {
        $_SESSION['error'] = 'Erro ao promover usuário a administrador.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Obter todos os usuários administradores - usando as funções do controller
$admins = [];
$allUsers = getAllUsers();
foreach ($allUsers as $user) {
    if ($user->getIsAdmin()) {
        $admins[] = $user;
    }
}

// Verificar se há uma busca
$searchTerm = '';
$searchResults = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchResults = searchUsers($searchTerm);
}

drawHeader("Handee - Controle de Administradores", ["/Styles/admin.css", "/Styles/users.css", "/Styles/admin_control.css"]);
?>
<main class="admin-control-container">
    <?php drawSectionHeader("Controle de Administradores", "Gerencie os usuários administradores do sistema", true); ?>
    
    <?php if (isset($_GET['promoted'])): ?>
    <div class="alert alert-success">
        Usuário promovido a administrador com sucesso!
    </div>
    <?php endif; ?>

    <!-- Tabela de administradores -->
    <?php drawSectionTitle('Administradores Atuais') ?>
    <?php drawAdminTable($admins) ?>

    <!-- Seção para adicionar novos administradores -->
    <?php drawSectionTitle('Promover Usuário a Administrador') ?>

    <!-- Barra de pesquisa -->
    <?php drawSearchBar($searchTerm) ?>

    <!-- Resultados da pesquisa -->
    <?php drawSearchResultTable($searchTerm, $searchResults) ?>
</main>
<?php drawFooter(); ?>