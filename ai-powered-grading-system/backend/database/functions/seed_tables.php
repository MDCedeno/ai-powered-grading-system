<?php
require_once __DIR__ . '/../../config/db.php';

// Include seeder classes
require_once __DIR__ . '/../seeders/UserSeeder.php';
require_once __DIR__ . '/../seeders/StudentSeeder.php';
require_once __DIR__ . '/../seeders/CourseSeeder.php';
require_once __DIR__ . '/../seeders/GradeSeeder.php';
require_once __DIR__ . '/../seeders/LogSeeder.php';

try {
    echo "Seeding tables...\n";

    // Instantiate seeder objects
    $userSeeder    = new UserSeeder($pdo);
    $studentSeeder = new StudentSeeder($pdo);
    $courseSeeder  = new CourseSeeder($pdo);
    $gradeSeeder   = new GradeSeeder($pdo);
    $logSeeder     = new LogSeeder($pdo);

    // Run seeders in correct order
    $userSeeder->run();
    $studentSeeder->run();
    $courseSeeder->run();
    $gradeSeeder->run();
    $logSeeder->run();

    echo "All tables seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding tables: " . $e->getMessage() . "\n";
}
