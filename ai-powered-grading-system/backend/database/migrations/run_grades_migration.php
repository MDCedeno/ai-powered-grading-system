<?php
require_once __DIR__ . '/../../config/db.php';
require_once '2024_06_01_000003_create_grades_table.php';

$migration = new CreateGradesTable($pdo);
$migration->down();
$migration->up();

echo "Grades table migration completed.\n";
?>
