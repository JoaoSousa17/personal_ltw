<?php
require_once(dirname(__FILE__) . '/../Models/NewsletterSubscription.php');

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

    // Processar nova inscrição na newsletter
    if (isset($_POST['email'])) {
        insertSubscription($_POST['email']);
        header("Location: ../Views/mainPage.php");
        exit;
    }

    // Processar remoção de subscrição existente
    if (isset($_POST['remove_id'])) {
        $success = removeSubscription($_POST['remove_id']);
        echo json_encode(['success' => $success]);
        exit;
    }
}
?>
