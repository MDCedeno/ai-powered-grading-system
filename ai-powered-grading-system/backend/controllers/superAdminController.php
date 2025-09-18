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

    public function getSystemLogs($search = '', $status = '', $logType = '', $logLevel = '', $sort = 'newest', $limit = 10)
    {
        $query = "SELECT logs.id, users.email as user_email, logs.log_type, logs.action, logs.details, logs.success, logs.failure_reason, logs.created_at FROM logs LEFT JOIN users ON logs.user_id = users.id WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (users.email LIKE :search OR logs.action LIKE :search OR logs.details LIKE :search OR logs.failure_reason LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($status && $status !== 'Filter by Status') {
            if ($status === 'Success') {
                $query .= " AND logs.success = 1";
            } elseif ($status === 'Failed') {
                $query .= " AND logs.success = 0";
            }
        }

        if ($logType && $logType !== 'Filter by Log Type') {
            $query .= " AND logs.log_type = :logType";
            $params['logType'] = $logType;
        }

        if ($logLevel && $logLevel !== 'Filter by Log Level') {
            // Map log levels to success/failure status and log types
            switch ($logLevel) {
                case 'INFO':
                    $query .= " AND logs.success = 1 AND logs.log_type IN ('authentication', 'account_lifecycle', 'system_action')";
                    break;
                case 'WARNING':
                    $query .= " AND logs.success = 1 AND logs.log_type IN ('permission_change', 'sensitive_data_access', 'data_modification')";
                    break;
                case 'ERROR':
                    $query .= " AND logs.success = 0 AND logs.log_type = 'failed_operation'";
                    break;
                case 'SECURITY':
                    $query .= " AND logs.log_type IN ('authentication', 'permission_change', 'sensitive_data_access') AND logs.success = 0";
                    break;
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
            // Determine log level based on success and log_type
            $logLevel = 'INFO'; // default
            if ($log['success'] == 0) {
                $logLevel = $log['log_type'] === 'authentication' || $log['log_type'] === 'permission_change' || $log['log_type'] === 'sensitive_data_access' ? 'SECURITY' : 'ERROR';
            } elseif ($log['log_type'] === 'permission_change' || $log['log_type'] === 'sensitive_data_access' || $log['log_type'] === 'data_modification') {
                $logLevel = 'WARNING';
            }

            return [
                'timestamp' => $log['created_at'],
                'user' => $log['user_email'] ?? 'Unknown',
                'log_type' => $log['log_type'],
                'action' => $log['action'],
                'details' => $log['details'],
                'status' => $log['success'] == 1 ? 'Success' : 'Failed',
                'failure_reason' => $log['failure_reason'],
                'log_level' => $logLevel
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

        // Calculate real database size
        $stmt = $this->pdo->prepare("SELECT
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = DATABASE()");
        $stmt->execute();
        $dbSize = $stmt->fetch()['size_mb'];
        $stats['db_size'] = $dbSize . ' MB';

        // Determine database health based on size thresholds
        $sizeGB = $dbSize / 1024;
        if ($sizeGB < 1) {
            $stats['db_health'] = 'Healthy';
            $stats['db_health_color'] = 'green';
            $stats['db_health_message'] = '';
        } elseif ($sizeGB < 2) {
            $stats['db_health'] = 'Normal';
            $stats['db_health_color'] = 'blue';
            $stats['db_health_message'] = '';
        } elseif ($sizeGB < 3) {
            $stats['db_health'] = 'Caution';
            $stats['db_health_color'] = 'orange';
            $stats['db_health_message'] = 'Database size is approaching critical levels. Consider optimizing or archiving old data.';
        } else {
            $stats['db_health'] = 'Critical';
            $stats['db_health_color'] = 'red';
            $stats['db_health_message'] = 'Database size is critical. Immediate action required: backup and optimize database.';
        }

        // Server status - assume online if API is responding
        $stats['server_status'] = 'Online';

        // Calculate uptime - time since server start (simplified)
        $uptime_seconds = time() - strtotime('today'); // Since midnight, or use a stored start time
        $uptime_days = floor($uptime_seconds / 86400);
        $uptime_hours = floor(($uptime_seconds % 86400) / 3600);
        $uptime_minutes = floor(($uptime_seconds % 3600) / 60);
        $stats['uptime'] = sprintf('%dd %dh %dm', $uptime_days, $uptime_hours, $uptime_minutes);

        // Last backup time from DB
        $stats['last_backup'] = $this->getLastBackupTime();

        // Auto-backup status
        $stats['auto_backup_enabled'] = $this->getAutoBackupStatus();

        return $stats;
    }

    public function getLastBackupTime()
    {
        $stmt = $this->pdo->prepare("SELECT backup_time FROM backup_records ORDER BY backup_time DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result && isset($result['backup_time'])) {
            return $result['backup_time'];
        } else {
            return 'Never';
        }
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

        // Record backup time in backup_records table
        $stmt = $this->pdo->prepare("INSERT INTO backup_records (backup_time) VALUES (NOW())");
        $stmt->execute();

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

    public function getAutoBackupStatus()
    {
        // Check if settings table exists, if not create it
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'auto_backup_enabled'");
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ? (bool)$result['setting_value'] : false;
    }

    public function setAutoBackupStatus($enabled)
    {
        // Check if settings table exists, if not create it
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('auto_backup_enabled', :value)
                                     ON DUPLICATE KEY UPDATE setting_value = :value");
        return $stmt->execute(['value' => $enabled ? 1 : 0]);
    }

    public function getAutoBackupInterval()
    {
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'auto_backup_interval'");
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ? (int)$result['setting_value'] : 24; // default 24 hours
    }

    public function setAutoBackupInterval($hours)
    {
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('auto_backup_interval', :value)
                                     ON DUPLICATE KEY UPDATE setting_value = :value");
        return $stmt->execute(['value' => $hours]);
    }

    private function ensureSettingsTable()
    {
        $stmt = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) UNIQUE NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $stmt->execute();
    }

    // New method to restore database from a backup file
    public function restoreDatabase($filename)
    {
        $backupDir = __DIR__ . '/../../backups/';
        $filePath = realpath($backupDir . $filename);

        // Security check: ensure file is inside backup directory
        if (!$filePath || strpos($filePath, realpath($backupDir)) !== 0) {
            return ['success' => false, 'message' => 'Invalid backup file path'];
        }

        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'Backup file does not exist'];
        }

        $sql = file_get_contents($filePath);
        try {
            $this->pdo->exec($sql);
            return ['success' => true, 'message' => 'Database restored successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error restoring database: ' . $e->getMessage()];
        }
    }

    // New method to get recent backup files (limit 4)
    public function getBackupFiles($limit = 4)
    {
        $backupDir = __DIR__ . '/../../backups/';
        $files = glob($backupDir . 'backup_*.sql');

        // Sort files by modification time descending
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $files = array_slice($files, 0, $limit);

        // Return filenames only
        return array_map(function ($file) use ($backupDir) {
            return basename($file);
        }, $files);
    }
}
