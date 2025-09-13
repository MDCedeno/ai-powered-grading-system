<?php
require_once '../backend/config/db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM logs LIMIT 10");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Logs data:\n";
    foreach ($logs as $log) {
        echo json_encode($log) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
