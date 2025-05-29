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
            $_SESSION['error'] = 'Deve fazer login para submeter um pedido.';
            header("Location: /Views/auth.php");
            exit;
        }

        // Validar campos obrigatórios
        if (!isset($_POST['title']) || !isset($_POST['body']) || !isset($_POST['terms_agreement']) || !isset($_POST['truthfulness'])) {
            $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
            header("Location: /Views/unblock_request.php");
            exit;
        }

        $title = trim($_POST['title']);
        $body = trim($_POST['body']);

        // Validações básicas
        if (strlen($title) < 5 || strlen($title) > 255) {
            $_SESSION['error'] = 'O título deve ter entre 5 e 255 caracteres.';
            header("Location: /Views/unblock_request.php");
            exit;
        }

        if (strlen($body) < 50 || strlen($body) > 2000) {
            $_SESSION['error'] = 'A explicação deve ter entre 50 e 2000 caracteres.';
            header("Location: /Views/unblock_request.php");
            exit;
        }

        // Verificar se o utilizador está bloqueado
        require_once(dirname(__FILE__) . '/../Controllers/userController.php');
        $user = getUserById($_SESSION['user_id']);
        if (!$user || !$user->getIsBlocked()) {
            $_SESSION['error'] = 'Apenas utilizadores bloqueados podem submeter pedidos de desbloqueio.';
            header("Location: /Views/mainPage.php");
            exit;
        }

        // Verificar se já tem um pedido pendente
        if (userHasPendingAppeal($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Já submeteu um pedido de desbloqueio que está pendente de aprovação.';
            header("Location: /Views/profile/profile.php");
            exit;
        }

        $result = createAppeal($_SESSION['user_id'], $title, $body);
        
        if ($result) {
            $_SESSION['success'] = 'Pedido de desbloqueio submetido com sucesso! Os administradores irão analisar o seu pedido.';
            header("Location: /Views/profile/profile.php");
        } else {
            $_SESSION['error'] = 'Erro ao submeter o pedido. Tente novamente mais tarde.';
            header("Location: /Views/unblock_request.php");
        }
        exit;
    }

    // Aprova pedido (apenas para admins)
    if (isset($_POST['action']) && $_POST['action'] === 'approve_appeal' && isset($_POST['appeal_id'])) {
        // Verificar se é admin
        require_once(dirname(__FILE__) . '/../Utils/session.php');
        if (!isUserAdmin()) {
            $_SESSION['error'] = 'Acesso negado.';
            header("Location: /Views/mainPage.php");
            exit;
        }

        $success = approveAppeal($_POST['appeal_id']);
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($isAjax) {
            echo json_encode(['success' => $success]);
        } else {
            if ($success) {
                $_SESSION['success'] = 'Pedido aprovado com sucesso. O utilizador foi desbloqueado.';
            } else {
                $_SESSION['error'] = 'Erro ao aprovar o pedido.';
            }
            header("Location: /Views/admin/blockedUsers.php");
        }
        exit;
    }

    // Rejeita pedido (apenas para admins)
    if (isset($_POST['action']) && $_POST['action'] === 'reject_appeal' && isset($_POST['appeal_id'])) {
        // Verificar se é admin
        require_once(dirname(__FILE__) . '/../Utils/session.php');
        if (!isUserAdmin()) {
            $_SESSION['error'] = 'Acesso negado.';
            header("Location: /Views/mainPage.php");
            exit;
        }

        $success = rejectAppeal($_POST['appeal_id']);
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($isAjax) {
            echo json_encode(['success' => $success]);
        } else {
            if ($success) {
                $_SESSION['success'] = 'Pedido rejeitado com sucesso.';
            } else {
                $_SESSION['error'] = 'Erro ao rejeitar o pedido.';
            }
            header("Location: /Views/admin/blockedUsers.php");
        }
        exit;
    }
}
?>
