<?php
session_start();
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");

// Verificar se foi passado um ID de utilizador via GET
$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Se não foi passado ID e o utilizador está autenticado, mostrar o próprio perfil
if (!$profileUserId && isset($_SESSION['user_id'])) {
    $profileUserId = $_SESSION['user_id'];
}

// Se não há ID definido, redirecionar para a página principal
if (!$profileUserId) {
    header("Location: /Views/mainPage.php");
    exit();
}

// Obter os dados do utilizador a visualizar
$profileUser = getUserById($profileUserId);

if (!$profileUser) {
    header("Location: /Views/mainPage.php");
    exit();
}

// Verificar se é o próprio perfil
$isOwnProfile = isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $profileUserId);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$isLoggedIn = isset($_SESSION['user_id']);

// Processar logout se solicitado (apenas no próprio perfil)
if ($isOwnProfile && isset($_POST['logout'])) {
    logoutUser();
    header("Location: /Views/mainPage.php");
    exit();
}

// Processar ações de administrador
if ($isAdmin && !$isOwnProfile && isset($_POST['admin_action'])) {
    if ($_POST['admin_action'] === 'block') {
        blockUser($profileUserId);
    } elseif ($_POST['admin_action'] === 'unblock') {
        unblockUser($profileUserId);
    }
    // Recarregar os dados do utilizador
    $profileUser = getUserById($profileUserId);
}

drawHeader("Handee - Perfil de " . htmlspecialchars($profileUser->getName()), ["/Styles/profile.css"]);
?>

<main>
    <section class="section-header">
        <h2><?php echo $isOwnProfile ? 'O Meu Perfil' : 'Perfil de Utilizador'; ?></h2>
        <p><?php echo $isOwnProfile ? 'Gerencie as suas informações pessoais' : 'Visualize as informações públicas deste utilizador'; ?></p>
        <?php if (!$isOwnProfile): ?>
        <a href="javascript:history.back()" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Go Back" class="back-button">
        </a>
        <?php endif; ?>
    </section>

    <section class="profile-section">
        <div class="profile-container">
            <!-- Header do perfil -->
            <div class="profile-header"></div>

            <!-- Área principal do perfil -->
            <div class="profile-main">
                <div class="profile-image-container">
                    <div class="profile-image" style="background-image: url('/Images/site/header/genericProfile.png');"></div>
                </div>

                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($profileUser->getName()); ?></h1>
                    <p class="profile-username">@<?php echo htmlspecialchars($profileUser->getUsername()); ?></p>
                    
                    <div class="profile-badges">
                        <?php if ($profileUser->getIsAdmin()): ?>
                            <span class="badge badge-admin">Administrador</span>
                        <?php endif; ?>
                        
                        <?php if ($profileUser->getIsFreelancer()): ?>
                            <span class="badge badge-freelancer">Freelancer</span>
                        <?php endif; ?>
                        
                        <?php if ($profileUser->getIsBlocked()): ?>
                            <span class="badge badge-blocked">Bloqueado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-actions">
                    <?php if ($isOwnProfile): ?>
                        <!-- Ações para o próprio perfil -->
                        <a href="/Pages/editProfile.php" class="edit-profile-btn">Editar Perfil</a>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="logout" class="logout-btn">Terminar Sessão</button>
                        </form>
                    <?php elseif ($isAdmin && !$isOwnProfile): ?>
                        <!-- Ações de administrador -->
                        <form method="post" style="display: inline;">
                            <?php if ($profileUser->getIsBlocked()): ?>
                                <input type="hidden" name="admin_action" value="unblock">
                                <button type="submit" class="edit-profile-btn">Desbloquear</button>
                            <?php else: ?>
                                <input type="hidden" name="admin_action" value="block">
                                <button type="submit" class="logout-btn">Bloquear</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Conteúdo do perfil -->
            <div class="profile-content">
                <div class="profile-grid">
                    <!-- Informações de contato -->
                    <div class="profile-contact">
                        <h2>Informações <?php echo $isOwnProfile ? 'de Contato' : 'Públicas'; ?></h2>
                        
                        <?php if ($isOwnProfile): ?>
                        <!-- Mostrar email apenas no próprio perfil -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="/Images/site/footer/icon-email.png" alt="Email">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Email</p>
                                <p class="contact-value"><?php echo htmlspecialchars($profileUser->getEmail()); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($profileUser->getWebLink()): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="/Images/site/header/search-icon.png" alt="Website">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Website</p>
                                <p class="contact-value">
                                    <a href="<?php echo htmlspecialchars($profileUser->getWebLink()); ?>" target="_blank">
                                        <?php echo htmlspecialchars($profileUser->getWebLink()); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!$isOwnProfile && !$profileUser->getWebLink()): ?>
                        <p class="bio-empty">Não existem informações públicas disponíveis.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Biografia e detalhes -->
                    <div class="profile-details">
                        <h2>Sobre <?php echo $isOwnProfile ? 'Mim' : ''; ?></h2>
                        
                        <div class="bio-section">
                            <h3>Biografia</h3>
                            <div class="bio-content">
                                <?php if ($profileUser->getBio()): ?>
                                    <?php echo nl2br(htmlspecialchars($profileUser->getBio())); ?>
                                <?php else: ?>
                                    <p class="bio-empty">
                                        <?php echo $isOwnProfile ? 
                                            'Ainda não adicionou uma biografia...' : 
                                            'Este utilizador ainda não adicionou uma biografia...'; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($profileUser->getWebLink()): ?>
                        <div class="website-section">
                            <h3><?php echo $isOwnProfile ? 'O meu website' : 'Website'; ?></h3>
                            <a href="<?php echo htmlspecialchars($profileUser->getWebLink()); ?>" target="_blank" class="website-link">
                                <img src="/Images/site/header/search-icon.png" alt="Website">
                                Visitar website
                            </a>
                        </div>
                        <?php endif; ?>

                        <div class="register-info">
                            <p>Membro desde <?php echo date('d/m/Y', strtotime($profileUser->getRegisterDate())); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php drawFooter(); ?>