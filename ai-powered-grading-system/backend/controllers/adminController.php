<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/student.php';
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/grade.php';

class AdminController {
    private $userModel;
    private $studentModel;
    private $courseModel;
    private $gradeModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->studentModel = new Student($pdo);
        $this->courseModel = new Course($pdo);
        $this->gradeModel = new Grade($pdo);
    }

    public function getStudents() {
        return $this->userModel->getStudents();
    }

    public function getProfessors() {
        return $this->userModel->getProfessors();
    }

    public function createStudent($name, $email, $password, $program, $year) {
        $userId = $this->userModel->create($name, $email, $password, 4);
        if ($userId) {
            return $this->studentModel->create($userId, $program, $year);
        }
        return false;
    }

    public function createCourse($code, $name, $schedule, $faculty_id) {
        return $this->courseModel->create($code, $name, $schedule, $faculty_id);
    }

    public function getCourses() {
        return $this->courseModel->getAll();
    }

    public function getGradesByCourse($course_id) {
        return $this->gradeModel->getByCourse($course_id);
    }

    // Add more methods as needed for reports, etc.
}
?>
