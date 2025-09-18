<?php
require_once __DIR__ . '/../../config/db.php';

// Include seeder classes
require_once __DIR__ . '/../seeders/RoleSeeder.php';
require_once __DIR__ . '/../seeders/UserSeeder.php';

require_once __DIR__ . '/../seeders/StudentSeeder.php';
require_once __DIR__ . '/../seeders/CourseSeeder.php';
require_once __DIR__ . '/../seeders/QuizSeeder.php';
require_once __DIR__ . '/../seeders/QuizResultSeeder.php';
require_once __DIR__ . '/../seeders/GradeSeeder.php';
require_once __DIR__ . '/../seeders/LogSeeder.php';
require_once __DIR__ . '/../seeders/BackupRecordSeeder.php';

try {
    echo "Seeding tables...\n";

    // Instantiate seeder objects
    $roleSeeder          = new RoleSeeder($pdo);
    $userSeeder          = new UserSeeder($pdo);
    $studentSeeder       = new StudentSeeder($pdo);
    $courseSeeder        = new CourseSeeder($pdo);
    $quizSeeder          = new QuizSeeder($pdo);
    $quizResultSeeder    = new QuizResultSeeder($pdo);
    $gradeSeeder         = new GradeSeeder($pdo);
    $logSeeder           = new LogSeeder($pdo);
    $backupRecordSeeder  = new BackupRecordSeeder($pdo);

    // Run seeders in correct order
    $roleSeeder->run();
    $userSeeder->run();
    $studentSeeder->run();
    $courseSeeder->run();
    $quizSeeder->run();
    $quizResultSeeder->run();
    $gradeSeeder->run();
    $logSeeder->run();
    $backupRecordSeeder->run();

    echo "All tables seeded successfully.\n";
} catch (Exception $e) {
    echo "Error seeding tables: " . $e->getMessage() . "\n";
}
