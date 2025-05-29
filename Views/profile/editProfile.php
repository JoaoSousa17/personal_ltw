<?php
// Verificar autenticação e incluir dependências
require_once(dirname(dirname(__DIR__)) . "/Utils/session.php");
require_once(dirname(dirname(__DIR__)) . "/Templates/common_elems.php");
require_once(dirname(dirname(__DIR__)) . "/Templates/profilePages_elems.php");
require_once(dirname(dirname(__DIR__)) . "/Controllers/userController.php");
require_once(dirname(dirname(__DIR__)) . "/Controllers/adressController.php");

// Iniciar sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

// Determinar utilizador a editar
$editUserId = isset($_GET['id']) ? intval($_GET['id']) : getCurrentUserId();
$currentUserId = getCurrentUserId();
$isAdmin = isUserAdmin();
$isOwnProfile = ($currentUserId == $editUserId);

// Verificar permissões de edição
if (!canEditProfile($currentUserId, $editUserId, $isAdmin)) {
    $_SESSION['error'] = 'Não tem permissão para editar este perfil.';
    header("Location: /Views/profile/profile.php");
    exit();
}

// Obter dados do utilizador
$userData = getUserCompleteData($editUserId);
if (!$userData) {
    $_SESSION['error'] = 'Utilizador não encontrado.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Obter dados adicionais
$userAddress = getUserAddress($editUserId);
$availableCurrencies = getAvailableCurrencies();
$portugueseDistricts = getPortugueseDistricts();
$currentPhotoUrl = getProfilePhotoUrl($editUserId);

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$pageTitle = $isOwnProfile ? "Editar o Meu Perfil" : "Editar Perfil de " . htmlspecialchars($userData['name_']);
drawHeader("Handee - " . $pageTitle, ["/Styles/profile.css"]);
?>

<main>
    <!-- Cabeçalho da página -->
    <section class="section-header">
        <h2><?php echo $pageTitle; ?></h2>
        <p><?php echo $isOwnProfile ? 'Atualize as suas informações pessoais' : 'Editar informações do utilizador (Admin)'; ?></p>
        <a href="/Views/profile/profile.php<?php echo $isOwnProfile ? '' : '?id=' . $editUserId; ?>" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Go Back" class="back-button">
        </a>
    </section>

    <!-- Container principal -->
    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-content">
                <!-- Mensagens de feedback -->
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <?php if (!$isOwnProfile && $isAdmin): ?>
                    <div class="alert" style="background-color: #e3f2fd; color: #1976d2; border: 1px solid #1976d2;">
                        <strong>Modo Administrador:</strong> Está a editar o perfil de outro utilizador.
                    </div>
                <?php endif; ?>
                
                <!-- Seção de foto de perfil -->
                <?php drawPhotoSection($currentPhotoUrl); ?>
                
                <!-- Formulário de dados pessoais -->
                <?php drawPersonalInfoForm($userData, $availableCurrencies, $isOwnProfile, $editUserId); ?>
                
                <!-- Formulário de morada -->
                <?php drawAddressForm($userAddress, $portugueseDistricts, $isOwnProfile, $editUserId); ?>
            </div>
        </div>
    </section>
</main>

<!-- Scripts necessários -->
<script>
// Configurar dados para o script da foto
setupProfilePageData(<?php echo $isOwnProfile ? 'true' : 'false'; ?>, <?php echo $editUserId; ?>, '<?php echo $currentPhotoUrl ?: "/Images/site/header/genericProfile.png"; ?>');
</script>

<script src="/Scripts/profile.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
