<?php
require_once __DIR__ . '/../../config/db.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DROP TABLE IF EXISTS grades");
    $pdo->exec("DROP TABLE IF EXISTS courses");
    $pdo->exec("DROP TABLE IF EXISTS students");
    $pdo->exec("DROP TABLE IF EXISTS logs");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "Tables dropped successfully.\n";
} catch (Exception $e) {
    echo "Error dropping tables: " . $e->getMessage() . "\n";
}
?>
