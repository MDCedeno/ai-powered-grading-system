<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../seeders/RoleSeeder.php';

try {
    echo "Fixing roles...\n";

    // Run the RoleSeeder to ensure roles are created
    $roleSeeder = new RoleSeeder($pdo);
    $roleSeeder->run();

    // Optionally, verify or update user role_ids if needed
    // For now, assuming users are already correctly assigned via UserSeeder

    echo "Roles fixed successfully.\n";
} catch (Exception $e) {
    echo "Error fixing roles: " . $e->getMessage() . "\n";
}
?>
