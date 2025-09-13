<?php
require_once __DIR__ . '/../../config/db.php';

// Include seeder classes
require_once __DIR__ . '/../seeders/UserSeeder.php';
require_once __DIR__ . '/../seeders/StudentSeeder.php';
require_once __DIR__ . '/../seeders/CourseSeeder.php';
require_once __DIR__ . '/../seeders/GradeSeeder.php';
require_once __DIR__ . '/../seeders/LogSeeder.php';

// Instantiate seeder objects
$userSeeder = new UserSeeder($pdo);
$studentSeeder = new StudentSeeder($pdo);
$courseSeeder = new CourseSeeder($pdo);
$gradeSeeder = new GradeSeeder($pdo);
$logSeeder = new LogSeeder($pdo);

try {
    echo "Starting data seeding...\n";

    // Run seeders in correct order (users first, then others that depend on users)
    $userSeeder->run();
    $studentSeeder->run();
    $courseSeeder->run();
    $gradeSeeder->run();
    $logSeeder->run();

    echo "All data seeded successfully.\n";

} catch (Exception $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
}
?>
