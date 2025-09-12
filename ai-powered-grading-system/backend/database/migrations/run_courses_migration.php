<?php
require_once __DIR__ . '/../../config/db.php';
require_once '2024_06_01_000002_create_courses_table.php';

$migration = new CreateCoursesTable($pdo);
$migration->down();
$migration->up();

echo "Courses table migration completed.\n";
?>
