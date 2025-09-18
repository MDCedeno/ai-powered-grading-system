<?php
require_once __DIR__ . '/../../config/db.php';

class CreateBackupRecordsTable {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function up() {
        $sql = "CREATE TABLE IF NOT EXISTS backup_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            backup_time DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sql);
    }

    public function down() {
        $sql = "DROP TABLE IF EXISTS backup_records;";
        $this->pdo->exec($sql);
    }
}
?>
