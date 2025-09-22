<?php
require_once __DIR__ . '/../backend/config/db.php';

global $pdo;

echo "=== Checking Logs Table Structure ===\n\n";

try {
    $stmt = $pdo->prepare("DESCRIBE logs");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Logs table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} " . ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . " " . ($column['Key'] === 'PRI' ? 'PRIMARY' : '') . "\n";
    }

    echo "\n=== Checking Foreign Key Constraints ===\n";
    $stmt = $pdo->prepare("SELECT
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM
        information_schema.KEY_COLUMN_USAGE
    WHERE
        REFERENCED_TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME = 'users'
        AND TABLE_NAME = 'logs'");
    $stmt->execute();
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($constraints)) {
        echo "No foreign key constraints found for logs table referencing users table\n";
    } else {
        foreach ($constraints as $constraint) {
            echo "- {$constraint['CONSTRAINT_NAME']}: {$constraint['TABLE_NAME']}.{$constraint['COLUMN_NAME']} -> {$constraint['REFERENCED_TABLE_NAME']}.{$constraint['REFERENCED_COLUMN_NAME']}\n";
        }
    }

} catch (Exception $e) {
    echo "Error checking table structure: " . $e->getMessage() . "\n";
}
?>
