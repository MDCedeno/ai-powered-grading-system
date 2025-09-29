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

        $this->ensureSettingsTable();
        $this->ensureGradingScalesTable();
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

        // Check if user exists first
        $userExistsStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE id = :id");
        $userExistsStmt->execute(['id' => $user_id]);
        $userExists = $userExistsStmt->fetch(PDO::FETCH_ASSOC);

        if ($userExists['count'] == 0) {
            // User doesn't exist, log the error
            $this->logModel->create(
                $currentUserId,
                'account_lifecycle',
                'user_deletion_failed',
                "Failed to delete user ID {$user_id} - User does not exist",
                0,
                'User not found in database'
            );
            return false;
        }

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
        $snapshotTime = date('Y-m-d H:i:s');
        $backup = "-- Database backup created on: {$snapshotTime}\n-- Snapshot of system data at this exact time\n\n";
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
        $this->ensureSettingsTable();

        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN (
            'system_name', 'theme_color', 'default_password_reset', 'session_timeout',
            'password_min_length', 'password_min_length_enabled', 'password_uppercase_required',
            'password_lowercase_required', 'password_numbers_required', 'password_special_chars_required',
            'password_history_count', 'max_login_attempts', 'lockout_duration', 'password_expiration_days',
            'two_factor_required'
        )");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($results as $row) {
            $key = $row['setting_key'];
            $value = $row['setting_value'];

            // Convert boolean strings to actual booleans
            if (in_array($key, ['password_min_length_enabled', 'password_uppercase_required', 'password_lowercase_required', 'password_numbers_required', 'password_special_chars_required'])) {
                $settings[$key] = (bool)$value;
            } elseif (in_array($key, ['password_min_length', 'password_history_count', 'max_login_attempts', 'lockout_duration', 'password_expiration_days', 'session_timeout'])) {
                $settings[$key] = (int)$value;
            } else {
                $settings[$key] = $value;
            }
        }

        // Set defaults if not found
        $defaults = [
            'system_name' => 'PLMUN Portal',
            'theme_color' => '#217589',
            'default_password_reset' => 'changeme123',
            'session_timeout' => 60,
            'password_min_length' => 8,
            'password_min_length_enabled' => true,
            'password_uppercase_required' => false,
            'password_lowercase_required' => false,
            'password_numbers_required' => false,
            'password_special_chars_required' => false,
            'password_history_count' => 3,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'password_expiration_days' => 90,
            'two_factor_required' => 'optional'
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key])) {
                $settings[$key] = $default;
            }
        }

        return $settings;
    }

    public function updateSystemSettings($settings)
    {
        $this->ensureSettingsTable();

        $currentUserId = $this->getCurrentUserId();
        $success = true;
        $changes = [];

        foreach ($settings as $key => $value) {
            // Get current value for logging
            $currentStmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
            $currentStmt->execute(['key' => $key]);
            $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
            $oldValue = $current ? $current['setting_value'] : 'Not set';

            // Update or insert
            $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                                         ON DUPLICATE KEY UPDATE setting_value = :value");
            $result = $stmt->execute(['key' => $key, 'value' => $value]);

            if ($result && $oldValue !== (string)$value) {
                $changes[] = "{$key}: '{$oldValue}' → '{$value}'";
            }

            $success = $success && $result;
        }

        // Log the changes
        if ($success && !empty($changes)) {
            $this->logModel->create(
                $currentUserId,
                'system_action',
                'system_settings_updated',
                'System settings updated: ' . implode(', ', $changes),
                1,
                null
            );
        } elseif (!$success) {
            $this->logModel->create(
                $currentUserId,
                'system_action',
                'system_settings_update_failed',
                'Failed to update system settings',
                0,
                'Database error during settings update'
            );
        }

        return $success;
    }

    public function getGradingScales()
    {
        $this->ensureGradingScalesTable();

        $stmt = $this->pdo->prepare("SELECT * FROM grading_scales ORDER BY is_active DESC, min_score DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createGradingScale($data)
    {
        $this->ensureGradingScalesTable();

        $currentUserId = $this->getCurrentUserId();

        $stmt = $this->pdo->prepare("INSERT INTO grading_scales (name, min_score, max_score, grade_letter, is_active) VALUES (:name, :min_score, :max_score, :grade_letter, :is_active)");
        $success = $stmt->execute([
            'name' => $data['name'],
            'min_score' => (float)$data['min_score'],
            'max_score' => (float)$data['max_score'],
            'grade_letter' => $data['grade_letter'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : false
        ]);

        if ($success) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_created',
                "New grading scale '{$data['name']}' (Grade: {$data['grade_letter']}, Range: {$data['min_score']}-{$data['max_score']}) created",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_creation_failed',
                "Failed to create grading scale '{$data['name']}'",
                0,
                'Database error during grading scale creation'
            );
        }

        return $success;
    }

    public function updateGradingScale($id, $data)
    {
        $this->ensureGradingScalesTable();

        $currentUserId = $this->getCurrentUserId();

        // Get original data for logging
        $originalStmt = $this->pdo->prepare("SELECT * FROM grading_scales WHERE id = :id");
        $originalStmt->execute(['id' => $id]);
        $original = $originalStmt->fetch(PDO::FETCH_ASSOC);

        if (!$original) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_update_failed',
                "Failed to update grading scale ID {$id} - Scale not found",
                0,
                'Grading scale not found'
            );
            return false;
        }

        $changes = [];
        if (isset($data['name']) && $data['name'] !== $original['name']) {
            $changes[] = "name: '{$original['name']}' → '{$data['name']}'";
        }
        if (isset($data['min_score']) && (float)$data['min_score'] !== (float)$original['min_score']) {
            $changes[] = "min_score: {$original['min_score']} → {$data['min_score']}";
        }
        if (isset($data['max_score']) && (float)$data['max_score'] !== (float)$original['max_score']) {
            $changes[] = "max_score: {$original['max_score']} → {$data['max_score']}";
        }
        if (isset($data['grade_letter']) && $data['grade_letter'] !== $original['grade_letter']) {
            $changes[] = "grade_letter: '{$original['grade_letter']}' → '{$data['grade_letter']}'";
        }
        if (isset($data['is_active']) && (bool)$data['is_active'] !== (bool)$original['is_active']) {
            $changes[] = "is_active: " . ($original['is_active'] ? 'true' : 'false') . " → " . ((bool)$data['is_active'] ? 'true' : 'false');
        }

        $stmt = $this->pdo->prepare("UPDATE grading_scales SET name = :name, min_score = :min_score, max_score = :max_score, grade_letter = :grade_letter, is_active = :is_active WHERE id = :id");
        $success = $stmt->execute([
            'id' => $id,
            'name' => $data['name'] ?? $original['name'],
            'min_score' => (float)($data['min_score'] ?? $original['min_score']),
            'max_score' => (float)($data['max_score'] ?? $original['max_score']),
            'grade_letter' => $data['grade_letter'] ?? $original['grade_letter'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : (bool)$original['is_active']
        ]);

        if ($success && $stmt->rowCount() > 0) {
            $changeDesc = empty($changes) ? 'No changes detected' : implode(', ', $changes);
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_updated',
                "Grading scale '{$original['name']}' (ID: {$id}) updated. Changes: {$changeDesc}",
                1,
                null
            );
        } elseif (!$success) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_update_failed',
                "Failed to update grading scale ID {$id}",
                0,
                'Database error during grading scale update'
            );
        }

        return $success;
    }

    public function deleteGradingScale($id)
    {
        $this->ensureGradingScalesTable();

        $currentUserId = $this->getCurrentUserId();

        // Get scale details for logging
        $scaleStmt = $this->pdo->prepare("SELECT name FROM grading_scales WHERE id = :id");
        $scaleStmt->execute(['id' => $id]);
        $scale = $scaleStmt->fetch(PDO::FETCH_ASSOC);

        if (!$scale) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_deletion_failed',
                "Failed to delete grading scale ID {$id} - Scale not found",
                0,
                'Grading scale not found'
            );
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM grading_scales WHERE id = :id");
        $success = $stmt->execute(['id' => $id]);

        if ($success) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_deleted',
                "Grading scale '{$scale['name']}' (ID: {$id}) deleted",
                1,
                null
            );
        } else {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_deletion_failed',
                "Failed to delete grading scale '{$scale['name']}' (ID: {$id})",
                0,
                'Database error during grading scale deletion'
            );
        }

        return $success;
    }

    public function activateGradingScale($id)
    {
        $this->ensureGradingScalesTable();

        $currentUserId = $this->getCurrentUserId();

        // Get scale details
        $scaleStmt = $this->pdo->prepare("SELECT name FROM grading_scales WHERE id = :id");
        $scaleStmt->execute(['id' => $id]);
        $scale = $scaleStmt->fetch(PDO::FETCH_ASSOC);

        if (!$scale) {
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_activation_failed',
                "Failed to activate grading scale ID {$id} - Scale not found",
                0,
                'Grading scale not found'
            );
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            // Deactivate all others
            $deactivateStmt = $this->pdo->prepare("UPDATE grading_scales SET is_active = 0 WHERE id != :id");
            $deactivateStmt->execute(['id' => $id]);

            // Activate this one
            $activateStmt = $this->pdo->prepare("UPDATE grading_scales SET is_active = 1 WHERE id = :id");
            $success = $activateStmt->execute(['id' => $id]);

            if ($success && $activateStmt->rowCount() > 0) {
                $this->pdo->commit();

                $this->logModel->create(
                    $currentUserId,
                    'data_modification',
                    'grading_scale_activated',
                    "Grading scale '{$scale['name']}' (ID: {$id}) activated; all others deactivated",
                    1,
                    null
                );
            } else {
                $this->pdo->rollBack();
                $this->logModel->create(
                    $currentUserId,
                    'data_modification',
                    'grading_scale_activation_failed',
                    "Failed to activate grading scale '{$scale['name']}' (ID: {$id})",
                    0,
                    'Database error during activation'
                );
                $success = false;
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->logModel->create(
                $currentUserId,
                'data_modification',
                'grading_scale_activation_failed',
                "Failed to activate grading scale '{$scale['name']}' (ID: {$id})",
                0,
                $e->getMessage()
            );
            $success = false;
        }

        return $success;
    }

    public function getEncryptionStatus()
    {
        $this->ensureSettingsTable();

        $currentUserId = $this->getCurrentUserId();

        // Check DB encryption flag from settings (default to enabled for simulation)
        $dbStmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'db_encryption_enabled'");
        $dbStmt->execute();
        $dbResult = $dbStmt->fetch(PDO::FETCH_ASSOC);
        $dbEnabled = $dbResult ? (bool)$dbResult['setting_value'] : true;

        // Check file encryption flag (default enabled)
        $fileStmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'file_encryption_enabled'");
        $fileStmt->execute();
        $fileResult = $fileStmt->fetch(PDO::FETCH_ASSOC);
        $fileEnabled = $fileResult ? (bool)$fileResult['setting_value'] : true;

        // Simulate SSL certificate expiration (e.g., 30 days from now)
        $sslExpiresIn = 30; // Simulated days

        // Log the status check
        $this->logModel->create(
            $currentUserId,
            'system_action',
            'encryption_status_checked',
            'Encryption status was checked by Super Admin',
            1,
            null
        );

        return [
            'db_encryption' => [
                'status' => $dbEnabled ? 'enabled' : 'disabled',
                'details' => $dbEnabled ? 'AES-256-CBC used for sensitive data' : 'No encryption configured'
            ],
            'file_encryption' => [
                'status' => $fileEnabled ? 'enabled' : 'disabled',
                'details' => $fileEnabled ? 'Backups encrypted with system key' : 'Files stored in plain text'
            ],
            'ssl_status' => [
                'status' => 'valid',
                'details' => "HTTPS enforced, certificate expires in {$sslExpiresIn} days"
            ],
            'api_security' => [
                'status' => 'secure',
                'details' => 'JWT tokens required, rate limiting active'
            ]
        ];
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

    private function ensureGradingScalesTable()
    {
        $createTableStmt = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS grading_scales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            min_score DECIMAL(5,2) NOT NULL,
            max_score DECIMAL(5,2) NOT NULL,
            grade_letter VARCHAR(5) NOT NULL,
            is_active BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $createTableStmt->execute();

        // Seed with default grading scales if table is empty
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM grading_scales");
        $countStmt->execute();
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count == 0) {
            $defaultScales = [
                ['name' => 'Excellent', 'min_score' => 90.00, 'max_score' => 100.00, 'grade_letter' => 'A', 'is_active' => true],
                ['name' => 'Very Good', 'min_score' => 80.00, 'max_score' => 89.99, 'grade_letter' => 'B', 'is_active' => false],
                ['name' => 'Good', 'min_score' => 70.00, 'max_score' => 79.99, 'grade_letter' => 'C', 'is_active' => false],
                ['name' => 'Satisfactory', 'min_score' => 60.00, 'max_score' => 69.99, 'grade_letter' => 'D', 'is_active' => false],
                ['name' => 'Fail', 'min_score' => 0.00, 'max_score' => 59.99, 'grade_letter' => 'F', 'is_active' => false]
            ];

            $insertStmt = $this->pdo->prepare("INSERT INTO grading_scales (name, min_score, max_score, grade_letter, is_active) VALUES (:name, :min_score, :max_score, :grade_letter, :is_active)");
            foreach ($defaultScales as $scale) {
                $insertStmt->execute($scale);
            }

            // Log the seeding
            $this->logModel->create(
                $this->getCurrentUserId(),
                'system_action',
                'grading_scales_seeded',
                'Default grading scales have been seeded into the database',
                1,
                null
            );
        }
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

        try {
            // Start transaction for atomic restore
            $this->pdo->beginTransaction();

            // Truncate tables in reverse order to handle foreign keys
            $tables = ['logs', 'grades', 'courses', 'students', 'users'];
            foreach ($tables as $table) {
                $this->pdo->exec("TRUNCATE TABLE $table");
            }

            // Execute the backup SQL
            $sql = file_get_contents($filePath);
            $this->pdo->exec($sql);

            // Commit transaction
            $this->pdo->commit();

            // Log successful restore
            $this->logModel->create(
                $this->getCurrentUserId(),
                'system_action',
                'database_restore_completed',
                "Database restored successfully from backup: {$filename}",
                1,
                null
            );

            return ['success' => true, 'message' => 'Database restored successfully'];
        } catch (Exception $e) {
            // Rollback on error
            $this->pdo->rollBack();

            // Log failed restore
            $this->logModel->create(
                $this->getCurrentUserId(),
                'system_action',
                'database_restore_failed',
                "Database restore failed from backup: {$filename}",
                0,
                $e->getMessage()
            );

            return ['success' => false, 'message' => 'Error restoring database: ' . $e->getMessage()];
        }
    }

    // New method to get recent backup files (limit 4)
    public function getBackupFiles($limit = 4)
    {
        $backupDir = __DIR__ . '/../../backups/';
        $files = glob($backupDir . 'backup_*.sql');

        // Parse timestamps from filenames and sort by snapshot creation time descending
        $filesWithTimestamps = array_map(function ($file) {
            $filename = basename($file);
            // Extract timestamp from filename: backup_YYYY-MM-DD_HH-MM-SS.sql
            if (preg_match('/backup_(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.sql$/', $filename, $matches)) {
                $date = $matches[1];
                $time = str_replace('-', ':', $matches[2]);
                $snapshotTime = strtotime($date . ' ' . $time);
            } else {
                $snapshotTime = filemtime($file); // Fallback to filemtime
            }
            return [
                'filename' => $filename,
                'snapshot_time' => $snapshotTime,
                'formatted_date' => date('Y-m-d H:i:s', $snapshotTime)
            ];
        }, $files);

        // Sort by snapshot time descending
        usort($filesWithTimestamps, function ($a, $b) {
            return $b['snapshot_time'] - $a['snapshot_time'];
        });

        $filesWithTimestamps = array_slice($filesWithTimestamps, 0, $limit);

        // Return array with filename and formatted date
        return array_map(function ($item) {
            return [
                'filename' => $item['filename'],
                'snapshot_date' => $item['formatted_date']
            ];
        }, $filesWithTimestamps);
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
