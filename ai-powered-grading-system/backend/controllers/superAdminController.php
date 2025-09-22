<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/log.php';
require_once __DIR__ . '/../models/CsvLogger.php';

class SuperAdminController
{
    private $userModel;
    private $courseModel;
    private $logModel;
    private $csvLogger;
    private $pdo;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
        $this->courseModel = new Course($pdo);
        $this->logModel = new Log($pdo);
        $this->csvLogger = new CsvLogger($pdo);
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
        // Get current user ID for logging (assuming Super Admin is user ID 1 for now)
        // In a real application, this should come from session/authentication
        $currentUserId = $this->getCurrentUserId();

        $stmt = $this->pdo->prepare("UPDATE users SET active = 0 WHERE id = :id");
        $success = $stmt->execute(['id' => $user_id]);

        // Log the user deactivation using unified Log model
        if ($success) {
            // Get user details for better logging
            $userDetails = $this->getUserDetails($user_id);
            $this->logModel->create(
                $currentUserId,
                'permission_change',
                'user_deactivated',
                "User '{$userDetails['name']}' (ID: {$user_id}, Email: {$userDetails['email']}) was deactivated by Super Admin",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'permission_change',
                'user_deactivation_failed',
                "Failed to deactivate user ID {$user_id}",
                0,
                'Database error during user deactivation'
            );
        }

        return $success;
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
        // Get current user ID for logging (assuming Super Admin is user ID 1 for now)
        // In a real application, this should come from session/authentication
        $currentUserId = $this->getCurrentUserId();

        $stmt = $this->pdo->prepare("UPDATE users SET active = 1 WHERE id = :id");
        $success = $stmt->execute(['id' => $user_id]);

        // Log the user activation using unified Log model
        if ($success) {
            // Get user details for better logging
            $userDetails = $this->getUserDetails($user_id);
            $this->logModel->create(
                $currentUserId,
                'permission_change',
                'user_activated',
                "User '{$userDetails['name']}' (ID: {$user_id}, Email: {$userDetails['email']}) was activated by Super Admin",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'permission_change',
                'user_activation_failed',
                "Failed to activate user ID {$user_id}",
                0,
                'Database error during user activation'
            );
        }

