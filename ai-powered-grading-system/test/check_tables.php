<?php
require_once __DIR__ . '/../backend/config/db.php';

$tables = ['users', 'students', 'courses', 'grades', 'logs'];
foreach($tables as $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        echo "$table: " . $stmt->fetch()[0] . " records\n";
    } catch(Exception $e) {
        echo "$table: does not exist\n";
    }
}
?>
