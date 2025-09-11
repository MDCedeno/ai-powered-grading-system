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
        return $this->studentModel->getAll();
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

    public function getAllStudents() {
        return $this->studentModel->getAll();
    }

    public function getAllCourses() {
        return $this->courseModel->getAll();
    }

    public function getAllGrades() {
        return $this->gradeModel->getAll();
    }

    public function addStudent($data) {
        return $this->studentModel->create($data['user_id'], $data['program'], $data['year']);
    }

    public function addCourse($data) {
        return $this->courseModel->create($data['code'], $data['name'], $data['schedule'], $data['faculty_id']);
    }

    public function addGrade($data) {
        return $this->gradeModel->create($data['student_id'], $data['course_id'], $data['midterm_quizzes'], $data['midterm_exam'], $data['midterm_grade'], $data['final_quizzes'], $data['final_exam'], $data['final_grade'], $data['gpa']);
    }

    public function updateStudent($id, $data) {
        return $this->studentModel->update($id, $data['program'], $data['year']);
    }

    public function updateCourse($id, $data) {
        return $this->courseModel->update($id, $data['code'], $data['name'], $data['schedule'], $data['faculty_id']);
    }

    public function updateGrade($id, $data) {
        return $this->gradeModel->update($id, $data['midterm_quizzes'], $data['midterm_exam'], $data['midterm_grade'], $data['final_quizzes'], $data['final_exam'], $data['final_grade'], $data['gpa']);
    }

    public function deleteStudent($id) {
        return $this->studentModel->delete($id);
    }

    public function deleteCourse($id) {
        return $this->courseModel->delete($id);
    }

    public function deleteGrade($id) {
        return $this->gradeModel->delete($id);
    }

    // Add more methods as needed for reports, etc.
}
?>
