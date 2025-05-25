<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($cartCount)) {
    $cartCount = 0;
}

require_once("../Templates/common_elems.php");
require_once("../Templates/msg_elems.php");
require_once("../Database/connection.php");

drawHeader("Mensagens", ["../Styles/msg.css"]);

$userId = $_SESSION['user_id'] ?? 4;

if (!$userId) {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /Views/auth.php");
    exit();
}

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

if (!$conversations || !is_array($conversations)) {
    $conversations = [];
}

foreach ($conversations as &$conv) {
    $conv['last_timestamp'] = getLastMessageTimestamp($userId, $conv['other_user_id']) ?? '0000-00-00 00:00:00';
}
unset($conv);

usort($conversations, function ($a, $b) {
    return strtotime($b['last_timestamp']) <=> strtotime($a['last_timestamp']);
});
?>

<div class="messages-page-container">
  <div class="messages-container">
    <div class="conversation-list">
      <?php drawConversationList($conversations); ?>
    </div>
    
    <div class="chat-window-container">
      <?php drawAllChatWindows($conversations, $userId); ?>
    </div>
  </div>
</div>


<script>
  const loggedInUserId = <?= json_encode($userId) ?>;
</script>

<script src="/Scripts/messages.js"></script>
