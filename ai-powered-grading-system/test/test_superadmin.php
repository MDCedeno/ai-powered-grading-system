<?php
require_once '../backend/config/db.php';
require_once '../backend/controllers/superAdminController.php';

$controller = new SuperAdminController($pdo);

// Test getAllUsers
echo "Testing getAllUsers()...\n";
$users = $controller->getAllUsers();
if (is_array($users)) {
    echo "getAllUsers returned " . count($users) . " users.\n";
} else {
    echo "getAllUsers failed.\n";
}

// Test getSystemLogs
echo "Testing getSystemLogs()...\n";
$logs = $controller->getSystemLogs();
if (is_array($logs)) {
    echo "getSystemLogs returned " . count($logs) . " logs.\n";
} else {
    echo "getSystemLogs failed.\n";
}

// Test deactivateUser
if (count($users) > 0) {
    $userId = $users[0]['id'];
    echo "Testing deactivateUser() on user ID $userId...\n";
    $result = $controller->deactivateUser($userId);
    if ($result) {
        echo "deactivateUser succeeded.\n";
    } else {
        echo "deactivateUser failed.\n";
    }
} else {
    echo "No users to test deactivateUser.\n";
}
?>
