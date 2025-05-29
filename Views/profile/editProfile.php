<?php
// Definir o caminho base absoluto
define('BASE_PATH', dirname(dirname(__DIR__)));

require_once(BASE_PATH . "/Utils/session.php");
require_once(BASE_PATH . "/Templates/common_elems.php");
require_once(BASE_PATH . "/Controllers/userController.php");
require_once(BASE_PATH . "/Controllers/adressController.php");

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

// Obter dados da morada do utilizador
$userAddress = getUserAddress($editUserId);

// Obter moedas disponíveis
$availableCurrencies = getAvailableCurrencies();

// Obter distritos portugueses
$portugueseDistricts = getPortugueseDistricts();

// Obter URL da foto de perfil atual
$currentPhotoUrl = getProfilePhotoUrl($editUserId);

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
                
                <!-- Seção de Foto de Perfil -->
                <div class="photo-section">
                    <h3>Foto de Perfil</h3>
                    <div class="current-photo">
                        <?php if ($currentPhotoUrl): ?>
                            <img id="profile-photo-preview" src="<?php echo htmlspecialchars($currentPhotoUrl); ?>" 
                                 alt="Foto de perfil atual" 
                                 title="Clique para alterar ou arraste uma nova imagem">
                        <?php else: ?>
                            <img id="profile-photo-preview" src="/Images/site/header/genericProfile.png" 
                                 alt="Foto de perfil padrão" 
                                 title="Clique para adicionar uma foto ou arraste uma imagem">
                        <?php endif; ?>
                    </div>
                    
                    <div class="photo-actions">
                        <label for="photo-upload" class="btn-photo">
                            <i class="fas fa-camera"></i>
                            Escolher Nova Foto
                        </label>
                        <input type="file" id="photo-upload" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                        
                        <?php if ($currentPhotoUrl): ?>
                            <button type="button" id="delete-photo-btn" class="btn-delete">
                                <i class="fas fa-trash"></i>
                                Eliminar Foto
                            </button>
                        <?php else: ?>
                            <button type="button" id="delete-photo-btn" class="btn-delete" style="display: none;">
                                <i class="fas fa-trash"></i>
                                Eliminar Foto
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <p class="photo-hint">
                        <i class="fas fa-info-circle"></i>
                        Formatos aceites: JPEG, PNG, GIF, WebP. Tamanho máximo: 5MB.<br>
                        Dica: Clique na foto ou arraste uma imagem para fazer upload.
                    </p>
                    
                    <!-- Área para mensagens da foto -->
                    <div id="photo-message"></div>
                </div>
                
                <!-- Formulário de Dados Pessoais -->
                <div class="form-section">
                    <h3>Informações Pessoais</h3>
                    <form method="post" action="/Controllers/userController.php" class="edit-form">
                        <input type="hidden" name="action" value="update_profile">
                        <?php if (!$isOwnProfile): ?>
                            <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
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
                        </div>
                        
                        <div class="form-row">
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
                        </div>
                        
                        <div class="form-row">
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
                        
                        <div class="form-row">
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
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">Guardar Alterações</button>
                            <a href="/Views/profile/profile.php<?php echo $isOwnProfile ? '' : '?id=' . $editUserId; ?>" class="btn-cancel">Cancelar</a>
                        </div>
                    </form>
                </div>
                
                <!-- Formulário de Morada -->
                <div class="form-section address-section">
                    <div class="section-header-with-action">
                        <h3>Morada</h3>
                        <?php if ($userAddress): ?>
                            <form method="post" action="/Controllers/adressController.php" style="display: inline;">
                                <input type="hidden" name="action" value="delete_address">
                                <?php if (!$isOwnProfile): ?>
                                    <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn-delete-small" onclick="return confirm('Tem certeza que deseja remover a morada?')">
                                    <i class="fas fa-trash"></i> Remover Morada
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <p class="section-description">
                        <?php echo $userAddress ? 'Atualize a sua morada atual:' : 'Adicione uma morada à sua conta:'; ?>
                    </p>
                    
                    <form method="post" action="/Controllers/adressController.php" class="edit-form address-form">
                        <input type="hidden" name="action" value="update_address">
                        <?php if (!$isOwnProfile): ?>
                            <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group flex-2">
                                <label for="street">Rua *</label>
                                <input type="text" id="street" name="street" 
                                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getStreet()) : ''; ?>" 
                                       placeholder="Ex: Rua da Constituição" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="door_num">Nº da Porta *</label>
                                <input type="text" id="door_num" name="door_num" 
                                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getDoorNum()) : ''; ?>" 
                                       placeholder="Ex: 123" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="floor">Andar</label>
                                <input type="text" id="floor" name="floor" 
                                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getFloor() ?? '') : ''; ?>" 
                                       placeholder="Ex: 2º, R/C">
                            </div>
                            
                            <div class="form-group flex-2">
                                <label for="extra">Informações Adicionais</label>
                                <input type="text" id="extra" name="extra" 
                                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getExtra() ?? '') : ''; ?>" 
                                       placeholder="Ex: Apartamento B, Bloco 2">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="district">Distrito *</label>
                                <select id="district" name="district" required>
                                    <option value="">Selecione o distrito</option>
                                    <?php foreach ($portugueseDistricts as $district): ?>
                                        <option value="<?php echo $district; ?>" 
                                                <?php echo ($userAddress && $userAddress->getDistrict() === $district) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($district); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="municipality">Município *</label>
                                <input type="text" id="municipality" name="municipality" 
                                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getMunicipality()) : ''; ?>" 
                                       placeholder="Ex: Porto" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code">Código Postal *</label>
                            <input type="text" id="zip_code" name="zip_code" 
                                   value="<?php echo $userAddress ? htmlspecialchars($userAddress->getZipCode()) : ''; ?>" 
                                   placeholder="XXXX-XXX" pattern="\d{4}-\d{3}" required>
                            <p class="form-hint">Formato: XXXX-XXX (ex: 4000-001)</p>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">
                                <?php echo $userAddress ? 'Atualizar Morada' : 'Adicionar Morada'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Definir dados para o script da foto
window.profilePageData = {
    isOwnProfile: <?php echo $isOwnProfile ? 'true' : 'false'; ?>,
    targetUserId: <?php echo $editUserId; ?>,
    currentPhotoUrl: '<?php echo $currentPhotoUrl ?: "/Images/site/header/genericProfile.png"; ?>'
};

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
    
    // Formatação automática do código postal
    const zipCodeInput = document.getElementById('zip_code');
    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 7);
            }
            e.target.value = value;
        });
    }
    
    // Adicionar efeitos visuais aos botões
    const buttons = document.querySelectorAll('.btn-photo, .btn-delete, .btn-submit, .btn-cancel');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

<!-- Incluir Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Script para gestão de fotos -->
<script src="/Scripts/profilePhoto.js"></script>

<?php drawFooter(); ?>
