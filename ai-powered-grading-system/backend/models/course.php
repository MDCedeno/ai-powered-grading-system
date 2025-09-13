<?php
require_once __DIR__ . '/../config/db.php';

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($course_code, $course_name, $professor_id, $semester, $year) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (course_code, course_name, professor_id, semester, year) VALUES (:course_code, :course_name, :professor_id, :semester, :year)");
        return $stmt->execute([
            'course_code' => $course_code,
            'course_name' => $course_name,
            'professor_id' => $professor_id,
            'semester' => $semester,
            'year' => $year
        ]);
    }

    public function findByCode($course_code) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE course_code = :course_code LIMIT 1");
        $stmt->execute(['course_code' => $course_code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $course_code, $course_name, $professor_id, $semester, $year) {
        $stmt = $this->pdo->prepare("UPDATE courses SET course_code = :course_code, course_name = :course_name, professor_id = :professor_id, semester = :semester, year = :year WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'course_code' => $course_code,
            'course_name' => $course_name,
            'professor_id' => $professor_id,
            'semester' => $semester,
            'year' => $year
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM courses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProfessor($professor_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE professor_id = :professor_id");
        $stmt->execute(['professor_id' => $professor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
