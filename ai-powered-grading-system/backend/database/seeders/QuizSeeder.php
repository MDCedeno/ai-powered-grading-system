<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/quiz.php';
require_once __DIR__ . '/../../models/course.php';

class QuizSeeder {
    private $pdo;
    private $quizModel;
    private $courseModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->quizModel = new Quiz($pdo);
        $this->courseModel = new Course($pdo);
    }

    public function run() {
        $courses = $this->courseModel->getAll();

        $quizTitles = [
            'Midterm Review Quiz',
            'Final Exam Practice',
            'Chapter 1 Assessment',
            'Weekly Quiz',
            'Comprehensive Test'
        ];

        $descriptions = [
            'Review quiz covering key concepts from the first half of the course.',
            'Practice questions for the final examination.',
            'Assessment on the introductory chapter.',
            'Weekly knowledge check.',
            'Comprehensive test on all topics covered.'
        ];

        foreach ($courses as $course) {
            // Create 1-2 quizzes per course
            $numQuizzes = rand(1, 2);
            for ($i = 0; $i < $numQuizzes; $i++) {
                $title = $quizTitles[array_rand($quizTitles)] . ' - ' . $course['course_code'];
                $description = $descriptions[array_rand($descriptions)];

                if ($this->quizModel->create($title, $description, $course['id'], $course['professor_id'])) {
                    echo "Quiz '{$title}' for course {$course['course_code']} created successfully.\n";
                } else {
                    echo "Failed to create quiz for course {$course['course_code']}.\n";
                }
            }
        }
    }
}

// Run the seeder
$seeder = new QuizSeeder($pdo);
$seeder->run();
?>
