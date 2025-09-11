<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/student.php';
require_once __DIR__ . '/../../models/user.php';

class StudentSeeder {
    private $pdo;
    private $studentModel;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->studentModel = new Student($pdo);
        $this->userModel = new User($pdo);
    }

    public function run() {
        // Get all student users (role_id = 4)
        $students = $this->userModel->getStudents();

        $programs = ['BSIT', 'BSCS', 'BSIS'];
        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        foreach ($students as $student) {
            // Check if student record already exists
            if (!$this->studentModel->findByUserId($student['id'])) {
                $program = $programs[array_rand($programs)];
                $year = $years[array_rand($years)];

                if ($this->studentModel->create($student['id'], $program, $year)) {
                    echo "Student record for {$student['name']} created successfully.\n";
                } else {
                    echo "Failed to create student record for {$student['name']}.\n";
                }
            } else {
                echo "Student record for {$student['name']} already exists.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new StudentSeeder($pdo);
$seeder->run();
?>
