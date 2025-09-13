<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/user.php';

class UserSeeder {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function run() {
        $dummyUsers = [
            // Super Admin
            [
                'name' => 'John Doe',
                'email' => 'john.doe@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 1
            ],
            // MIS Admin
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 2
            ],
            // Professors
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 3
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob.wilson@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 3
            ],
            [
                'name' => 'Carol Davis',
                'email' => 'carol.davis@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 3
            ],
            // Students
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie.brown@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 4
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana.prince@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 4
            ],
            [
                'name' => 'Edward Norton',
                'email' => 'edward.norton@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 4
            ],
            [
                'name' => 'Fiona Green',
                'email' => 'fiona.green@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 4
            ],
            [
                'name' => 'George Miller',
                'email' => 'george.miller@plmun.edu.ph',
                'password' => 'password123',
                'role_id' => 4
            ]
        ];

        foreach ($dummyUsers as $userData) {
            // Check if user already exists
            if (!$this->userModel->findByEmail($userData['email'])) {
                if ($this->userModel->create($userData['name'], $userData['email'], $userData['password'], $userData['role_id'])) {
                    echo "User {$userData['name']} created successfully.\n";
                } else {
                    echo "Failed to create user {$userData['name']}.\n";
                }
            } else {
                echo "User {$userData['email']} already exists.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new UserSeeder($pdo);
$seeder->run();
?>
