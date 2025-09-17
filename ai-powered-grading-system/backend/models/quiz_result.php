<?php
require_once __DIR__ . '/../config/db.php';

class QuizResult
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($quiz_id, $student_id, $score, $answers, $submitted_at = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO quiz_results (quiz_id, student_id, score, answers, submitted_at) VALUES (:quiz_id, :student_id, :score, :answers, :submitted_at)");
        return $stmt->execute([
            'quiz_id' => $quiz_id,
            'student_id' => $student_id,
            'score' => $score,
            'answers' => $answers,
            'submitted_at' => $submitted_at ?? date('Y-m-d H:i:s')
        ]);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_results WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByQuiz($quiz_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_results WHERE quiz_id = :quiz_id");
        $stmt->execute(['quiz_id' => $quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByStudent($student_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_results WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $score, $answers, $submitted_at = null)
    {
        $stmt = $this->pdo->prepare("UPDATE quiz_results SET score = :score, answers = :answers, submitted_at = :submitted_at WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'score' => $score,
            'answers' => $answers,
            'submitted_at' => $submitted_at ?? date('Y-m-d H:i:s')
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM quiz_results WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
