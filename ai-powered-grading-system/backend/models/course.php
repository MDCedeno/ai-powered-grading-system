<?php
require_once __DIR__ . '/../config/db.php';

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($code, $name, $schedule, $faculty_id) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (code, name, schedule, faculty_id) VALUES (:code, :name, :schedule, :faculty_id)");
        return $stmt->execute([
            'code' => $code,
            'name' => $name,
            'schedule' => $schedule,
            'faculty_id' => $faculty_id
        ]);
    }

    public function findByCode($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $code, $name, $schedule, $faculty_id) {
        $stmt = $this->pdo->prepare("UPDATE courses SET code = :code, name = :name, schedule = :schedule, faculty_id = :faculty_id WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'code' => $code,
            'name' => $name,
            'schedule' => $schedule,
            'faculty_id' => $faculty_id
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM courses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFaculty($faculty_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE faculty_id = :faculty_id");
        $stmt->execute(['faculty_id' => $faculty_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
