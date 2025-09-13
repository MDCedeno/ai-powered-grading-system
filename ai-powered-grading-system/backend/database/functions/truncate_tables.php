<?php
// truncate_tables.php
require_once __DIR__ . '/../../config/db.php';

try {
    // Disable foreign key checks to avoid constraint issues
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    // List of tables to truncate
    $tables = ['grades', 'courses', 'students', 'logs', 'users'];

    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
        echo "Table '$table' truncated successfully.\n";
    }

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "All tables truncated successfully.\n";
} catch (Exception $e) {
    echo "Error truncating tables: " . $e->getMessage() . "\n";
}
?>