        return $success;
    }

    public function updateUser($user_id, $data)
    {
        // Get current user ID for logging (assuming Super Admin is user ID 1 for now)
        // In a real application, this should come from session/authentication
        $currentUserId = $this->getCurrentUserId();

        // Check if user exists first
        $userExistsStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE id = :id");
        $userExistsStmt->execute(['id' => $user_id]);
        $userExists = $userExistsStmt->fetch(PDO::FETCH_ASSOC);

        if ($userExists['count'] == 0) {
            // User doesn't exist, log the error
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'user_update_failed',
                "Failed to update user ID {$user_id} - User does not exist",
                0,
                'User not found in database'
            );
            return false;
        }

        // Get original user data for comparison
        $originalData = $this->getUserDetails($user_id);

        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, role_id = :role_id WHERE id = :id");
        $success = $stmt->execute([
            'id' => $user_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);

        // Check if any rows were actually affected
        $affectedRows = $stmt->rowCount();

        // Log the user update using unified Log model
        if ($success && $affectedRows > 0) {
            // Get updated user details for better logging
            $updatedData = $this->getUserDetails($user_id);

            // Create detailed change description
            $changes = [];
            if ($originalData['name'] !== $updatedData['name']) {
                $changes[] = "name: '{$originalData['name']}' → '{$updatedData['name']}'";
            }
            if ($originalData['email'] !== $updatedData['email']) {
                $changes[] = "email: '{$originalData['email']}' → '{$updatedData['email']}'";
            }
            if ($originalData['role_id'] !== $updatedData['role_id']) {
                $roleMap = [1 => 'Super Admin', 2 => 'MIS Admin', 3 => 'Professor', 4 => 'Student'];
                $oldRole = $roleMap[$originalData['role_id']] ?? 'Unknown';
                $newRole = $roleMap[$updatedData['role_id']] ?? 'Unknown';
                $changes[] = "role: '{$oldRole}' → '{$newRole}'";
            }

            $changeDescription = empty($changes) ? "No changes detected" : implode(', ', $changes);

            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'user_updated',
                "User '{$updatedData['name']}' (ID: {$user_id}) was updated by Super Admin. Changes: {$changeDescription}",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'user_update_failed',
                "Failed to update user ID {$user_id} - No rows affected",
                0,
                'No database rows were updated'
            );
        }

        return $success && $affectedRows > 0;
    }

    public function deleteUser($user_id)
    {
        // Get current user ID for logging (assuming Super Admin is user ID 1 for now)
        // In a real application, this should come from session/authentication
        $currentUserId = $this->getCurrentUserId();

        // Get user details before deletion for better logging
        $userDetails = $this->getUserDetails($user_id);

        // Soft delete or hard delete? For now, hard delete
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $success = $stmt->execute(['id' => $user_id]);

        // Log the user deletion using unified Log model
        if ($success) {
            $this->logModel->create(
                $currentUserId,
                'account_lifecycle',
                'user_deleted',
                "User '{$userDetails['name']}' (ID: {$user_id}, Email: {$userDetails['email']}) was permanently deleted by Super Admin",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'account_lifecycle',
                'user_deletion_failed',
                "Failed to delete user ID {$user_id}",
                0,
                'Database error during user deletion'
            );
        }

        return $success;
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
        $success = file_put_contents(__DIR__ . '/../../backups/' . $filename, $backup);

        // Record backup time in backup_records table
        $stmt = $this->pdo->prepare("INSERT INTO backup_records (backup_time) VALUES (NOW())");
        $backupRecorded = $stmt->execute();

        // Log the database backup using the Log model (writes to both DB and CSV)
        if ($success && $backupRecorded) {
            $this->logModel->create(
                1, // Use Super Admin user_id for system actions
                'system_action',
                'database_backup_created',
                "Database backup created successfully: {$filename}",
                1,
                null
            );
        } else {
            $this->logModel->create(
                1, // Use Super Admin user_id for system actions
                'system_action',
                'database_backup_failed',
                "Failed to create database backup. File write: " . ($success ? 'Success' : 'Failed') . ", DB record: " . ($backupRecorded ? 'Success' : 'Failed'),
                0,
                'Database backup creation failed'
            );
        }

        return ['success' => $success && $backupRecorded, 'file' => $filename];
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

    // CSV Logging Methods

    /**
     * Export logs to CSV file
     */
    public function exportLogsToCsv($startDate = null, $endDate = null, $logType = null, $status = null)
    {
        return $this->logModel->exportToCsv($startDate, $endDate, $logType, $status);
    }

    /**
     * Get list of CSV log files
     */
    public function getCsvLogFiles($limit = 10)
    {
        return $this->logModel->getCsvFiles($limit);
    }

    /**
     * Get CSV logging configuration
     */
    public function getCsvConfig()
    {
        return $this->logModel->getCsvConfig();
    }

    /**
     * Get CSV log statistics
     */
    public function getCsvStats()
    {
        return $this->logModel->getCsvStats();
    }

    /**
     * Clean up old CSV log files
     */
    public function cleanupOldCsvLogs($retentionDays = 30)
    {
        return $this->logModel->cleanupOldCsvLogs($retentionDays);
    }

    /**
     * Check if CSV logging is enabled
     */
    public function isCsvLoggingEnabled()
    {
        return $this->logModel->isCsvLoggingEnabled();
    }

    /**
     * Set CSV logging enabled/disabled
     */
    public function setCsvLoggingEnabled($enabled)
    {
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('csv_logging_enabled', :value)
                                     ON DUPLICATE KEY UPDATE setting_value = :value");
        return $stmt->execute(['value' => $enabled ? 1 : 0]);
    }

    /**
     * Set CSV log retention days
     */
    public function setCsvRetentionDays($days)
    {
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('csv_retention_days', :value)
                                     ON DUPLICATE KEY UPDATE setting_value = :value");
        return $stmt->execute(['value' => $days]);
    }

    /**
     * Get CSV log retention days
     */
    public function getCsvRetentionDays()
    {
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'csv_retention_days'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['setting_value'] : 30; // Default 30 days
    }

    /**
     * Download CSV log file
     */
    public function downloadCsvFile($filename)
    {
        $logDir = __DIR__ . '/../../logs/csv/';

        // Search for the file in all subdirectories
        $filePath = $this->findCsvFile($logDir, $filename);

        if (!$filePath) {
            return ['success' => false, 'message' => 'File does not exist'];
        }

        return [
            'success' => true,
            'file_path' => $filePath,
            'filename' => $filename,
            'size' => filesize($filePath)
        ];
    }

    /**
     * Find CSV file in directory structure
     */
    private function findCsvFile($baseDir, $filename)
    {
        // First check if file exists directly in base directory
        $directPath = $baseDir . $filename;
        if (file_exists($directPath)) {
            return $directPath;
        }

        // Search in subdirectories
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }

        return null;
    }

    /**
     * Delete CSV log file
     */
    public function deleteCsvFile($filename)
    {
        $logDir = __DIR__ . '/../../logs/csv/';

        // Search for the file in all subdirectories
        $filePath = $this->findCsvFile($logDir, $filename);

        if (!$filePath) {
            return ['success' => false, 'message' => 'File does not exist'];
        }

        if (unlink($filePath)) {
            return ['success' => true, 'message' => 'File deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete file'];
        }
    }

    /**
     * Get current user ID for logging purposes
     * In a real application, this should come from session/authentication
     */
    private function getCurrentUserId()
    {
        // For now, assume Super Admin is user ID 1
        // In production, this should be retrieved from session or authentication context
        return 1;
    }

    /**
     * Get user details by user ID for logging purposes
     */
    private function getUserDetails($user_id)
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role_id FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: ['id' => $user_id, 'name' => 'Unknown', 'email' => 'Unknown', 'role_id' => 0];
    }
}
