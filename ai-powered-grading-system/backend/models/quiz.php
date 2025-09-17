<?php
require_once __DIR__ . '/../config/db.php';

class Quiz
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($title, $description, $course_id, $professor_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO quizzes (title, description, course_id, professor_id) VALUES (:title, :description, :course_id, :professor_id)");
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'course_id' => $course_id,
            'professor_id' => $professor_id
        ]);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCourse($course_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProfessor($professor_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE professor_id = :professor_id");
        $stmt->execute(['professor_id' => $professor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $description, $course_id, $professor_id)
    {
        $stmt = $this->pdo->prepare("UPDATE quizzes SET title = :title, description = :description, course_id = :course_id, professor_id = :professor_id WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'course_id' => $course_id,
            'professor_id' => $professor_id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM quizzes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
