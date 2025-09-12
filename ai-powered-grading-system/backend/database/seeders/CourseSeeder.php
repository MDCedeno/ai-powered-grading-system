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
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'professor_id' => 3, // Assuming Alice Johnson is professor
                'semester' => 'Fall 2024',
                'year' => 2024
            ],
            [
                'course_code' => 'IT201',
                'course_name' => 'Database Management Systems',
                'professor_id' => 4, // Bob Wilson
                'semester' => 'Fall 2024',
                'year' => 2024
            ],
            [
                'course_code' => 'CS301',
                'course_name' => 'Data Structures and Algorithms',
                'professor_id' => 5, // Carol Davis
                'semester' => 'Spring 2024',
                'year' => 2024
            ],
            [
                'course_code' => 'IT401',
                'course_name' => 'Web Development',
                'professor_id' => 3,
                'semester' => 'Spring 2024',
                'year' => 2024
            ],
            [
                'course_code' => 'CS501',
                'course_name' => 'Artificial Intelligence',
                'professor_id' => 4,
                'semester' => 'Fall 2024',
                'year' => 2024
            ]
        ];

        foreach ($dummyCourses as $courseData) {
            // Check if course already exists
            if (!$this->courseModel->findByCode($courseData['course_code'])) {
                if ($this->courseModel->create($courseData['course_code'], $courseData['course_name'], $courseData['professor_id'], $courseData['semester'], $courseData['year'])) {
                    echo "Course {$courseData['course_name']} created successfully.\n";
                } else {
                    echo "Failed to create course {$courseData['course_name']}.\n";
                }
            } else {
                echo "Course {$courseData['course_code']} already exists.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new CourseSeeder($pdo);
$seeder->run();
?>
