<?php
require_once __DIR__ . '/../config/db.php';

class Log {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($user_id, $action, $details = null) {
        $stmt = $this->pdo->prepare("INSERT INTO logs (user_id, action, details) VALUES (:user_id, :action, :details)");
        return $stmt->execute([
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details
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
