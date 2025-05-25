<?php
// Inicia a sessão apenas se não estiver já iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Define o username do user atual na sessão.
 *
 * @param string $username - username a ser armazenado.
 */
function setCurrentUser($username){
    $_SESSION['username'] = $username;
}

/**
 * Obtém o username do user atualmente armazenado na sessão.
 *
 * @return string|null - username, ou null, caso não esteja definido.
 */
function getCurrentUser(){
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Verifica se existe um utilizador logado.
 *
 * @return bool - true se estiver logado, false caso contrário.
 */
function isUserLoggedIn(){
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica se o utilizador atual é administrador.
 *
 * @return bool - true se for admin, false caso contrário.
 */
function isUserAdmin(){
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Obtém o ID do utilizador atual.
 *
 * @return int|null - ID do utilizador ou null se não estiver logado.
 */
function getCurrentUserId(){
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Obtém o nome do utilizador atual.
 *
 * @return string|null - Nome do utilizador ou null se não estiver logado.
 */
function getCurrentUserName(){
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Verifica se o utilizador tem acesso a páginas de administração.
 * Redireciona automaticamente se não tiver permissão.
 * 
 * @param string $redirectOnNoLogin URL para redirecionar se não estiver logado (padrão: /Views/auth.php)
 * @param string $redirectOnNoAdmin URL para redirecionar se não for admin (padrão: /Views/mainPage.php)
 */
function requireAdminAccess($redirectOnNoLogin = '/Views/auth.php', $redirectOnNoAdmin = '/Views/mainPage.php') {
    // Verificar se o utilizador está autenticado
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login para aceder a esta página.';
        header("Location: " . $redirectOnNoLogin);
        exit();
    }

    // Verificar se o utilizador é administrador
    if (!isUserAdmin()) {
        $_SESSION['error'] = 'Não tem permissão para aceder ao painel de administração.';
        header("Location: " . $redirectOnNoAdmin);
        exit();
    }
}
?>