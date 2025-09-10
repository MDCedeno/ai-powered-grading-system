<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $password, $role_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role_id) VALUES (:name, :email, :password, :role_id)");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role_id' => $role_id
        ]);
    }

    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
