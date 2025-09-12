<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/course.php';

class CourseSeeder {
    private $pdo;
    private $courseModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->courseModel = new Course($pdo);
    }

    public function run() {
        $dummyCourses = [
            [
                'code' => 'PROFDEV8-CS4A',
                'name' => 'Professional Development 8',
                'schedule' => '1:00PM-2:00PM/SUN',
                'faculty_id' => 3
            ],
            [
                'code' => 'PROFDEV8-CS4B',
                'name' => 'Professional Development 8',
                'schedule' => '2:00PM-3:00PM/SUN',
                'faculty_id' => 3
            ],
            [
                'code' => 'PROFDEV8-CS4C',
                'name' => 'Professional Development 8',
                'schedule' => '3:00PM-4:00PM/SUN',
                'faculty_id' => 3
            ],
            [
                'code' => 'PROFDEV8-IT4A',
                'name' => 'Professional Development 8',
                'schedule' => '4:00PM-5:00PM/SUN',
                'faculty_id' => 4
            ],
            [
                'code' => 'PROFDEV8-IT4B',
                'name' => 'Professional Development 8',
                'schedule' => '5:00PM-6:00PM/SUN',
                'faculty_id' => 4
            ]
        ];

        foreach ($dummyCourses as $courseData) {
            // Check if course already exists
            if (!$this->courseModel->findByCode($courseData['code'])) {
                if ($this->courseModel->create($courseData['code'], $courseData['name'], $courseData['schedule'], $courseData['faculty_id'])) {
                    echo "Course {$courseData['code']} created successfully.\n";
                } else {
                    echo "Failed to create course {$courseData['code']}.\n";
                }
            } else {
                echo "Course {$courseData['code']} already exists.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new CourseSeeder($pdo);
$seeder->run();
?>
