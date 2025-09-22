<?php
require_once __DIR__ . '/../backend/config/db.php';

global $pdo;

echo "=== Checking Recent Logs ===\n\n";

try {
    $stmt = $pdo->prepare("SELECT * FROM logs ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($logs)) {
        echo "No logs found in database\n";
    } else {
        echo "Recent logs in database:\n";
        foreach ($logs as $log) {
            echo "- {$log['action']}: {$log['details']} (Success: {$log['success']}) - {$log['created_at']}\n";
        }
    }

    // Check for backup-related logs specifically
    echo "\n=== Checking for Backup Logs ===\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM logs WHERE action LIKE '%backup%' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute();
    $backupLogs = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Backup-related logs in last hour: {$backupLogs['count']}\n";

} catch (Exception $e) {
    echo "Error checking logs: " . $e->getMessage() . "\n";
}
?>
