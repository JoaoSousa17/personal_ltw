<?php
session_start();    // Inicia a sessão, permitindo o uso de $_SESSION

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
    return $_SESSION['username'];
}
?>
