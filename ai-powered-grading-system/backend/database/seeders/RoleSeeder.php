<?php
require_once __DIR__ . '/../../config/db.php';

class RoleSeeder {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run() {
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'description' => 'Developer with full access'],
            ['id' => 2, 'name' => 'MIS Admin', 'description' => 'Administrator for MIS operations'],
            ['id' => 3, 'name' => 'Professor', 'description' => 'Faculty member'],
            ['id' => 4, 'name' => 'Student', 'description' => 'Enrolled student']
        ];

        foreach ($roles as $role) {
            // Check if role already exists
            $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $role['id']]);
            $exists = $stmt->fetch();

            if (!$exists) {
                $sql = "INSERT INTO roles (id, name, description) VALUES (:id, :name, :description)";
                $insertStmt = $this->pdo->prepare($sql);
                if ($insertStmt->execute($role)) {
                    echo "Role {$role['name']} created successfully.\n";
                } else {
                    echo "Failed to create role {$role['name']}.\n";
                }
            } else {
                echo "Role {$role['name']} already exists.\n";
            }
        }
    }
}

// Run the seeder
$seeder = new RoleSeeder($pdo);
$seeder->run();
?>
