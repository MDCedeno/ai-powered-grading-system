<?php
require_once __DIR__ . '/../../config/db.php';

class BackupRecordSeeder {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run() {
        // Insert sample backup records
        $backupRecords = [
            ['backup_time' => '2024-09-15 10:00:00'],
            ['backup_time' => '2024-09-16 10:00:00'],
            ['backup_time' => '2024-09-17 10:00:00'],
        ];

        foreach ($backupRecords as $record) {
            $stmt = $this->pdo->prepare("INSERT INTO backup_records (backup_time) VALUES (:backup_time)");
            $stmt->execute($record);
            echo "Backup record for {$record['backup_time']} created successfully.\n";
        }
    }
}
?>
