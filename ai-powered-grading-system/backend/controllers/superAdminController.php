<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/log.php'; 

class SuperAdminController {
    private $userModel;
    private $courseModel;
    private $logModel;
    private $pdo;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->courseModel = new Course($pdo);
        $this->logModel = new Log($pdo);
        $this->pdo = $pdo;
    }

    public function getAllUsers() {
        // Get all users
        $stmt = $this->pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deactivateUser($user_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getSystemLogs() {
        return $this->logModel->getAll();
    }

    public function activateUser($user_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 1 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function deleteUser($user_id) {
        // Soft delete or hard delete? For now, hard delete
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getSystemStats() {
        // Get counts of users, students, courses, grades
        $stats = [];
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $stats['users'] = $stmt->fetch()['count'];

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM students");
        $stmt->execute();
        $stats['students'] = $stmt->fetch()['count'];

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM courses");
        $stmt->execute();
        $stats['courses'] = $stmt->fetch()['count'];

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM grades");
        $stmt->execute();
        $stats['grades'] = $stmt->fetch()['count'];

        return $stats;
    }

    public function backupDatabase() {
        // Simple backup to SQL file
        $tables = ['users', 'students', 'courses', 'grades', 'logs'];
        $backup = '';
        foreach ($tables as $table) {
            $stmt = $this->pdo->prepare("SELECT * FROM $table");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $backup .= "-- Table: $table\n";
            foreach ($rows as $row) {
                $values = array_map(function($val) { return $this->pdo->quote($val); }, array_values($row));
                $backup .= "INSERT INTO $table (" . implode(',', array_keys($row)) . ") VALUES (" . implode(',', $values) . ");\n";
            }
            $backup .= "\n";
        }
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        file_put_contents(__DIR__ . '/../../backups/' . $filename, $backup);
        return ['success' => true, 'file' => $filename];
    }

    public function getAIConfig() {
        // For now, return dummy config
        return [
            'ai_endpoint' => 'http://localhost:5000',
            'model' => 'gpt-3.5-turbo',
            'enabled' => true
        ];
    }

    public function updateAIConfig($config) {
        // Save to a config file or database
        // For now, just return success
        return true;
    }

    public function getSystemSettings() {
        // Return system settings
        return [
            'site_name' => 'AI Powered Grading System',
            'version' => '1.0',
            'maintenance_mode' => false
        ];
    }

    public function updateSystemSettings($settings) {
        // Update settings
        return true;
    }
}
?>
