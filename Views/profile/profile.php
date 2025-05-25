<?php
// Definir o caminho base absoluto
define('BASE_PATH', dirname(dirname(__DIR__)));

require_once(BASE_PATH . "/Utils/session.php");
require_once(BASE_PATH . "/Templates/common_elems.php");
require_once(BASE_PATH . "/Controllers/userController.php");

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o utilizador está autenticado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para aceder ao perfil.';
    header("Location: /Views/auth.php");
    exit();
}

// Verificar se foi passado um ID de utilizador via GET
$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Se não foi passado ID, mostrar o próprio perfil
if (!$profileUserId) {
    $profileUserId = getCurrentUserId();
}

// Obter os dados completos do utilizador a visualizar
$userData = getUserCompleteData($profileUserId);

if (!$userData) {
    $_SESSION['error'] = 'Utilizador não encontrado.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Verificar se é o próprio perfil
$isOwnProfile = (getCurrentUserId() == $profileUserId);
$isAdmin = isUserAdmin();

// Processar logout se solicitado (apenas no próprio perfil)
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
    // Recarregar os dados do utilizador
    $userData = getUserCompleteData($profileUserId);
}

// Obter moedas disponíveis para mostrar o nome completo
$availableCurrencies = getAvailableCurrencies();
$currencyName = $availableCurrencies[$userData['currency']] ?? 'Euro (€)';

drawHeader("Handee - Perfil de " . htmlspecialchars($userData['name_']), ["/Styles/profile.css"]);
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
                    <h1><?php echo htmlspecialchars($userData['name_']); ?></h1>
                    <p class="profile-username">@<?php echo htmlspecialchars($userData['username']); ?></p>
                    
                    <div class="profile-badges">
                        <?php if ($userData['is_admin']): ?>
                            <span class="badge badge-admin">Administrador</span>
                        <?php endif; ?>
                        
                        <?php if ($userData['is_freelancer']): ?>
                            <span class="badge badge-freelancer">Freelancer</span>
                        <?php endif; ?>
                        
                        <?php if ($userData['is_blocked']): ?>
                            <span class="badge badge-blocked">Bloqueado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-actions">
                    <?php if ($isOwnProfile): ?>
                        <!-- Ações para o próprio perfil -->
                        <a href="/Views/profile/editProfile.php" class="edit-profile-btn">Editar Perfil</a>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="logout" class="logout-btn">Terminar Sessão</button>
                        </form>
                    <?php elseif ($isAdmin && !$isOwnProfile): ?>
                        <!-- Ações de administrador -->
                        <form method="post" style="display: inline;">
                            <?php if ($userData['is_blocked']): ?>
                                <input type="hidden" name="admin_action" value="unblock">
                                <button type="submit" class="edit-profile-btn">Desbloquear</button>
                            <?php else: ?>
                                <input type="hidden" name="admin_action" value="block">
                                <button type="submit" class="logout-btn">Bloquear</button>
                            <?php endif; ?>
                        </form>
                        <a href="/Views/profile/editProfile.php?id=<?php echo $profileUserId; ?>" class="edit-profile-btn">Editar Perfil</a>
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
                                <img src="/Images/site/footer/mail.png" alt="Email">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Email</p>
                                <p class="contact-value"><?php echo htmlspecialchars($userData['email']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Mostrar telefone se disponível -->
                        <?php if (!empty($userData['phone_number'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="/Images/site/footer/phone.png" alt="Telefone">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Telefone</p>
                                <p class="contact-value"><?php echo htmlspecialchars($userData['phone_number']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Mostrar moeda preferida apenas no próprio perfil -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="/Images/site/header/search-icon.png" alt="Moeda">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Moeda Preferida</p>
                                <p class="contact-value"><?php echo htmlspecialchars($currencyName); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($userData['web_link']): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <img src="/Images/site/header/search-icon.png" alt="Website">
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Website</p>
                                <p class="contact-value">
                                    <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($userData['web_link']); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!$isOwnProfile && !$userData['web_link'] && empty($userData['phone_number'])): ?>
                        <p class="bio-empty">Não existem informações públicas disponíveis.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Informações adicionais -->
                    <div class="profile-details">
                        <h2>Informações Adicionais</h2>
                        
                        <div class="bio-section">
                            <h3>Detalhes da Conta</h3>
                            <div class="bio-content">
                                <p><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($userData['creation_date'])); ?></p>
                                
                                <?php if (!empty($userData['phone_number']) && !$isOwnProfile): ?>
                                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($userData['phone_number']); ?></p>
                                <?php endif; ?>
                                
                                <p><strong>Tipo de conta:</strong> 
                                    <?php 
                                    $accountTypes = [];
                                    if ($userData['is_admin']) $accountTypes[] = 'Administrador';
                                    if ($userData['is_freelancer']) $accountTypes[] = 'Freelancer';
                                    if (empty($accountTypes)) $accountTypes[] = 'Utilizador Regular';
                                    echo implode(', ', $accountTypes);
                                    ?>
                                </p>
                                
                                <?php if ($isOwnProfile): ?>
                                <p><strong>Moeda preferida:</strong> <?php echo htmlspecialchars($currencyName); ?></p>
                                <p><strong>Modo noturno:</strong> <?php echo $userData['night_mode'] ? 'Ativado' : 'Desativado'; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($userData['web_link']): ?>
                        <div class="website-section">
                            <h3><?php echo $isOwnProfile ? 'O meu website' : 'Website'; ?></h3>
                            <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank" class="website-link">
                                <img src="/Images/site/header/search-icon.png" alt="Website">
                                Visitar website
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php drawFooter(); ?>