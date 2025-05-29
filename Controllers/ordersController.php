<?php
require_once(dirname(__FILE__) . "/../Utils/session.php");
require_once(dirname(__FILE__) . "/../Database/connection.php");

function getOrdersForUserServices($userId) {
    $db = getDatabaseConnection();

    $stmt = $db->prepare("
        SELECT 
            r.id AS request_id,
            r.title,
            r.price,
            r.duration,
            r.service_data_id,
            r.message_id,
            r.status_ AS status,
            sd.service_id,
            s.name_ AS service_name,
            u.name_ AS client_name,
            m.date_ || ' ' || m.time_ AS created_at,
            m.body_ AS client_message
        FROM Request r
        JOIN Service_Data sd ON r.service_data_id = sd.id
        JOIN Service_ s ON sd.service_id = s.id
        JOIN Message_ m ON r.message_id = m.id
        JOIN User_ u ON m.sender_id = u.id
        WHERE s.freelancer_id = ?
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($orderId, $newStatus, $freelancerId) {
    $db = getDatabaseConnection();

    $stmt = $db->prepare("
        UPDATE Request
        SET status_ = :status
        WHERE id = :orderId
        AND service_data_id IN (
            SELECT sd.id
            FROM Service_Data sd
            JOIN Service_ s ON s.id = sd.service_id
            WHERE s.freelancer_id = :freelancerId
        )
    ");

    return $stmt->execute([
        ':status' => $newStatus,
        ':orderId' => $orderId,
        ':freelancerId' => $freelancerId
    ]);
}

function deleteOrderById($orderId, $freelancerId) {
    $db = getDatabaseConnection();

    try {
        // Iniciar transação
        $db->beginTransaction();

        // Primeiro, obter o service_data_id para garantir que pertence ao freelancer
        $stmt = $db->prepare("
            SELECT service_data_id 
            FROM Request
            WHERE id = :orderId
            AND service_data_id IN (
                SELECT id FROM Service_Data WHERE service_id IN (
                    SELECT id FROM Service_ WHERE freelancer_id = :freelancerId
                )
            )
        ");
        $stmt->execute([
            ':orderId' => $orderId,
            ':freelancerId' => $freelancerId
        ]);
        $serviceDataId = $stmt->fetchColumn();

        if (!$serviceDataId) {
            // Pedido não encontrado ou não pertence ao freelancer
            $db->rollBack();
            return false;
        }

        // Apagar da tabela Request
        $stmtDeleteRequest = $db->prepare("DELETE FROM Request WHERE id = :orderId");
        $stmtDeleteRequest->execute([':orderId' => $orderId]);

        // Confirmar transação
        $db->commit();

        return true;

    } catch (Exception $e) {
        // Em caso de erro, desfazer alterações
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return false;
    }
}



?>