<?php
require_once __DIR__ . '/../models/course.php';
require_once __DIR__ . '/../models/grade.php';
require_once __DIR__ . '/../models/student.php';

class ProfessorController
{
    private $courseModel;
    private $gradeModel;
    private $studentModel;

    public function __construct($pdo)
    {
        $this->courseModel = new Course($pdo);
        $this->gradeModel = new Grade($pdo);
        $this->studentModel = new Student($pdo);
    }

    public function getCourses($faculty_id)
    {
        return $this->courseModel->getByProfessor($faculty_id);
    }

    public function getStudentsByCourse($course_id)
    {
        // Get students enrolled in the course (assuming a enrollment table or from grades)
        // For now, return all students
        return $this->studentModel->getAll();
    }

    public function enterGrade($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa)
    {
        $existing = $this->gradeModel->findByStudentAndCourse($student_id, $course_id);
        if ($existing) {
            return $this->gradeModel->update($existing['id'], $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa);
        } else {
            return $this->gradeModel->create($student_id, $course_id, $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa);
        }
    }

    public function getGrades($course_id)
    {
        return $this->gradeModel->getByCourse($course_id);
    }

    public function getMyStudents()
    {
        if (!isset($_SESSION['user_id'])) {
            return ['error' => 'User not logged in'];
        }
        $faculty_id = $_SESSION['user_id'];
        $courses = $this->courseModel->getByFaculty($faculty_id);
        $course_ids = array_column($courses, 'id');
        if (empty($course_ids)) {
            return [];
        }
        // Get unique students from grades
        $students = [];
        foreach ($course_ids as $course_id) {
            $grades = $this->gradeModel->getByCourse($course_id);
            foreach ($grades as $grade) {
                $student = $this->studentModel->findById($grade['student_id']);
                if ($student && !in_array($student, $students)) {
                    $students[] = $student;
                }
            }
        }
        return $students;
    }

    public function getMyCourses()
    {
        if (!isset($_SESSION['user_id'])) {
            return ['error' => 'User not logged in'];
        }
        $faculty_id = $_SESSION['user_id'];
        return $this->courseModel->getByProfessor($faculty_id);
    }

    public function getMyGrades()
    {
        if (!isset($_SESSION['user_id'])) {
            return ['error' => 'User not logged in'];
        }
        $faculty_id = $_SESSION['user_id'];
        $courses = $this->courseModel->getByFaculty($faculty_id);
        $course_ids = array_column($courses, 'id');
        if (empty($course_ids)) {
            return [];
        }
        $all_grades = [];
        foreach ($course_ids as $course_id) {
            $grades = $this->gradeModel->getByCourse($course_id);
            $all_grades = array_merge($all_grades, $grades);
        }
        return $all_grades;
    }

    public function addGrade($data)
    {
        try {
            return $this->gradeModel->createWithAI($data['student_id'], $data['course_id'], $data['midterm_quizzes'], $data['midterm_exam'], $data['final_quizzes'], $data['final_exam']);
        } catch (Exception $e) {
            error_log('Error in addGrade: ' . $e->getMessage());
            return false;
        }
    }

    public function updateGrade($id, $data)
    {
        return $this->gradeModel->update($id, $data['midterm_quizzes'], $data['midterm_exam'], $data['midterm_grade'], $data['final_quizzes'], $data['final_exam'], $data['final_grade'], $data['gpa']);
    }

    public function deleteGrade($id)
    {
        return $this->gradeModel->delete($id);
    }
}
