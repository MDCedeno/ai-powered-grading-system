<?php
require_once __DIR__ . '/../../config/db.php';
require_once '2024_06_01_000000_create_users_table.php';

$migration = new CreateUsersTable($pdo);
$migration->down();
$migration->up();

echo "Users table migration completed.\n";
?>