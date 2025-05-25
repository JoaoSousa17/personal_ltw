<?php
/**
 * Função para exibir mensagens de erro.
 * 
 * @param string $error A mensagem de erro a ser exibida.
 */
function showError($error) {
    if ($error) {
        echo '<div class="alert error">' . htmlspecialchars($error) . '</div>';
    }
}

/**
 * Função para exibir mensagens de sucesso.
 * 
 * @param string $success A mensagem de sucesso a ser exibida.
 */
function showSuccess($success) {
    if ($success) {
        echo '<div class="alert success">' . htmlspecialchars($success) . '</div>';
        echo '<script>
                setTimeout(function() {
                    window.location.href = "../Views/mainPage.php";
                }, 3000);
              </script>';
    }
}

/**
 * Função para desenhar os tabs (Login e Criar Conta).
 */
function drawTabs() {
    echo '<div class="tabs">
            <div id="login-tab" class="tab active" onclick="showTab(\'login-form\')">Login</div>
            <div id="signup-tab" class="tab" onclick="showTab(\'signup-form\')">Criar Conta</div>
            <div class="tab-underline"></div>
          </div>';
}

/**
 * Função para desenhar o formulário de login.
 */
function drawLoginForm() {
    echo '<form id="login-form" method="POST" action="../Controllers/authController.php">
            <input type="hidden" name="action" value="login">
            <input type="text" name="email" class="input-box" placeholder="Email" required>
            <input type="password" name="password" class="pass-box" placeholder="Senha" required>
            <i class="fa-solid fa-eye" onclick="togglePasswordVisibility(this)"></i>
            <p>Esqueceu a senha?</p>
            <button type="submit" class="btn">Login</button>
          </form>';
}

/**
 * Função para desenhar o formulário de criação de conta.
 */
function drawSignupForm() {
    echo '<form id="signup-form" class="hidden" method="POST" action="../Controllers/authController.php">
            <input type="hidden" name="action" value="signup">
            <input type="text" name="name" class="input-box" placeholder="Nome" required>
            <input type="email" name="email" class="input-box" placeholder="Email" required>
            <input type="tel" name="phone" class="input-box" placeholder="Telefone" pattern="[0-9]+" required>
            <input type="password" name="password" class="pass-box" placeholder="Senha" required>
            <i class="fa-solid fa-eye" onclick="togglePasswordVisibility(this)"></i>
            <input type="password" name="password2" class="pass-box" placeholder="Confirmar Senha" required>
            <i class="fa-solid fa-eye" onclick="togglePasswordVisibility(this)"></i>
            <button type="submit" class="btn">Criar Conta</button>
          </form>';
}
?>
