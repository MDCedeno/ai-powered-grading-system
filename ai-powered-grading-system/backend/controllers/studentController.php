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
        // Assume user_id is passed or from session
        // For now, return all grades
        return $this->gradeModel->getAll();
    }

    public function getMyCourses() {
        // Assume user_id is passed or from session
        // For now, return all courses
        return $this->courseModel->getAll();
    }

    // Add methods for performance insights, etc.
}
?>
