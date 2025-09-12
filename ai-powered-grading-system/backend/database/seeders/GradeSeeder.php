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
        $students = $this->studentModel->getAll();
        $courses = $this->courseModel->getAll();

        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Check if grade record already exists
if (!$this->gradeModel->findByStudentAndCourse($student['id'], $course['id'])) {
                    $midterm_quizzes = rand(5, 10);
                    $midterm_exam = rand(10, 20);
                    $midterm_grade = $midterm_quizzes + $midterm_exam;
                    $final_quizzes = rand(5, 10);
                    $final_exam = rand(10, 20);
                    $final_grade = $final_quizzes + $final_exam;
                    $gpa = rand(20, 40) / 10.0;

                    if ($this->gradeModel->create(
                        $student['id'],
                        $course['id'],
                        $midterm_quizzes,
                        $midterm_exam,
                        $midterm_grade,
                        $final_quizzes,
                        $final_exam,
                        $final_grade,
                        $gpa
                    )) {
                        echo "Grade record for student {$student['id']} in course {$course['id']} created successfully.\n";
                    } else {
                        echo "Failed to create grade record for student {$student['id']} in course {$course['id']}.\n";
                    }
                } else {
                    echo "Grade record for student {$student['id']} in course {$course['id']} already exists.\n";
                }
            }
        }
    }
}

// Run the seeder
$seeder = new GradeSeeder($pdo);
$seeder->run();
?>
