<?php
require_once __DIR__ . '/../config/db.php';

class Grade {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa) {
        $stmt = $this->pdo->prepare("INSERT INTO grades (student_id, course_id, midterm_quizzes, midterm_exam, midterm_grade, final_quizzes, final_exam, final_grade, gpa) VALUES (:student_id, :course_id, :midterm_quizzes, :midterm_exam, :midterm_grade, :final_quizzes, :final_exam, :final_grade, :gpa)");
        return $stmt->execute([
            'student_id' => $student_id,
            'course_id' => $course_id,
            'midterm_quizzes' => $midterm_quizzes,
            'midterm_exam' => $midterm_exam,
            'midterm_grade' => $midterm_grade,
            'final_quizzes' => $final_quizzes,
            'final_exam' => $final_exam,
            'final_grade' => $final_grade,
            'gpa' => $gpa
        ]);
    }

    public function createWithAI($student_id, $course_id, $midterm_quizzes, $midterm_exam, $final_quizzes, $final_exam) {
        $ai_result = $this->callAIComputeGrade($midterm_quizzes, $midterm_exam, $final_quizzes, $final_exam);
        if ($ai_result) {
            $midterm_grade = $ai_result['midterm_grade'];
            $final_grade = $ai_result['final_grade'];
            $overall = $ai_result['overall'];
            $gpa = $ai_result['gpa'];
        } else {
            // Fallback to manual calculation
            $midterm_grade = ($midterm_quizzes + $midterm_exam) / 2;
            $final_grade = ($final_quizzes + $final_exam) / 2;
            $overall = ($midterm_grade + $final_grade) / 2;
            $gpa = min(4.0, $overall / 25);
        }
        return $this->create($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa);
    }

    public function findByStudentAndCourse($student_id, $course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM grades WHERE student_id = :student_id AND course_id = :course_id LIMIT 1");
        $stmt->execute([
            'student_id' => $student_id,
            'course_id' => $course_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa) {
        $stmt = $this->pdo->prepare("UPDATE grades SET midterm_quizzes = :midterm_quizzes, midterm_exam = :midterm_exam, midterm_grade = :midterm_grade, final_quizzes = :final_quizzes, final_exam = :final_exam, final_grade = :final_grade, gpa = :gpa WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'midterm_quizzes' => $midterm_quizzes,
            'midterm_exam' => $midterm_exam,
            'midterm_grade' => $midterm_grade,
            'final_quizzes' => $final_quizzes,
            'final_exam' => $final_exam,
            'final_grade' => $final_grade,
            'gpa' => $gpa
        ]);
    }

    public function getByStudent($student_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM grades WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCourse($course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM grades WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM grades");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM grades WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    private function callAIComputeGrade($midterm_quizzes, $midterm_exam, $final_quizzes, $final_exam) {
        $ai_url = 'http://localhost:5000/compute_grade';  // Assuming AI module runs on port 5000
        $data = json_encode([
            'midterm_quizzes' => $midterm_quizzes,
            'midterm_exam' => $midterm_exam,
            'final_quizzes' => $final_quizzes,
            'final_exam' => $final_exam
        ]);

        $ch = curl_init($ai_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result;
        }
        return null;
    }
}
?>
