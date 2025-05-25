<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Inclua as funções do arquivo authPages_elems.php
require_once("../Templates/authPages_elems.php");

// Processa login se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    if ($user) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['is_admin'] = $user->getIsAdmin();

        unset($_SESSION['error'], $_SESSION['success']);

        header("Location: /Views/profile.php?id=" . $_SESSION['user_id']);
        exit();
    } else {
        // Login falhou
        $_SESSION['error'] = "Nome de usuário ou senha incorretos.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Autenticação</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../Styles/auth.css">
    <script src="../Scripts/auth.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Autenticação</h1>

        <?php 
            // Exibe mensagens de erro e sucesso
            showError($error);
            showSuccess($success);
        ?>

        <?php
            // Exibe os tabs
            drawTabs();
            
            // Exibe o formulário de login
            drawLoginForm();
            
            // Exibe o formulário de criação de conta
            drawSignupForm();
        ?>
    </div>
</body>
</html>
