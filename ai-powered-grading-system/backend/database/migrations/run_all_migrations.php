<?php
// run_all_migrations.php
require_once __DIR__ . '/../../config/db.php';

// Include migration classes
require_once __DIR__ . '/../creations/2024_06_01_000000_create_users_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000001_create_students_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000002_create_courses_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000003_create_grades_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000004_create_logs_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000005_create_quizzes_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000006_create_quiz_results_table.php';
require_once __DIR__ . '/../creations/2024_06_01_000007_create_role_assignments_table.php';

$usersMigration    = new CreateUsersTable($pdo);
$studentsMigration = new CreateStudentsTable($pdo);
$coursesMigration  = new CreateCoursesTable($pdo);
$gradesMigration   = new CreateGradesTable($pdo);
$logsMigration     = new CreateLogsTable($pdo);
$quizzesMigration  = new CreateQuizzesTable($pdo);
$quizResultsMigration = new CreateQuizResultsTable($pdo);
$roleAssignmentsMigration = new CreateRoleAssignmentsTable($pdo);

try {
    echo "Starting migrations...\n";

    // Drop tables in reverse order to avoid foreign key issues
    $roleAssignmentsMigration->down();
    $quizResultsMigration->down();
    $quizzesMigration->down();
    $logsMigration->down();
    $gradesMigration->down();
    $coursesMigration->down();
    $studentsMigration->down();
    $usersMigration->down();

    echo "All tables dropped successfully.\n";

    // Create tables in correct order
    $usersMigration->up();
    $studentsMigration->up();
    $coursesMigration->up();
    $gradesMigration->up();
    $logsMigration->up();
    $quizzesMigration->up();
    $quizResultsMigration->up();

    echo "All tables created successfully.\n";
    echo "Migrations completed.\n";

} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
?>
