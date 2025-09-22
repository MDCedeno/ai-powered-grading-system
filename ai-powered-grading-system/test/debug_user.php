<?php
require_once 'backend/config/db.php';

echo "Checking database for user ID 999999...\n";

try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM users WHERE id = 999999');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'User 999999 exists: ' . ($result['count'] > 0 ? 'YES' : 'NO') . PHP_EOL;

    echo "\nLast 5 users in database:\n";
    $stmt = $pdo->prepare('SELECT id, name, email FROM users ORDER BY id DESC LIMIT 5');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $user) {
        echo 'ID: ' . $user['id'] . ', Name: ' . $user['name'] . ', Email: ' . $user['email'] . PHP_EOL;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
