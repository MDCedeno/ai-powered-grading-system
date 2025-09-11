<?php
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/grade.php';
require_once __DIR__ . '/../models/student.php';

class ProfessorController {
    private $courseModel;
    private $gradeModel;
    private $studentModel;

    public function __construct($pdo) {
        $this->courseModel = new Course($pdo);
        $this->gradeModel = new Grade($pdo);
        $this->studentModel = new Student($pdo);
    }

    public function getCourses($faculty_id) {
        return $this->courseModel->getByFaculty($faculty_id);
    }

    public function getStudentsByCourse($course_id) {
        // Get students enrolled in the course (assuming a enrollment table or from grades)
        // For now, return all students
        return $this->studentModel->getAll();
    }

    public function enterGrade($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa) {
        $existing = $this->gradeModel->findByStudentAndCourse($student_id, $course_id);
        if ($existing) {
            return $this->gradeModel->update($existing['id'], $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa);
        } else {
            return $this->gradeModel->create($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa);
        }
    }

    public function getGrades($course_id) {
        return $this->gradeModel->getByCourse($course_id);
    }
}
?>
