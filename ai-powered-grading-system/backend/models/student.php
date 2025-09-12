<?php
require_once __DIR__ . '/../config/db.php';

class Student {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($user_id, $program, $year) {
        $stmt = $this->pdo->prepare("INSERT INTO students (user_id, program, year) VALUES (:user_id, :program, :year)");
        return $stmt->execute([
            'user_id' => $user_id,
            'program' => $program,
            'year' => $year
        ]);
    }

    public function findByUserId($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $program, $year) {
        $stmt = $this->pdo->prepare("UPDATE students SET program = :program, year = :year WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'program' => $program,
            'year' => $year
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM students");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
