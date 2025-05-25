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
    $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
    header("Location: /Views/auth.php");
    exit();
}

// Verificar se foi passado um ID de utilizador via GET (para admins editarem outros perfis)
$editUserId = isset($_GET['id']) ? intval($_GET['id']) : getCurrentUserId();

// Verificar permissões de acesso
$currentUserId = getCurrentUserId();
$isAdmin = isUserAdmin();
$isOwnProfile = ($currentUserId == $editUserId);

// Só pode editar se for o próprio perfil OU se for admin
if (!canEditProfile($currentUserId, $editUserId, $isAdmin)) {
    $_SESSION['error'] = 'Não tem permissão para editar este perfil.';
    header("Location: /Views/profile/profile.php");
    exit();
}

// Obter os dados completos do utilizador a editar
$userData = getUserCompleteData($editUserId);

if (!$userData) {
    $_SESSION['error'] = 'Utilizador não encontrado.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Obter moedas disponíveis
$availableCurrencies = getAvailableCurrencies();

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$pageTitle = $isOwnProfile ? "Editar o Meu Perfil" : "Editar Perfil de " . htmlspecialchars($userData['name_']);
drawHeader("Handee - " . $pageTitle, ["/Styles/profile.css"]);
?>

<main>
    <section class="section-header">
        <h2><?php echo $pageTitle; ?></h2>
        <p><?php echo $isOwnProfile ? 'Atualize as suas informações pessoais' : 'Editar informações do utilizador (Admin)'; ?></p>
        <a href="/Views/profile/profile.php<?php echo $isOwnProfile ? '' : '?id=' . $editUserId; ?>" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Go Back" class="back-button">
        </a>
    </section>

    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-content">
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
                
                <form method="post" action="/Controllers/userController.php" class="edit-form">
                    <input type="hidden" name="action" value="update_profile">
                    <?php if (!$isOwnProfile): ?>
                        <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nome Completo</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['name_']); ?>" required>
                        <p class="form-hint">O seu nome completo como aparecerá no perfil</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                        <p class="form-hint">Este será o identificador único no site (mínimo 3 caracteres)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        <p class="form-hint">Email para login e comunicações importantes</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone_number">Telefone</label>
                        <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($userData['phone_number'] ?? ''); ?>" placeholder="+351 123 456 789">
                        <p class="form-hint">Número de telefone para contacto (mínimo 9 dígitos)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="currency">Moeda Preferida</label>
                        <select id="currency" name="currency">
                            <?php foreach ($availableCurrencies as $code => $name): ?>
                                <option value="<?php echo $code; ?>" 
                                        <?php echo ($userData['currency'] === $code) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="form-hint">Moeda utilizada para exibir preços e valores no site</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="web_link">Website Pessoal</label>
                        <input type="url" id="web_link" name="web_link" value="<?php echo htmlspecialchars($userData['web_link'] ?? ''); ?>" placeholder="https://exemplo.com">
                        <p class="form-hint">Link para website pessoal, portfolio ou perfil profissional</p>
                    </div>
                    
                    <?php if ($isOwnProfile): ?>
                    <div class="form-group">
                        <label for="night_mode">Modo Noturno</label>
                        <select id="night_mode" name="night_mode">
                            <option value="0" <?php echo !$userData['night_mode'] ? 'selected' : ''; ?>>Desativado</option>
                            <option value="1" <?php echo $userData['night_mode'] ? 'selected' : ''; ?>>Ativado</option>
                        </select>
                        <p class="form-hint">Ativar tema escuro para melhor experiência noturna</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="password">Nova Password</label>
                        <input type="password" id="password" name="password" minlength="6">
                        <p class="form-hint">Deixe em branco para manter a password atual (mínimo 6 caracteres)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Nova Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" minlength="6">
                        <p class="form-hint">Digite novamente a nova password para confirmação</p>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn-submit">Guardar Alterações</button>
                        <a href="/Views/profile/profile.php<?php echo $isOwnProfile ? '' : '?id=' . $editUserId; ?>" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
// Validar confirmação de password
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirm');
    const form = document.querySelector('.edit-form');
    
    function validatePasswords() {
        if (passwordField.value && confirmField.value) {
            if (passwordField.value !== confirmField.value) {
                confirmField.setCustomValidity('As passwords não coincidem');
            } else {
                confirmField.setCustomValidity('');
            }
        } else {
            confirmField.setCustomValidity('');
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmField.addEventListener('input', validatePasswords);
    
    form.addEventListener('submit', function(e) {
        validatePasswords();
        if (!confirmField.checkValidity()) {
            e.preventDefault();
            alert('Por favor, corrija os erros no formulário antes de submeter.');
        }
    });
});
</script>

<?php drawFooter(); ?>
