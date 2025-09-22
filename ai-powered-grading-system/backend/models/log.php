<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/CsvLogger.php';

class Log {
    private $pdo;
    private $csvLogger;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->csvLogger = new CsvLogger($pdo);
    }

    public function create($user_id, $log_type, $action, $details = null, $success = 1, $failure_reason = null) {
        // Insert into database (existing functionality)
        $dbResult = $this->createInDatabase($user_id, $log_type, $action, $details, $success, $failure_reason);

        // Write to CSV file (new functionality)
        $csvResult = $this->csvLogger->writeLog($user_id, $log_type, $action, $details, $success, $failure_reason);

        // Return true if either operation succeeds (for backward compatibility)
        return $dbResult || $csvResult;
    }

    /**
     * Create log entry in database (original functionality)
     */
    private function createInDatabase($user_id, $log_type, $action, $details = null, $success = 1, $failure_reason = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO logs (user_id, log_type, action, details, success, failure_reason) VALUES (:user_id, :log_type, :action, :details, :success, :failure_reason)");
            return $stmt->execute([
                'user_id' => $user_id,
                'log_type' => $log_type,
                'action' => $action,
                'details' => $details,
                'success' => $success,
                'failure_reason' => $failure_reason
            ]);
        } catch (Exception $e) {
            error_log("Database logging failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM logs ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM logs WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Export logs to CSV file
     */
    public function exportToCsv($startDate = null, $endDate = null, $logType = null, $status = null) {
        return $this->csvLogger->exportLogsToCsv($startDate, $endDate, $logType, $status);
    }

    /**
     * Get list of CSV log files
     */
    public function getCsvFiles($limit = 10) {
        return $this->csvLogger->getCsvFiles($limit);
    }

    /**
     * Get CSV logging configuration
     */
    public function getCsvConfig() {
        return $this->csvLogger->getConfig();
    }

    /**
     * Get CSV log statistics
     */
    public function getCsvStats() {
        return $this->csvLogger->getLogStats();
    }

    /**
     * Clean up old CSV log files
     */
    public function cleanupOldCsvLogs($retentionDays = 30) {
        return $this->csvLogger->cleanupOldLogs($retentionDays);
    }

    /**
     * Check if CSV logging is enabled
     */
    public function isCsvLoggingEnabled() {
        return $this->csvLogger->isCsvLoggingEnabled();
    }
}
?>
