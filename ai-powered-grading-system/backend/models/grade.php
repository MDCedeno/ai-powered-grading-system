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
}
?>
