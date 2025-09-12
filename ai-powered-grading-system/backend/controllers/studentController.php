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

    public function getNotifications() {
        // Mock notifications for now
        return [
            ['id' => 1, 'message' => 'New grade posted for CS101', 'date' => '2024-01-15'],
            ['id' => 2, 'message' => 'Assignment due tomorrow', 'date' => '2024-01-14'],
            ['id' => 3, 'message' => 'Welcome to the new semester!', 'date' => '2024-01-10']
        ];
    }

    public function getQuizzes() {
        // Mock quizzes for now
        return [
            ['id' => 1, 'title' => 'Midterm Quiz CS101', 'subject' => 'Computer Science', 'status' => 'available', 'action' => 'Take Quiz'],
            ['id' => 2, 'title' => 'Final Quiz IT201', 'subject' => 'Information Technology', 'status' => 'completed', 'action' => 'View Results'],
            ['id' => 3, 'title' => 'Practice Quiz MATH101', 'subject' => 'Mathematics', 'status' => 'upcoming', 'action' => 'Preview']
        ];
    }

    // Add methods for performance insights, etc.
}
?>
