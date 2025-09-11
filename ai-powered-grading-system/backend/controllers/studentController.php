<?php
require_once __DIR__ . '/../models/grade.php';
require_once __DIR__ . '/../models/student.php';

class StudentController {
    private $gradeModel;
    private $studentModel;

    public function __construct($pdo) {
        $this->gradeModel = new Grade($pdo);
        $this->studentModel = new Student($pdo);
    }

    public function getGrades($student_id) {
        return $this->gradeModel->getByStudent($student_id);
    }

    public function getStudentInfo($user_id) {
        return $this->studentModel->findByUserId($user_id);
    }

    // Add methods for performance insights, etc.
}
?>
