<?php
require_once __DIR__ . '/../config/db.php';

class CsvLogger {
    private $pdo;
    private $logDir;
    private $currentFile;
    private $fileHandle;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logDir = __DIR__ . '/../../logs/csv/';
        $this->initializeLogDirectory();
        $this->setCurrentLogFile();
    }

    /**
     * Initialize log directory structure
     */
    private function initializeLogDirectory() {
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }

        // Create monthly subdirectories for better organization
        $yearMonth = date('Y-m');
        $monthlyDir = $this->logDir . $yearMonth . '/';

        if (!file_exists($monthlyDir)) {
            mkdir($monthlyDir, 0755, true);
        }

        $this->logDir = $monthlyDir;
    }

    /**
     * Set the current log file based on date
     */
    private function setCurrentLogFile() {
        $date = date('Y-m-d');
        $this->currentFile = $this->logDir . 'audit_logs_' . $date . '.csv';
    }

    /**
     * Write log entry to CSV file
     */
    public function writeLog($user_id, $log_type, $action, $details = null, $success = 1, $failure_reason = null) {
        $this->setCurrentLogFile();

        // Get user email for logging
        $user_email = $this->getUserEmail($user_id);

        // Prepare log data
        $logData = [
            date('Y-m-d H:i:s'),
            $user_email ?: 'Unknown',
            $log_type,
            $action,
            $details ?: '',
            $success ? 'Success' : 'Failed',
            $failure_reason ?: ''
        ];

        // Ensure file exists and has headers
        $this->ensureFileWithHeaders();

        // Write to CSV file
        $this->writeToFile($logData);

        return true;
    }

    /**
     * Get user email by user ID
     */
    private function getUserEmail($user_id) {
        if (!$user_id) return null;

        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['email'] : null;
    }

    /**
     * Ensure CSV file exists with proper headers
     */
    private function ensureFileWithHeaders() {
        if (!file_exists($this->currentFile)) {
            $headers = ['timestamp', 'user_email', 'log_type', 'action', 'details', 'status', 'failure_reason'];
            $this->writeToFile($headers);
        }
    }

    /**
     * Write data array to CSV file
     */
    private function writeToFile($data) {
        $fileHandle = fopen($this->currentFile, 'a');

        if ($fileHandle === false) {
            error_log("Failed to open CSV log file: " . $this->currentFile);
            return false;
        }

        // Escape CSV fields properly
        $escapedData = array_map(function($field) {
            if (is_string($field) && (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false)) {
                return '"' . str_replace('"', '""', $field) . '"';
            }
            return $field;
        }, $data);

        fputcsv($fileHandle, $escapedData);
        fclose($fileHandle);

        return true;
    }

    /**
     * Export existing database logs to CSV
     */
    public function exportLogsToCsv($startDate = null, $endDate = null, $logType = null, $status = null) {
        // Create export filename
        $timestamp = date('Y-m-d_H-i-s');
        $filename = 'audit_logs_export_' . $timestamp . '.csv';
        $exportPath = $this->logDir . $filename;

        // Build query
        $query = "SELECT
            DATE_FORMAT(logs.created_at, '%Y-%m-%d %H:%i:%s') as timestamp,
            COALESCE(users.email, 'Unknown') as user_email,
            logs.log_type,
            logs.action,
            logs.details,
            CASE WHEN logs.success = 1 THEN 'Success' ELSE 'Failed' END as status,
            logs.failure_reason
        FROM logs
        LEFT JOIN users ON logs.user_id = users.id
        WHERE 1=1";

        $params = [];

        if ($startDate) {
            $query .= " AND DATE(logs.created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $query .= " AND DATE(logs.created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        if ($logType) {
            $query .= " AND logs.log_type = :log_type";
            $params['log_type'] = $logType;
        }

        if ($status) {
            if ($status === 'Success') {
                $query .= " AND logs.success = 1";
            } elseif ($status === 'Failed') {
                $query .= " AND logs.success = 0";
            }
        }

        $query .= " ORDER BY logs.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($logs)) {
            return ['success' => false, 'message' => 'No logs found matching criteria'];
        }

        // Write headers
        $headers = ['timestamp', 'user_email', 'log_type', 'action', 'details', 'status', 'failure_reason'];
        $this->writeToFile($headers);

        // Write log data
        foreach ($logs as $log) {
            $this->writeToFile([
                $log['timestamp'],
                $log['user_email'],
                $log['log_type'],
                $log['action'],
                $log['details'] ?: '',
                $log['status'],
                $log['failure_reason'] ?: ''
            ]);
        }

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $exportPath,
            'records_exported' => count($logs)
        ];
    }

    /**
     * Get list of available CSV log files
     */
    public function getCsvFiles($limit = 10) {
        $files = glob($this->logDir . 'audit_logs_*.csv');

        if (empty($files)) {
            return [];
        }

        // Sort files by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $files = array_slice($files, 0, $limit);

        $fileInfo = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $fileInfo[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'record_count' => $this->countRecordsInFile($file)
            ];
        }

        return $fileInfo;
    }

    /**
     * Count records in a CSV file (excluding header)
     */
    private function countRecordsInFile($file) {
        $lineCount = 0;
        if (($handle = fopen($file, 'r')) !== false) {
            // Skip header row
            fgetcsv($handle);

            // Count actual data rows
            while (($data = fgetcsv($handle)) !== false) {
                // Only count non-empty rows
                if (!empty(array_filter($data))) {
                    $lineCount++;
                }
            }
            fclose($handle);
        }
        return $lineCount;
    }

    /**
     * Get CSV logging configuration
     */
    public function getConfig() {
        return [
            'log_directory' => $this->logDir,
            'current_file' => basename($this->currentFile),
            'enabled' => $this->isCsvLoggingEnabled(),
            'file_rotation' => 'daily',
            'retention_days' => 30,
            'max_file_size_mb' => 100
        ];
    }

    /**
     * Check if CSV logging is enabled
     */
    public function isCsvLoggingEnabled() {
        // Check settings table for CSV logging configuration
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'csv_logging_enabled'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (bool)$result['setting_value'] : true; // Default to enabled
    }

    /**
     * Clean up old log files based on retention policy
     */
    public function cleanupOldLogs($retentionDays = 30) {
        $files = glob($this->logDir . 'audit_logs_*.csv');
        $deletedCount = 0;
        $processedCount = 0;

        foreach ($files as $file) {
            $fileModified = filemtime($file);
            $daysOld = (time() - $fileModified) / (60 * 60 * 24);
            $processedCount++;

            if ($daysOld > $retentionDays) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        return [
            'success' => true,
            'files_processed' => $processedCount,
            'files_deleted' => $deletedCount,
            'space_freed_mb' => 0 // Could be calculated if needed
        ];
    }

    /**
     * Get log statistics
     */
    public function getLogStats() {
        $files = $this->getCsvFiles(100); // Get all files for stats
        $totalRecords = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $totalRecords += $file['record_count'];
            $totalSize += $file['size'];
        }

        return [
            'total_files' => count($files),
            'total_records' => $totalRecords,
            'total_size' => round($totalSize / 1024 / 1024, 2) . ' MB',
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'oldest_file' => !empty($files) ? $files[count($files)-1]['created_at'] : null,
            'newest_file' => !empty($files) ? $files[0]['created_at'] : null
        ];
    }
}
?>
