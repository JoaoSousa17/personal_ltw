<?php
require_once(__DIR__ . "/../../Database/connection.php");
session_start();

date_default_timezone_set('Europe/Lisbon');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

/*
if (!isset($_SESSION['user_id']) || !isset($data['receiver_id']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos."]);
    exit;
}*/

$senderId = $_SESSION['user_id'] ?? 4;      //testes remover o 4 depois
$receiverId = (int)$data['receiver_id'];
$message = trim($data['message']);
$serviceId = isset($data['service_id']) ? (int)$data['service_id'] : null;

if ($senderId === $receiverId || strlen($message) === 0) {
    http_response_code(400);
    echo json_encode(["error" => "Mensagem inválida."]);
    exit;
}

try {
    $db = getDatabaseConnection();

    $stmt = $db->prepare("
        INSERT INTO Message_ (sender_id, receiver_id, body_, date_, time_, is_read)
        VALUES (?, ?, ?, DATE('now', 'localtime'), TIME('now', 'localtime'), false)
    ");
    $stmt->execute([$senderId, $receiverId, $message]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro interno"]);
}
