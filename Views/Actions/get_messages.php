<?php
require_once(__DIR__ . "/../../Database/connection.php");
session_start();

header("Content-Type: application/json");
/*
if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos."]);
    exit;
}*/

$loggedInUserId = $_SESSION['user_id'] ?? 4; // para testes, remover o 4 depois
$otherUserId = intval($_GET['receiver_id']);

try {
    $db = getDatabaseConnection();

    // Buscar mensagens novas enviadas pelo outro usuário e ainda não lidas
    $stmt = $db->prepare("
        SELECT * FROM Message_ 
        WHERE sender_id = :otherUser 
          AND receiver_id = :loggedInUser 
          AND is_read = 0
        ORDER BY date_, time_
    ");
    $stmt->execute([
        'otherUser' => $otherUserId,
        'loggedInUser' => $loggedInUserId
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marcar essas mensagens como lidas
    if (!empty($messages)) {
        $ids = array_column($messages, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $markStmt = $db->prepare("UPDATE Message_ SET is_read = 1 WHERE id IN ($placeholders)");
        $markStmt->execute($ids);
    }

    echo json_encode(["messages" => $messages]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao buscar mensagens"]);
}
