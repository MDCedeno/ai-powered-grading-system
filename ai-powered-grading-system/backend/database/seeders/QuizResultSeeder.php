<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/quiz_result.php';
require_once __DIR__ . '/../../models/quiz.php';
require_once __DIR__ . '/../../models/student.php';

class QuizResultSeeder {
    private $pdo;
    private $quizResultModel;
    private $quizModel;
    private $studentModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->quizResultModel = new QuizResult($pdo);
        $this->quizModel = new Quiz($pdo);
        $this->studentModel = new Student($pdo);
    }

    public function run() {
        $quizzes = $this->quizModel->getAll();
        $students = $this->studentModel->getAll();

        foreach ($quizzes as $quiz) {
            // Randomly select some students to have taken this quiz
            $numResults = rand(1, count($students));
            $selectedStudents = array_rand($students, $numResults);
            if (!is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $index) {
                $student = $students[$index];
                $score = rand(0, 100) / 10.0; // Score out of 10
                $answers = json_encode([
                    'q1' => 'Answer ' . rand(1, 4),
                    'q2' => 'Answer ' . rand(1, 4),
                    'q3' => 'Answer ' . rand(1, 4)
                ]); // Dummy JSON answers

                if ($this->quizResultModel->create($quiz['id'], $student['id'], $score, $answers)) {
                    echo "Quiz result for student {$student['id']} on quiz {$quiz['id']} created successfully.\n";
                } else {
                    echo "Failed to create quiz result for student {$student['id']} on quiz {$quiz['id']}.\n";
                }
            }
        }
    }
}

// Run the seeder
$seeder = new QuizResultSeeder($pdo);
$seeder->run();
?>
