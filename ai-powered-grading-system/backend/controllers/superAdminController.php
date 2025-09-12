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

    public function getAllUsers($search = '', $role = '', $sort = 'name') {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (name LIKE :search OR email LIKE :search OR id LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($role) {
            $roleMap = ['Super Admin' => 1, 'MIS Admin' => 2, 'Professor' => 3, 'Student' => 4];
            if (isset($roleMap[$role])) {
                $query .= " AND role_id = :role";
                $params['role'] = $roleMap[$role];
            }
        }

        $sortMap = ['name' => 'name', 'date' => 'created_at'];
        $sortField = isset($sortMap[$sort]) ? $sortMap[$sort] : 'name';
        $query .= " ORDER BY $sortField ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deactivateUser($user_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getSystemLogs($search = '', $status = '', $sort = 'newest') {
        $query = "SELECT * FROM logs WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (user_id LIKE :search OR action LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($status) {
            $query .= " AND status = :status";
            $params['status'] = $status;
        }

        $sortOrder = $sort === 'oldest' ? 'ASC' : 'DESC';
        $query .= " ORDER BY created_at $sortOrder";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activateUser($user_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 1 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function updateUser($user_id, $data) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, role_id = :role_id WHERE id = :id");
        return $stmt->execute([
            'id' => $user_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);
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

        // Error logs in last 24 hours
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM logs WHERE status = 'error' AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute();
        $stats['error_logs_24h'] = $stmt->fetch()['count'];

        // Database health - simple connection check
        try {
            $stmt = $this->pdo->prepare("SELECT 1");
            $stmt->execute();
            $stats['db_health'] = 'Healthy';
        } catch (Exception $e) {
            $stats['db_health'] = 'Unhealthy';
        }

        // Server status - assume online if API is responding
        $stats['server_status'] = 'Online';

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
