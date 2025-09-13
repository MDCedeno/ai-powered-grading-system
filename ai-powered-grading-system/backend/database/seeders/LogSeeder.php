<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/log.php';

class LogSeeder {
    private $pdo;
    private $logModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logModel = new Log($pdo);
    }

    public function run() {
        $dummyLogs = [
            [
                'user_id' => 1,
                'action' => 'login',
                'details' => 'Super admin logged in'
            ],
            [
                'user_id' => 2,
                'action' => 'create_course',
                'details' => 'MIS Admin created a new course'
            ],
            [
                'user_id' => 3,
                'action' => 'grade_submission',
                'details' => 'Professor graded student submission'
            ],
            [
                'user_id' => 6,
                'action' => 'view_grades',
                'details' => 'Student viewed their grades'
            ],
            [
                'user_id' => 1,
                'action' => 'system_backup',
                'details' => 'System backup completed successfully'
            ]
        ];

        foreach ($dummyLogs as $logData) {
            if ($this->logModel->create($logData['user_id'], $logData['action'], $logData['details'])) {
                echo "Log entry created successfully.\n";
            } else {
                echo "Failed to create log entry.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new LogSeeder($pdo);
$seeder->run();
?>
