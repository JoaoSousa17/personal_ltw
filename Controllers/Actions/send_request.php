<?php
session_start();
require_once("../../Database/connection.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];
$freelancerId = $data['freelancer_id'] ?? null;
$serviceId = $data['service_id'] ?? null;
$price = $data['price'] ?? null;
$duration = $data['duration'] ?? null;

if (!$freelancerId || !$serviceId || !$price || !$duration) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

try {
    $db = getDatabaseConnection();
    $db->beginTransaction();

    // Verificar se já existe um pedido pendente para o mesmo freelancer e serviço
    $checkStmt = $db->prepare("
        SELECT id FROM Service_Data 
        WHERE user_id = :user_id AND service_id = :service_id AND status_ = 'pending'
        LIMIT 1
    ");
    $checkStmt->execute([
        ':user_id' => $freelancerId,
        ':service_id' => $serviceId
    ]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Pedido existe: atualizar o pedido
        $serviceDataId = $existing['id'];

        $updateStmt = $db->prepare("
            UPDATE Service_Data 
            SET final_price = :final_price, status_ = 'pending' 
            WHERE id = :id
        ");
        $updateStmt->execute([
            ':final_price' => $price,
            ':id' => $serviceDataId
        ]);
    } else {
        // Pedido não existe: inserir novo
        $insertStmt = $db->prepare("
            INSERT INTO Service_Data (user_id, service_id, final_price, status_) 
            VALUES (:user_id, :service_id, :final_price, 'pending')
        ");
        $insertStmt->execute([
            ':user_id' => $freelancerId,
            ':service_id' => $serviceId,
            ':final_price' => $price,
        ]);
        $serviceDataId = $db->lastInsertId();
    }


    // 2) Criar uma mensagem para o freelancer e capturar o message_id (exemplo: "Pedido enviado")
    $nowDate = date('Y-m-d');
    $nowTime = date('H:i:s');
    $body = "Pedido: €$price/h";

    $stmt = $db->prepare("INSERT INTO Message_ (sender_id, receiver_id, body_, date_, time_, is_read) VALUES (:sender_id, :receiver_id, :body_, :date_, :time_, 0)");
    $stmt->execute([
        ':sender_id' => $userId,        // Usuário logado é quem enviou o pedido
        ':receiver_id' => $freelancerId,
        ':body_' => $body,
        ':date_' => $nowDate,
        ':time_' => $nowTime,
    ]);
    $messageId = $db->lastInsertId();

    // 3) Inserir na Request associando service_data_id e message_id
    $stmt = $db->prepare("INSERT INTO Request (service_data_id, message_id, title, price, duration, status_) VALUES (:service_data_id, :message_id, :title, :price, :duration, 'pending')");
    $stmt->execute([
        ':service_data_id' => $serviceDataId,
        ':message_id' => $messageId,
        ':title' => 'Pedido via chat',
        ':price' => $price,
        ':duration' => $duration,
    ]);

    $db->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
