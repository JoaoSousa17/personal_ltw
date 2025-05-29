<?php
require_once("../../Database/connection.php");

header('Content-Type: application/json');

if (!isset($_GET['freelancer_id']) || !is_numeric($_GET['freelancer_id'])) {
    echo json_encode(['error' => 'ID de freelancer inválido.']);
    exit;
}

$freelancerId = intval($_GET['freelancer_id']);
$db = getDatabaseConnection();

$stmt = $db->prepare("
    SELECT id, name_, price_per_hour
    FROM Service_
    WHERE freelancer_id = ? AND is_active = 1
    ORDER BY id ASC
");

$stmt->execute([$freelancerId]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($services)) {
    echo json_encode($services);
} else {
    echo json_encode(['error' => 'Sem serviços para este utilizador']);
}
