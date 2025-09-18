<?php
require_once __DIR__ . '/../config/db.php';

class Log {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($user_id, $log_type, $action, $details = null, $success = 1, $failure_reason = null) {
        $stmt = $this->pdo->prepare("INSERT INTO logs (user_id, log_type, action, details, success, failure_reason) VALUES (:user_id, :log_type, :action, :details, :success, :failure_reason)");
        return $stmt->execute([
            'user_id' => $user_id,
            'log_type' => $log_type,
            'action' => $action,
            'details' => $details,
            'success' => $success,
            'failure_reason' => $failure_reason
        ]);
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
}
?>
