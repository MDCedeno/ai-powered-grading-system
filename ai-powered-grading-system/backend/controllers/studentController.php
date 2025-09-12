<?php
require_once __DIR__ . '/../models/grade.php';
require_once __DIR__ . '/../models/student.php';
require_once __DIR__ . '/../models/course.php';

class StudentController {
    private $gradeModel;
    private $studentModel;
    private $courseModel;

    public function __construct($pdo) {
        $this->gradeModel = new Grade($pdo);
        $this->studentModel = new Student($pdo);
        $this->courseModel = new Course($pdo);
    }

    public function getGrades($student_id) {
        return $this->gradeModel->getByStudent($student_id);
    }

    public function getStudentInfo($user_id) {
        return $this->studentModel->findByUserId($user_id);
    }

    public function getMyGrades() {
        // Get user_id from session
        if (!isset($_SESSION['user_id'])) {
            return ['error' => 'User not logged in'];
        }
        $user_id = $_SESSION['user_id'];
        $student = $this->studentModel->findByUserId($user_id);
        if (!$student) {
            return ['error' => 'Student record not found'];
        }
        return $this->gradeModel->getByStudent($student['id']);
    }

    public function getMyCourses() {
        // Get user_id from session
        if (!isset($_SESSION['user_id'])) {
            return ['error' => 'User not logged in'];
        }
        $user_id = $_SESSION['user_id'];
        $student = $this->studentModel->findByUserId($user_id);
        if (!$student) {
            return ['error' => 'Student record not found'];
        }
        // For now, return all courses, but ideally filter by enrolled courses
        return $this->courseModel->getAll();
    }

    // Add methods for performance insights, etc.
}
?>
