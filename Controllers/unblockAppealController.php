<?php
require_once(dirname(__FILE__) . '/../Models/UnblockAppeal.php');
require_once(dirname(__FILE__) . '/../Models/User.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Cria um novo pedido de desbloqueio.
 *
 * @param int $userId ID do utilizador.
 * @param string $title Título do pedido.
 * @param string $body Conteúdo do pedido.
 * @return bool|UnblockAppeal Objeto criado ou false em caso de falha.
 */
function createAppeal($userId, $title, $body) {
    $db = getDatabaseConnection();

    $userObj = User::findById($db, $userId);
    if (!$userObj || !$userObj->getIsBlocked()) return false;

    if (UnblockAppeal::hasPendingAppeal($db, $userId)) return false;

    $appeal = new UnblockAppeal($db);
    $appeal->setUserId($userId);
    $appeal->setTitle($title);
    $appeal->setBody($body);
    $appeal->setStatus('pending');

    return $appeal->save() ? $appeal : false;
}

/**
 * Obtém um pedido de desbloqueio pelo ID.
 *
 * @param int $appealId ID do pedido.
 * @return UnblockAppeal|null Objeto encontrado ou null.
 */
function getAppealById($appealId) {
    $db = getDatabaseConnection();
    return UnblockAppeal::findById($db, $appealId);
}

/**
 * Obtém todos os pedidos de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return array Lista de pedidos associados.
 */
function getUserAppeals($userId) {
    $db = getDatabaseConnection();
    return UnblockAppeal::findByUserId($db, $userId);
}

/**
 * Aprova um pedido de desbloqueio e desbloqueia o utilizador.
 *
 * @param int $appealId ID do pedido.
 * @return bool True se aprovado com sucesso, False caso contrário.
 */
function approveAppeal($appealId) {
    $db = getDatabaseConnection();

    $appeal = UnblockAppeal::findById($db, $appealId);
    if (!$appeal) return false;

    $user = User::findById($db, $appeal->getUserId());
    if (!$user || !$user->unblockUser()) return false;

    return $appeal->approve();
}

/**
 * Rejeita um pedido de desbloqueio.
 *
 * @param int $appealId ID do pedido.
 * @return bool True se rejeitado com sucesso, False caso contrário.
 */
function rejectAppeal($appealId) {
    $db = getDatabaseConnection();
    $appeal = UnblockAppeal::findById($db, $appealId);

    return $appeal ? $appeal->reject() : false;
}

/**
 * Obtém todos os pedidos de desbloqueio.
 *
 * @return array Lista de todos os pedidos.
 */
function getAllAppeals() {
    $db = getDatabaseConnection();
    return UnblockAppeal::getAllAppeals($db);
}

/**
 * Obtém todos os pedidos de desbloqueio pendentes.
 *
 * @return array Lista de pedidos pendentes.
 */
function getPendingAppeals() {
    $db = getDatabaseConnection();
    return UnblockAppeal::getPendingAppeals($db);
}

/**
 * Verifica se um utilizador tem um pedido pendente.
 *
 * @param int $userId ID do utilizador.
 * @return bool True se tiver pedido pendente, False caso contrário.
 */
function userHasPendingAppeal($userId) {
    $db = getDatabaseConnection();
    return UnblockAppeal::hasPendingAppeal($db, $userId);
}

/**
 * Processamento de requisições HTTP POST.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Cria novo pedido
    if (isset($_POST['action']) && $_POST['action'] === 'create_appeal') {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../Views/login.php");
            exit;
        }

        if (isset($_POST['title']) && isset($_POST['body'])) {
            $result = createAppeal($_SESSION['user_id'], $_POST['title'], $_POST['body']);
            header("Location: ../Views/" . ($result ? "profile.php?success=appeal_submitted" : "unblock_appeal.php?error=1"));
            exit;
        }
    }

    // Aprova pedido
    if (isset($_POST['action']) && $_POST['action'] === 'approve_appeal' && isset($_POST['appeal_id'])) {
        $success = approveAppeal($_POST['appeal_id']);
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($isAjax) {
            echo json_encode(['success' => $success]);
        } else {
            header("Location: ../Views/admin/appeals.php?" . ($success ? "success=approved" : "error=1"));
        }
        exit;
    }

    // Rejeita pedido
    if (isset($_POST['action']) && $_POST['action'] === 'reject_appeal' && isset($_POST['appeal_id'])) {
        $success = rejectAppeal($_POST['appeal_id']);
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($isAjax) {
            echo json_encode(['success' => $success]);
        } else {
            header("Location: ../Views/admin/appeals.php?" . ($success ? "success=rejected" : "error=1"));
        }
        exit;
    }
}
?>
