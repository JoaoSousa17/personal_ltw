<?php
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");

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

// Processar promoção de usuário para admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_id'])) {
    $userId = $_POST['promote_id'];
    $user = getUserById($userId);
    
    if ($user) {
        $updateData = ['is_admin' => true];
        $result = updateUser($userId, $updateData);
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?promoted=1");
        exit;
    }
}

// Obter dados dos administradores
$admins = [];
$allUsers = getAllUsers();
foreach ($allUsers as $user) {
    if ($user->getIsAdmin()) {
        $admins[] = $user;
    }
}

// Processar busca de utilizadores
$searchTerm = '';
$searchResults = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchResults = searchUsers($searchTerm);
}

drawHeader("Handee - Controle de Administradores", ["/Styles/admin.css", "/Styles/users.css", "/Styles/admin_control.css"]);
?>

<main class="admin-control-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Controle de Administradores", "Gerencie os usuários administradores do sistema", true); ?>
    
    <!-- Mensagem de sucesso -->
    <?php if (isset($_GET['promoted'])): ?>
    <div class="alert alert-success">
        Usuário promovido a administrador com sucesso!
    </div>
    <?php endif; ?>

    <!-- Tabela de administradores atuais -->
    <?php drawSectionTitle('Administradores Atuais') ?>
    <?php drawAdminTable($admins) ?>

    <!-- Seção para promover usuários -->
    <?php drawSectionTitle('Promover Usuário a Administrador') ?>
    <?php drawSearchBar($searchTerm) ?>
    <?php drawSearchResultTable($searchTerm, $searchResults) ?>
</main>

<?php drawFooter(); ?>
