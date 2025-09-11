<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/log.php';

class SuperAdminController {
    private $userModel;
    private $courseModel;
    private $logModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->courseModel = new Course($pdo);
        $this->logModel = new Log($pdo);
    }

    public function getAllUsers() {
        // Get all users
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deactivateUser($user_id) {
        $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getSystemLogs() {
        return $this->logModel->getAll();
    }

    // Add methods for database backup, AI config, etc.
}
?>
