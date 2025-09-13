<?php
require_once __DIR__ . '/../../config/db.php';

// Include creation classes
require_once __DIR__ . '/../creations/2024_06_01_000000_create_users_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000001_create_students_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000002_create_courses_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000003_create_grades_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000004_create_logs_table.php';

try {
    echo "Creating tables...\n";

    // Instantiate migration objects
    $usersMigration    = new CreateUsersTable($pdo);
    $studentsMigration = new CreateStudentsTable($pdo);
    $coursesMigration  = new CreateCoursesTable($pdo);
    $gradesMigration   = new CreateGradesTable($pdo);
    $logsMigration     = new CreateLogsTable($pdo);

    // Create tables in correct order
    $usersMigration->up();
    $studentsMigration->up();
    $coursesMigration->up();
    $gradesMigration->up();
    $logsMigration->up();

    echo "All tables created successfully.\n";

} catch (Exception $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
