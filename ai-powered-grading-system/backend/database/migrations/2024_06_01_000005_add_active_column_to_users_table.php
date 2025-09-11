<?php
require_once __DIR__ . '/../../config/db.php';

class AddActiveColumnToUsersTable {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function up() {
        $sql = "ALTER TABLE users ADD COLUMN active TINYINT(1) DEFAULT 1";
        $this->pdo->exec($sql);
    }

    public function down() {
        $sql = "ALTER TABLE users DROP COLUMN active";
        $this->pdo->exec($sql);
    }
}

// Run migration
$migration = new AddActiveColumnToUsersTable($pdo);
$migration->up();
?>
