<?php
require_once __DIR__ . '/config/db.php';

// List of migration files in order
$migrations = [
    '2024_06_01_000000_create_users_table.php',
    '2024_06_01_000001_create_students_table.php',
    '2024_06_01_000002_create_courses_table.php',
    '2024_06_01_000003_create_grades_table.php',
    '2024_06_01_000004_create_logs_table.php'
    // '2024_06_01_000005_add_active_column_to_users_table.php' // Already included in users table
];

foreach ($migrations as $migration) {
    $file = __DIR__ . '/database/migrations/' . $migration;
    if (file_exists($file)) {
        echo "Running migration: $migration\n";
        include $file;
    } else {
        echo "Migration file not found: $migration\n";
    }
}

echo "Migrations completed.\n";
?>
