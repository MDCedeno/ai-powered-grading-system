<?php
require_once __DIR__ . '/../backend/config/db.php';

try {
    // Check if users table exists and has data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users count: " . $result['count'] . "\n";

    if ($result['count'] > 0) {
        // Get some users
        $stmt = $pdo->prepare("SELECT id, name, email, role_id, active, created_at FROM users LIMIT 5");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Sample users:\n";
        foreach ($users as $user) {
            echo "- ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role_id']}, Active: {$user['active']}\n";
        }
    } else {
        echo "No users found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
