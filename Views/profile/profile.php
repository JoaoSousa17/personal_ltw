<?php
// Verificar autenticação e incluir dependências
require_once(dirname(dirname(__DIR__)) . "/Utils/session.php");
require_once(dirname(dirname(__DIR__)) . "/Templates/common_elems.php");
require_once(dirname(dirname(__DIR__)) . "/Templates/profilePages_elems.php");
require_once(dirname(dirname(__DIR__)) . "/Controllers/userController.php");

// Iniciar sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder ao perfil.';
    header("Location: /Views/auth.php");
    exit();
}

// Determinar qual perfil visualizar
$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : getCurrentUserId();

// Obter dados do utilizador
$userData = getUserCompleteData($profileUserId);
if (!$userData) {
    $_SESSION['error'] = 'Utilizador não encontrado.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Verificar permissões e estado
$isOwnProfile = (getCurrentUserId() == $profileUserId);
$isAdmin = isUserAdmin();

// Processar logout (apenas no próprio perfil)
if ($isOwnProfile && isset($_POST['logout'])) {
    header("Location: /Controllers/authController.php?action=logout");
    exit();
}

// Processar ações de administrador
if ($isAdmin && !$isOwnProfile && isset($_POST['admin_action'])) {
    if ($_POST['admin_action'] === 'block') {
        blockUser($profileUserId);
    } elseif ($_POST['admin_action'] === 'unblock') {
        unblockUser($profileUserId);
    }
    $userData = getUserCompleteData($profileUserId);
}

// Obter dados adicionais
$availableCurrencies = getAvailableCurrencies();
$currencyName = $availableCurrencies[$userData['currency']] ?? 'Euro (€)';
$profilePhotoUrl = getProfilePhotoUrl($profileUserId);

drawHeader("Handee - Perfil de " . htmlspecialchars($userData['name_']), ["/Styles/profile.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2><?php echo $isOwnProfile ? 'O Meu Perfil' : 'Perfil de Utilizador'; ?></h2>
        <p><?php echo $isOwnProfile ? 'Gerencie as suas informações pessoais' : 'Visualize as informações públicas deste utilizador'; ?></p>
        <?php if (!$isOwnProfile): ?>
        <a href="javascript:history.back()" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Go Back" class="back-button">
        </a>
        <?php endif; ?>
    </section>

    <!-- Container do perfil -->
    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header"></div>

            <!-- Área principal do perfil -->
            <?php drawProfileMain($userData, $profilePhotoUrl, $isOwnProfile, $isAdmin, $profileUserId); ?>

            <!-- Conteúdo do perfil -->
            <div class="profile-content">
                <div class="profile-grid">
                    <!-- Informações de contato -->
                    <?php drawContactInfo($userData, $isOwnProfile, $currencyName); ?>

                    <!-- Detalhes do perfil -->
                    <?php drawProfileDetails($userData, $isOwnProfile, $currencyName); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php drawFooter(); ?>
