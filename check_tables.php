<?php
require_once 'ai-powered-grading-system/backend/config/db.php';

$tables = ['students', 'courses', 'grades'];
foreach($tables as $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        echo "$table: " . $stmt->fetch()[0] . "\n";
    } catch(Exception $e) {
        echo "$table: does not exist\n";
    }
}
?>
