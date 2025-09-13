<?php
require_once 'ai-powered-grading-system/backend/config/db.php';

try {
    $stmt = $pdo->prepare("DESCRIBE logs");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Logs table columns:\n";
    foreach ($columns as $col) {
        echo "- {$col['Field']}: {$col['Type']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
