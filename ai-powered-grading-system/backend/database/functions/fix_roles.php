<?php
require_once __DIR__ . '/../../config/db.php';

class FixRoles {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run() {
        echo "Starting role fix...\n";

        // Define role mappings: email => role_id
        $roleMappings = [
            'john.doe@plmun.edu.ph' => 1, // Super Admin
            'jane.smith@plmun.edu.ph' => 2, // MIS Admin
            'alice.johnson@plmun.edu.ph' => 3, // Professor
            'bob.wilson@plmun.edu.ph' => 3, // Professor
            'carol.davis@plmun.edu.ph' => 3, // Professor
            'charlie.brown@plmun.edu.ph' => 4, // Student
            'diana.prince@plmun.edu.ph' => 4, // Student
            'edward.norton@plmun.edu.ph' => 4, // Student
            'fiona.green@plmun.edu.ph' => 4, // Student
            'george.miller@plmun.edu.ph' => 4, // Student
        ];

        foreach ($roleMappings as $email => $role_id) {
            $stmt = $this->pdo->prepare("UPDATE users SET role_id = ? WHERE email = ?");
            if ($stmt->execute([$role_id, $email])) {
                echo "Updated role for {$email} to {$role_id}.\n";
            } else {
                echo "Failed to update role for {$email}.\n";
            }
        }

        echo "Role fix completed.\n";
    }
}

// Run the fix
$fix = new FixRoles($pdo);
$fix->run();
?>
