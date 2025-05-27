<?php
require_once(dirname(__FILE__) . '/../Models/NewsletterSubscription.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Insere um novo email na lista da newsletter.
 *
 * @param string $email Email a ser inserido.
 * @return bool True se inserido com sucesso, False caso contrário.
 */
function insertSubscription($email) {
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $db = getDatabaseConnection();
    $subscription = new NewsletterSubscription($db);
    $subscription->setEmail($email);

    return $subscription->save();
}

/**
 * Obtém todos os emails inscritos na newsletter.
 *
 * @return array Lista de todas as subscrições.
 */
function getAllSubscriptions() {
    $db = getDatabaseConnection();
    return NewsletterSubscription::getAllSubscriptions($db);
}

/**
 * Remove uma subscrição da newsletter com base no ID.
 *
 * @param int $id ID da subscrição a remover.
 * @return bool True se removido com sucesso, False caso contrário.
 */
function removeSubscription($id) {
    $db = getDatabaseConnection();
    $subscription = new NewsletterSubscription($db);
    $subscription->setId($id);

    return $subscription->delete();
}

/**
 * Verifica se um email já está inscrito na newsletter.
 *
 * @param string $email Email a ser verificado.
 * @return bool True se já estiver inscrito, False caso contrário.
 */
function isSubscribed($email) {
    $db = getDatabaseConnection();
    $subscription = NewsletterSubscription::findByEmail($db, $email);

    return $subscription !== null;
}

/**
 * Processamento de pedidos POST, caso este ficheiro seja acedido diretamente.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Iniciar sessão se necessário
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Processar nova inscrição na newsletter (do footer)
    if (isset($_POST['email']) && !isset($_POST['remove_id'])) {
        $success = insertSubscription($_POST['email']);
        if ($success) {
            $_SESSION['newsletter_success'] = 'Subscrição efetuada com sucesso!';
        } else {
            $_SESSION['newsletter_error'] = 'Erro ao efetuar subscrição. Verifique se o email é válido.';
        }
        
        // Redirecionar de volta para a página anterior ou main page
        $referer = $_SERVER['HTTP_REFERER'] ?? '/Views/mainPage.php';
        header("Location: " . $referer);
        exit;
    }

    // Processar remoção de subscrição existente (do admin)
    if (isset($_POST['remove_id'])) {
        $success = removeSubscription($_POST['remove_id']);
        
        if ($success) {
            $_SESSION['admin_success'] = 'Subscrição removida com sucesso!';
        } else {
            $_SESSION['admin_error'] = 'Erro ao remover subscrição.';
        }
        
        // Redirecionar de volta para a página admin da newsletter
        header("Location: /Views/admin/newsletter.php");
        exit;
    }
}
?>