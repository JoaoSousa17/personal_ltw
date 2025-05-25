<?php
function getDatabaseConnection() {
    $dbPath = __DIR__ . '/database.db';
    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Erro ao ligar à base de dados: " . $e->getMessage());
    }
}
?>