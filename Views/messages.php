<?php

// Inicializar sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticação do utilizador
if (!isset($_SESSION['user_id'])) {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /Views/auth.php");
    exit();
}

// Includes necessários
require_once("../Templates/common_elems.php");
require_once("../Templates/msg_elems.php");
require_once("../Database/connection.php");

// Inicializar variáveis
$userId = $_SESSION['user_id'];
$cartCount = $cartCount ?? 0;

// Obter conversas do utilizador
try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT DISTINCT
            CASE
                WHEN sender_id = :userId THEN receiver_id
                ELSE sender_id
            END AS other_user_id,
            U.name_ AS username,
            U.profile_photo
        FROM Message_ M
        JOIN User_ U ON U.id = CASE
            WHEN M.sender_id = :userId THEN M.receiver_id
            ELSE M.sender_id
        END
        WHERE sender_id = :userId OR receiver_id = :userId
    ");
    $stmt->execute(['userId' => $userId]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erro ao obter conversas: " . $e->getMessage());
    $conversations = [];
}

// Garantir que conversations é um array
if (!$conversations || !is_array($conversations)) {
    $conversations = [];
}

// Adicionar timestamp da última mensagem a cada conversa
foreach ($conversations as &$conv) {
    $conv['last_timestamp'] = getLastMessageTimestamp($userId, $conv['other_user_id']) ?? '0000-00-00 00:00:00';
}
unset($conv);

// Ordenar conversas por timestamp (mais recente primeiro)
usort($conversations, function ($a, $b) {
    return strtotime($b['last_timestamp']) <=> strtotime($a['last_timestamp']);
});

// Desenhar cabeçalho da página
drawHeader("Mensagens", ["../Styles/messages.css"]);
?>

<main class="messages-page-container">
    <div class="messages-container">
        <!-- Lista de Conversas -->
        <aside class="conversation-list">
            <?php drawConversationList($conversations); ?>
        </aside>
        
        <!-- Container das Janelas de Chat -->
        <section class="chat-window-container">
            <?php drawAllChatWindows($conversations, $userId); ?>
        </section>
    </div>
</main>

<!-- Variáveis JavaScript -->
<script>
    const loggedInUserId = <?= json_encode($userId) ?>;
</script>

<!-- JavaScript da página -->
<script src="/Scripts/messages.js"></script>

<?php drawFooter(); ?>