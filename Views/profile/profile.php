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

// Obter URL da foto de perfil
$profilePhotoUrl = getProfilePhotoUrl($profileUserId);

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
                    <div class="profile-image" style="background-image: url('<?php echo $profilePhotoUrl ?: '/Images/site/header/genericProfile.png'; ?>'); background-size: cover; background-position: center;">
                        <?php if (!$profilePhotoUrl): ?>
                            <!-- Mostrar ícone padrão se não houver foto -->
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 3rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($userData['name_']); ?></h1>
                    <p class="profile-username">@<?php echo htmlspecialchars($userData['username']); ?></p>
                    
                    <div class="profile-badges">
                        <?php if ($userData['is_admin']): ?>
                            <span class="badge badge-admin">
                                <i class="fas fa-crown" style="margin-right: 5px;"></i>
                                Administrador
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($userData['is_freelancer']): ?>
                            <span class="badge badge-freelancer">
                                <i class="fas fa-briefcase" style="margin-right: 5px;"></i>
                                Freelancer
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($userData['is_blocked']): ?>
                            <span class="badge badge-blocked">
                                <i class="fas fa-ban" style="margin-right: 5px;"></i>
                                Bloqueado
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-actions">
                    <?php if ($isOwnProfile): ?>
                        <!-- Ações para o próprio perfil -->
                        <a href="/Views/profile/editProfile.php" class="edit-profile-btn">
                            <i class="fas fa-edit" style="margin-right: 5px;"></i>
                            Editar Perfil
                        </a>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="logout" class="logout-btn">
                                <i class="fas fa-sign-out-alt" style="margin-right: 5px;"></i>
                                Terminar Sessão
                            </button>
                        </form>
                    <?php elseif ($isAdmin && !$isOwnProfile): ?>
                        <!-- Ações de administrador -->
                        <form method="post" style="display: inline;">
                            <?php if ($userData['is_blocked']): ?>
                                <input type="hidden" name="admin_action" value="unblock">
                                <button type="submit" class="edit-profile-btn">
                                    <i class="fas fa-unlock" style="margin-right: 5px;"></i>
                                    Desbloquear
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="admin_action" value="block">
                                <button type="submit" class="logout-btn" onclick="return confirm('Tem certeza que deseja bloquear este utilizador?')">
                                    <i class="fas fa-ban" style="margin-right: 5px;"></i>
                                    Bloquear
                                </button>
                            <?php endif; ?>
                        </form>
                        <a href="/Views/profile/editProfile.php?id=<?php echo $profileUserId; ?>" class="edit-profile-btn">
                            <i class="fas fa-edit" style="margin-right: 5px;"></i>
                            Editar Perfil
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Conteúdo do perfil -->
            <div class="profile-content">
                <div class="profile-grid">
                    <!-- Informações de contato -->
                    <div class="profile-contact">
                        <h2>
                            <i class="fas fa-address-card" style="margin-right: 10px; color: var(--primary-color);"></i>
                            Informações <?php echo $isOwnProfile ? 'de Contato' : 'Públicas'; ?>
                        </h2>
                        
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
                                <i class="fas fa-coins" style="color: white; font-size: 20px;"></i>
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
                                <i class="fas fa-globe" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Website</p>
                                <p class="contact-value">
                                    <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                        <i class="fas fa-external-link-alt" style="margin-right: 5px;"></i>
                                        <?php echo htmlspecialchars($userData['web_link']); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Mostrar telefone para outros utilizadores se disponível -->
                        <?php if (!$isOwnProfile && !empty($userData['phone_number'])): ?>
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
                        
                        <?php if (!$isOwnProfile && !$userData['web_link'] && empty($userData['phone_number'])): ?>
                        <div class="bio-empty" style="text-align: center; padding: 20px;">
                            <i class="fas fa-info-circle" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                            <p>Não existem informações públicas disponíveis.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informações adicionais -->
                    <div class="profile-details">
                        <h2>
                            <i class="fas fa-info-circle" style="margin-right: 10px; color: var(--primary-color);"></i>
                            Informações Adicionais
                        </h2>
                        
                        <div class="bio-section">
                            <h3>
                                <i class="fas fa-user-cog" style="margin-right: 8px; color: var(--primary-color);"></i>
                                Detalhes da Conta
                            </h3>
                            <div class="bio-content">
                                <p>
                                    <i class="fas fa-calendar-alt" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($userData['creation_date'])); ?>
                                </p>
                                
                                <p>
                                    <i class="fas fa-user-tag" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Tipo de conta:</strong> 
                                    <?php 
                                    $accountTypes = [];
                                    if ($userData['is_admin']) $accountTypes[] = 'Administrador';
                                    if ($userData['is_freelancer']) $accountTypes[] = 'Freelancer';
                                    if (empty($accountTypes)) $accountTypes[] = 'Utilizador Regular';
                                    echo implode(', ', $accountTypes);
                                    ?>
                                </p>
                                
                                <?php if ($isOwnProfile): ?>
                                <p>
                                    <i class="fas fa-coins" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Moeda preferida:</strong> <?php echo htmlspecialchars($currencyName); ?>
                                </p>
                                <p>
                                    <i class="fas fa-moon" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Modo noturno:</strong> 
                                    <?php echo $userData['night_mode'] ? 'Ativado' : 'Desativado'; ?>
                                </p>
                                <?php endif; ?>
                                
                                <?php if ($userData['is_blocked']): ?>
                                <p style="color: #dc3545;">
                                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                                    <strong>Estado:</strong> Conta bloqueada
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($userData['web_link']): ?>
                        <div class="website-section">
                            <h3>
                                <i class="fas fa-globe" style="margin-right: 8px; color: var(--primary-color);"></i>
                                <?php echo $isOwnProfile ? 'O meu website' : 'Website'; ?>
                            </h3>
                            <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank" class="website-link">
                                <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i>
                                Visitar website
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Seção de estatísticas (se for freelancer) -->
                        <?php if ($userData['is_freelancer']): ?>
                        <div class="stats-section" style="margin-top: 30px;">
                            <h3>
                                <i class="fas fa-chart-bar" style="margin-right: 8px; color: var(--primary-color);"></i>
                                Estatísticas
                            </h3>
                            <div class="bio-content">
                                <p>
                                    <i class="fas fa-briefcase" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Serviços publicados:</strong> 
                                    <?php 
                                    // Aqui poderíamos fazer uma query para contar os serviços
                                    echo "Em desenvolvimento";
                                    ?>
                                </p>
                                <p>
                                    <i class="fas fa-star" style="margin-right: 8px; color: var(--primary-color);"></i>
                                    <strong>Avaliação média:</strong> 
                                    <?php 
                                    // Aqui poderíamos calcular a avaliação média
                                    echo "Em desenvolvimento";
                                    ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php drawFooter(); ?>