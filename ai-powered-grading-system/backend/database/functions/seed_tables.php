<?php
// seed_tables.php
require_once __DIR__ . '/../../config/db.php';

// Include seeder classes
require_once __DIR__ . '/../seeders/UserSeeder.php';
require_once __DIR__ . '/../seeders/StudentSeeder.php';
require_once __DIR__ . '/../seeders/CourseSeeder.php';
require_once __DIR__ . '/../seeders/GradeSeeder.php';
require_once __DIR__ . '/../seeders/LogSeeder.php';

try {
    echo "Seeding tables...\n";

    // Instantiate and run seeders
    $userSeeder = new UserSeeder($pdo);
    $userSeeder->run();

    $studentSeeder = new StudentSeeder($pdo);
    $studentSeeder->run();

    $courseSeeder = new CourseSeeder($pdo);
    $courseSeeder->run();

    $gradeSeeder = new GradeSeeder($pdo);
    $gradeSeeder->run();

    $logSeeder = new LogSeeder($pdo);
    $logSeeder->run();

    echo "All tables seeded successfully.\n";

} catch (Exception $e) {
    echo "Error seeding tables: " . $e->getMessage() . "\n";
}
?>
