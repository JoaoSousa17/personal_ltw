<?php
require_once(dirname(__FILE__)."/../Utils/session.php");
require_once(dirname(__FILE__)."/../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../Controllers/ReasonBlockController.php");
require_once(dirname(__FILE__)."/../Controllers/UnblockAppealController.php");
require_once(dirname(__FILE__)."/../Controllers/userController.php");

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

$currentUserId = getCurrentUserId();

// Verificar se o utilizador está bloqueado
$currentUser = getUserById($currentUserId);
if (!$currentUser || !$currentUser->getIsBlocked()) {
    $_SESSION['error'] = 'Não tem acesso a esta página. Apenas utilizadores bloqueados podem submeter pedidos de desbloqueio.';
    header("Location: /Views/mainPage.php");
    exit();
}

// Verificar se já tem um pedido pendente
if (userHasPendingAppeal($currentUserId)) {
    $_SESSION['error'] = 'Já submeteu um pedido de desbloqueio que está pendente de aprovação. Aguarde a resposta dos administradores.';
    header("Location: /Views/profile/profile.php");
    exit();
}

// Obter informações do motivo do bloqueio
$blockReason = getBlockReason($currentUserId);

// Processar mensagens de sessão
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$warning = $_SESSION['warning'] ?? '';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['warning']);

drawHeader("Handee - Pedido de Desbloqueio", ["/Styles/profile.css", "/Styles/unblock_request.css"]);
?>

<main>
    <section class="section-header">
        <h2>Pedido de Desbloqueio</h2>
        <p>Submeta um pedido para reativar a sua conta</p>
        <a href="/Views/profile/profile.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Voltar" class="back-button">
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
                
                <?php if ($warning): ?>
                    <div class="alert alert-warning"><?php echo htmlspecialchars($warning); ?></div>
                <?php endif; ?>

                <!-- Informação sobre o bloqueio -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-info-circle" style="margin-right: 10px; color: #dc3545;"></i>
                        Estado da Sua Conta
                    </h3>
                    
                    <div class="block-info-card">
                        <div class="block-status">
                            <i class="fas fa-ban" style="color: #dc3545; font-size: 2rem; margin-bottom: 10px;"></i>
                            <h4 style="color: #dc3545; margin-bottom: 15px;">Conta Bloqueada</h4>
                            <p>A sua conta encontra-se temporariamente suspensa. Pode submeter um pedido de desbloqueio explicando a situação.</p>
                        </div>
                        
                        <?php if ($blockReason): ?>
                        <div class="block-reason-info">
                            <h5>Motivo do Bloqueio:</h5>
                            <div class="reason-display">
                                <span class="reason-main"><?php echo htmlspecialchars($blockReason->getReason()); ?></span>
                                <?php if ($blockReason->getExtraInfo()): ?>
                                    <div class="reason-extra">
                                        <strong>Informações adicionais:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($blockReason->getExtraInfo())); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="block-reason-info">
                            <p><em>Motivo do bloqueio não especificado pelos administradores.</em></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Formulário de Pedido de Desbloqueio -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-unlock-alt" style="margin-right: 10px; color: var(--primary-color);"></i>
                        Submeter Pedido de Desbloqueio
                    </h3>
                    
                    <div class="appeal-instructions">
                        <h4>Instruções:</h4>
                        <ul>
                            <li>Seja claro e respeitoso na sua explicação</li>
                            <li>Explique as circunstâncias que levaram ao bloqueio</li>
                            <li>Demonstre que compreende as regras da plataforma</li>
                            <li>Indique as medidas que tomará para evitar futuras violações</li>
                            <li>Seja honesto - pedidos fraudulentos podem resultar em bloqueio permanente</li>
                        </ul>
                    </div>

                    <form method="post" action="/Controllers/UnblockAppealController.php" class="appeal-form">
                        <input type="hidden" name="action" value="create_appeal">
                        
                        <div class="form-group">
                            <label for="title">Título do Pedido *</label>
                            <input type="text" id="title" name="title" required maxlength="255"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                   placeholder="Ex: Pedido de reativação da conta">
                            <p class="form-hint">Um título breve que resuma o seu pedido (máximo 255 caracteres)</p>
                        </div>

                        <div class="form-group">
                            <label for="body">Explicação Detalhada *</label>
                            <textarea id="body" name="body" required rows="10" maxlength="2000"
                                      placeholder="Por favor, explique detalhadamente:
- As circunstâncias que levaram ao bloqueio
- O seu entendimento sobre o que aconteceu
- Como pretende evitar situações semelhantes no futuro
- Qualquer informação adicional relevante"><?php echo htmlspecialchars($_POST['body'] ?? ''); ?></textarea>
                            <p class="form-hint">
                                Explique detalhadamente a sua situação (máximo 2000 caracteres)
                                <span id="char-counter">0/2000</span>
                            </p>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-container">
                                <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                                <label for="terms_agreement">
                                    Declaro que li e compreendo os 
                                    <a href="/Views/staticPages/terms.php" target="_blank">Termos e Condições</a> 
                                    da plataforma e comprometo-me a respeitá-los no futuro.
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-container">
                                <input type="checkbox" id="truthfulness" name="truthfulness" required>
                                <label for="truthfulness">
                                    Confirmo que todas as informações fornecidas são verdadeiras e completas.
                                </label>
                            </div>
                        </div>

                        <div class="appeal-warning">
                            <i class="fas fa-exclamation-triangle" style="color: #ffc107;"></i>
                            <p><strong>Aviso:</strong> O envio de informações falsas ou enganosas pode resultar no bloqueio permanente da sua conta. 
                            Os administradores irão analisar o seu pedido e responder o mais brevemente possível.</p>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                                Submeter Pedido
                            </button>
                            <a href="/Views/profile/profile.php" class="btn-cancel">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Contador de caracteres para o textarea
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('body');
    const counter = document.getElementById('char-counter');
    const maxLength = 2000;
    
    function updateCounter() {
        const currentLength = textarea.value.length;
        counter.textContent = `${currentLength}/${maxLength}`;
        
        if (currentLength > maxLength * 0.9) {
            counter.style.color = '#dc3545';
        } else if (currentLength > maxLength * 0.8) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#6c757d';
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter(); // Inicializar contador
    
    // Validação do formulário
    const form = document.querySelector('.appeal-form');
    const titleInput = document.getElementById('title');
    const bodyTextarea = document.getElementById('body');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Validar título
        if (titleInput.value.trim().length < 5) {
            errorMessages.push('O título deve ter pelo menos 5 caracteres.');
            isValid = false;
        }
        
        // Validar corpo da mensagem
        if (bodyTextarea.value.trim().length < 50) {
            errorMessages.push('A explicação deve ter pelo menos 50 caracteres.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija os seguintes erros:\n\n' + errorMessages.join('\n'));
        }
    });
    
    // Auto-resize do textarea
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
</script>

<!-- Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php drawFooter(); ?>
