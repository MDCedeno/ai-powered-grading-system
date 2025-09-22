<?php
require_once __DIR__ . '/../backend/config/db.php';

global $pdo;

echo "=== Checking for Super Admin User ===\n\n";

try {
    $stmt = $pdo->prepare("SELECT id, name, email, role_id FROM users WHERE role_id = 1 LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "Super Admin user found:\n";
        echo "- ID: {$user['id']}\n";
        echo "- Name: {$user['name']}\n";
        echo "- Email: {$user['email']}\n";
    } else {
        echo "No Super Admin user found\n";

        // Check all users
        echo "\nAll users in system:\n";
        $stmt = $pdo->prepare("SELECT id, name, email, role_id FROM users ORDER BY role_id, name");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $roleName = match($user['role_id']) {
                1 => 'Super Admin',
                2 => 'MIS Admin',
                3 => 'Professor',
                4 => 'Student',
                default => 'Unknown'
            };
            echo "- ID {$user['id']}: {$user['name']} ({$user['email']}) - {$roleName}\n";
        }
    }

} catch (Exception $e) {
    echo "Error checking users: " . $e->getMessage() . "\n";
}
?>
