<?php
// backup_tables.php
require_once __DIR__ . '/../../config/db.php';

// $pdo is defined in db.php

$backupDir = __DIR__ . '/../../../backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// List of tables to backup
$tables = ['grades', 'courses', 'students', 'logs', 'users'];

foreach ($tables as $table) {
    $filename = "$backupDir/{$table}_" . date('Ymd_His') . ".sql";

    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $createTable = $row['Create Table'] . ";\n\n";

    $data = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $insertData = "";
    foreach ($data as $record) {
        $columns = array_map(function($col) { return "`$col`"; }, array_keys($record));
        $values = array_map(function($val) use ($pdo) { return $pdo->quote($val); }, array_values($record));
        $insertData .= "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
    }

    file_put_contents($filename, $createTable . $insertData);
    echo "Table '$table' backed up to $filename\n";
}

echo "Backup completed successfully.\n";
?>
