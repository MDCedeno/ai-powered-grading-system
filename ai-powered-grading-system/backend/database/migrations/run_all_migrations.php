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
require_once __DIR__ . '/../creations/2024_06_01_000007_create_roles_table.php';
$usersMigration    = new CreateUsersTable($pdo);
$studentsMigration = new CreateStudentsTable($pdo);
$coursesMigration  = new CreateCoursesTable($pdo);
$gradesMigration   = new CreateGradesTable($pdo);
$logsMigration     = new CreateLogsTable($pdo);
$quizzesMigration  = new CreateQuizzesTable($pdo);
$quizResultsMigration = new CreateQuizResultsTable($pdo);
$rolesMigration    = new CreateRolesTable($pdo);

try {
    echo "Starting migrations...\n";

    // Disable foreign key checks to avoid constraint issues during drop
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Drop tables in reverse order to avoid foreign key issues
    $quizResultsMigration->down();
    $quizzesMigration->down();
    $logsMigration->down();
    $gradesMigration->down();
    $coursesMigration->down();
    $studentsMigration->down();
    $usersMigration->down();

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "All tables dropped successfully.\n";

// Create tables in correct order
$rolesMigration->up();
$usersMigration->up();
$studentsMigration->up();
$coursesMigration->up();
$gradesMigration->up();
$logsMigration->up();
$quizzesMigration->up();
$quizResultsMigration->up();
require_once __DIR__ . '/../creations/2024_09_18_000000_create_backup_records_table.php';
$backupRecordsMigration = new CreateBackupRecordsTable($pdo);
$backupRecordsMigration->up();

echo "All tables created successfully.\n";
    echo "Migrations completed.\n";

} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
?>
