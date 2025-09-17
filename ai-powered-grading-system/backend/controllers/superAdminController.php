<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/log.php';

class SuperAdminController
{
    private $userModel;
    private $courseModel;
    private $logModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
        $this->courseModel = new Course($pdo);
        $this->logModel = new Log($pdo);
        $this->pdo = $pdo;
    }

    public function getAllUsers($search = '', $role = '', $sort = 'name', $limit = 10, $userId = '')
    {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (name LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($userId) {
            $query .= " AND id LIKE :userId";
            $params['userId'] = '%' . $userId . '%';
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

        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deactivateUser($user_id)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getSystemLogs($search = '', $status = '', $sort = 'newest', $limit = 10)
    {
        $query = "SELECT logs.id, users.email as user_email, logs.action, logs.details, logs.created_at FROM logs LEFT JOIN users ON logs.user_id = users.id WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (users.email LIKE :search OR logs.action LIKE :search OR logs.details LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($status && $status !== 'Filter by Status') {
            if ($status === 'Success') {
                $query .= " AND logs.action = 'success'";
            } elseif ($status === 'Failed') {
                $query .= " AND logs.action = 'error'";
            }
        }

        $sortOrder = $sort === 'oldest' ? 'ASC' : 'DESC';
        $query .= " ORDER BY logs.created_at $sortOrder";
        $query .= " LIMIT $limit";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transform to match frontend expectations with real data
        return array_map(function ($log) {
            return [
                'timestamp' => $log['created_at'],
                'user' => $log['user_email'] ?? 'Unknown',
                'action' => $log['action'],
                'details' => $log['details'],
                'status' => $log['action'] === 'success' ? 'Success' : 'Failed'
            ];
        }, $logs);
    }

    public function activateUser($user_id)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET active = 1 WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function updateUser($user_id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, role_id = :role_id WHERE id = :id");
        return $stmt->execute([
            'id' => $user_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);
    }

    public function deleteUser($user_id)
    {
        // Soft delete or hard delete? For now, hard delete
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function bulkUpdateUsers($userIds, $action)
    {
        if (!is_array($userIds) || empty($userIds)) {
            return ['success' => false, 'message' => 'Invalid user IDs provided'];
        }

        $allowedActions = ['activate', 'deactivate', 'delete'];
        if (!in_array($action, $allowedActions)) {
            return ['success' => false, 'message' => 'Invalid action provided'];
        }

        try {
            $placeholders = str_repeat('?,', count($userIds) - 1) . '?';

            if ($action === 'activate') {
                $query = "UPDATE users SET active = 1 WHERE id IN ($placeholders)";
            } elseif ($action === 'deactivate') {
                $query = "UPDATE users SET active = 0 WHERE id IN ($placeholders)";
            } elseif ($action === 'delete') {
                $query = "DELETE FROM users WHERE id IN ($placeholders)";
            }

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($userIds);
            $affectedRows = $stmt->rowCount();

            return [
                'success' => true,
                'message' => "Successfully {$action}d {$affectedRows} user(s)",
                'affected_rows' => $affectedRows
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getSystemStats()
    {
        // Get counts of users, students, courses, grades
        $stats = [];
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE active = 1");
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
        // Adjusted query to match actual logs table columns
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM logs WHERE action = 'error' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
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

        // Calculate uptime - time since server start (simplified)
        $uptime_seconds = time() - strtotime('today'); // Since midnight, or use a stored start time
        $uptime_days = floor($uptime_seconds / 86400);
        $uptime_hours = floor(($uptime_seconds % 86400) / 3600);
        $uptime_minutes = floor(($uptime_seconds % 3600) / 60);
        $stats['uptime'] = sprintf('%dd %dh %dm', $uptime_days, $uptime_hours, $uptime_minutes);

        // Last backup time
        $backup_dir = __DIR__ . '/../../backups/';
        if (is_dir($backup_dir)) {
            $files = glob($backup_dir . 'backup_*.sql');
            if (!empty($files)) {
                $latest_backup = max(array_map('filemtime', $files));
                $stats['last_backup'] = date('Y-m-d H:i:s', $latest_backup);
            } else {
                $stats['last_backup'] = 'Never';
            }
        } else {
            $stats['last_backup'] = 'Never';
        }

        return $stats;
    }

    public function backupDatabase()
    {
        // Simple backup to SQL file
        $tables = ['users', 'students', 'courses', 'grades', 'logs'];
        $backup = '';
        foreach ($tables as $table) {
            $stmt = $this->pdo->prepare("SELECT * FROM $table");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $backup .= "-- Table: $table\n";
            foreach ($rows as $row) {
                $values = array_map(function ($val) {
                    return $this->pdo->quote($val);
                }, array_values($row));
                $backup .= "INSERT INTO $table (" . implode(',', array_keys($row)) . ") VALUES (" . implode(',', $values) . ");\n";
            }
            $backup .= "\n";
        }
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        file_put_contents(__DIR__ . '/../../backups/' . $filename, $backup);
        return ['success' => true, 'file' => $filename];
    }

    public function getAIConfig()
    {
        // For now, return dummy config
        return [
            'ai_endpoint' => 'http://localhost:5000',
            'model' => 'gpt-3.5-turbo',
            'enabled' => true
        ];
    }

    public function updateAIConfig($config)
    {
        // Save to a config file or database
        // For now, just return success
        return true;
    }

    public function getSystemSettings()
    {
        // Return system settings
        return [
            'site_name' => 'AI Powered Grading System',
            'version' => '1.0',
            'maintenance_mode' => false
        ];
    }

    public function updateSystemSettings($settings)
    {
        // Update settings
        return true;
    }
}
