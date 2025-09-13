<?php
require_once __DIR__ . '/../../config/db.php';

class CreateCoursesTable {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function up() {
        $sql = "CREATE TABLE IF NOT EXISTS courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_code VARCHAR(50) NOT NULL UNIQUE,
            course_name VARCHAR(255) NOT NULL,
            professor_id INT,
            semester VARCHAR(50),
            year INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB;";
        $this->pdo->exec($sql);
    }

    public function down() {
        $sql = "DROP TABLE IF EXISTS courses;";
        $this->pdo->exec($sql);
    }
}


?>
