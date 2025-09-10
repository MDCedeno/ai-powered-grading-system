<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/user.php';

$userModel = new User($pdo);

// Dummy users data
$dummyUsers = [
    [
        'name' => 'John Doe',
        'email' => 'john.doe@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 1 // Super Admin
    ],
    [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 2 // MIS Admin
    ],
    [
        'name' => 'Alice Johnson',
        'email' => 'alice.johnson@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 3 // Professor
    ],
    [
        'name' => 'Bob Wilson',
        'email' => 'bob.wilson@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 3 // Professor
    ],
    [
        'name' => 'Charlie Brown',
        'email' => 'charlie.brown@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 4 // Student
    ],
    [
        'name' => 'Diana Prince',
        'email' => 'diana.prince@plmun.edu.ph',
        'password' => 'password123',
        'role_id' => 4 // Student
    ]
];

// Insert dummy users
foreach ($dummyUsers as $userData) {
    if ($userModel->create($userData['name'], $userData['email'], $userData['password'], $userData['role_id'])) {
        echo "User {$userData['name']} created successfully.\n";
    } else {
        echo "Failed to create user {$userData['name']}.\n";
    }
}

echo "Dummy users seeding completed.\n";
?>
