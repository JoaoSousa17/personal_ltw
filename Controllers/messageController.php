<?php
function sendMessage($senderId, $receiverId, $body) {
    $db = getDatabaseConnection();
    $query = "INSERT INTO Message_ (sender_id, receiver_id, body_, date_, time_) 
              VALUES (:sender, :receiver, :body, DATE('now'), TIME('now'))";

    $stmt = $db->prepare($query);
    return $stmt->execute([
        ':sender' => $senderId,
        ':receiver' => $receiverId,
        ':body' => $body
    ]);
}

function getClientIdFromRequest($requestId) {
    $db = getDatabaseConnection();
    $query = "
        SELECT m.sender_id
        FROM Request r
        JOIN Message_ m ON r.message_id = m.id
        WHERE r.id = :request_id
          AND m.body_ LIKE 'Pedido:%'
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([':request_id' => $requestId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['sender_id'] : null;
}
?>