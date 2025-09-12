<?php
require_once __DIR__ . '/../../config/db.php';
require_once '2024_06_01_000003_create_grades_table.php';
require_once '2024_06_01_000002_create_courses_table.php';

$gradesMigration = new CreateGradesTable($pdo);
$coursesMigration = new CreateCoursesTable($pdo);

$gradesMigration->down();
$coursesMigration->down();

$coursesMigration->up();
$gradesMigration->up();

echo "Courses and Grades tables migration completed.\n";
?>
