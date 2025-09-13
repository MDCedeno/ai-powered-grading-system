<?php
// restore_tables.php
require_once __DIR__ . '/../../config/db.php';

$backupDir = __DIR__ . '/backups';
$files = glob("$backupDir/*.sql");

if (!$files) {
    die("No backup files found in $backupDir\n");
}

foreach ($files as $file) {
    $sql = file_get_contents($file);
    try {
        $pdo->exec($sql);
        echo "Restored from file: $file\n";
    } catch (Exception $e) {
        echo "Error restoring file $file: " . $e->getMessage() . "\n";
    }
}

echo "Restore completed.\n";
?>
