<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/grade.php';
require_once __DIR__ . '/../../models/student.php';
require_once __DIR__ . '/../../models/course.php';

class GradeSeeder {
    private $pdo;
    private $gradeModel;
    private $studentModel;
    private $courseModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->gradeModel = new Grade($pdo);
        $this->studentModel = new Student($pdo);
        $this->courseModel = new Course($pdo);
    }

    public function run() {
        // Get all students and courses
        $students = $this->studentModel->getAll();
        $courses = $this->courseModel->getAll();

        if (empty($students) || empty($courses)) {
            echo "No students or courses found. Please run StudentSeeder and CourseSeeder first.\n";
            return;
        }

        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Check if grade already exists
                if (!$this->gradeModel->findByStudentAndCourse($student['id'], $course['id'])) {
                    // Generate random grades
                    $midterm_quizzes = rand(5, 10);
                    $midterm_exam = rand(10, 20);
                    $midterm_grade = $midterm_quizzes + $midterm_exam;
                    $final_quizzes = rand(5, 10);
                    $final_exam = rand(10, 20);
                    $final_grade = $final_quizzes + $final_exam;
                    $gpa = round(($midterm_grade + $final_grade) / 100 * 4.0, 2);

                    if ($this->gradeModel->create($student['id'], $course['id'], $midterm_quizzes, $midterm_exam, $midterm_grade, $final_quizzes, $final_exam, $final_grade, $gpa)) {
                        echo "Grade for student {$student['id']} in course {$course['code']} created successfully.\n";
                    } else {
                        echo "Failed to create grade for student {$student['id']} in course {$course['code']}.\n";
                    }
                } else {
                    echo "Grade for student {$student['id']} in course {$course['code']} already exists.\n";
                }
            }
        }
    }
}

// Run the seeder
$seeder = new GradeSeeder($pdo);
$seeder->run();
?>
