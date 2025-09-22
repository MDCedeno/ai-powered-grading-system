<?php
/**
 * Test file for logging implementation in SuperAdminController
 * Tests the enhanced logging functionality for user operations and database backups
 */

require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/controllers/superAdminController.php';
require_once __DIR__ . '/../backend/models/CsvLogger.php';

class LoggingTest
{
    private $pdo;
    private $superAdminController;
    private $csvLogger;
    private $testUserId;

    public function __construct()
    {
        $this->pdo = $this->getTestDatabaseConnection();
        $this->superAdminController = new SuperAdminController($this->pdo);
        $this->csvLogger = new CsvLogger($this->pdo);
        $this->testUserId = null;
    }

    private function getTestDatabaseConnection()
    {
        try {
            // Use variables from the included db.php config instead of undefined constants
            $pdo = new PDO(
                "mysql:host=localhost;dbname=plmun_portal_system",
                "root",
                "",
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            return $pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function setupTestData()
    {
        echo "Setting up test data...\n";

        // Create a test user
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, role_id, active) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Test User', 'test@example.com', 3, 1]);
        $this->testUserId = $this->pdo->lastInsertId();

        echo "✅ Created test user with ID: {$this->testUserId}\n";
    }

    public function cleanupTestData()
    {
        echo "Cleaning up test data...\n";

        if ($this->testUserId) {
            // Delete test user
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$this->testUserId]);
            echo "✅ Deleted test user with ID: {$this->testUserId}\n";
        }
    }

    public function testUpdateUserLogging()
    {
        echo "\n🧪 Testing updateUser logging...\n";

        $updateData = [
            'name' => 'Updated Test User',
            'email' => 'updated@example.com',
            'role_id' => 4
        ];

        $result = $this->superAdminController->updateUser($this->testUserId, $updateData);

        if ($result) {
            echo "✅ User update successful\n";
            echo "✅ Logging should have been triggered\n";
        } else {
            echo "❌ User update failed\n";
        }

        return $result;
    }

    public function testDeleteUserLogging()
    {
        echo "\n🧪 Testing deleteUser logging...\n";

        // First create another test user for deletion
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, role_id, active) VALUES (?, ?, ?, ?)");
        $stmt->execute(['User to Delete', 'delete@example.com', 3, 1]);
        $deleteUserId = $this->pdo->lastInsertId();

        $result = $this->superAdminController->deleteUser($deleteUserId);

        if ($result) {
            echo "✅ User deletion successful\n";
            echo "✅ Logging should have been triggered\n";
        } else {
            echo "❌ User deletion failed\n";
        }

        return $result;
    }

    public function testBackupDatabaseLogging()
    {
        echo "\n🧪 Testing backupDatabase logging...\n";

        $result = $this->superAdminController->backupDatabase();

        if ($result['success']) {
            echo "✅ Database backup successful\n";
            echo "✅ Backup file: {$result['file']}\n";
            echo "✅ Logging should have been triggered\n";
        } else {
            echo "❌ Database backup failed\n";
        }

        return $result['success'];
    }

    public function testFailedOperationsLogging()
    {
        echo "\n🧪 Testing failed operations logging...\n";

        // Test updating non-existent user
        $result = $this->superAdminController->updateUser(999999, [
            'name' => 'Non-existent User',
            'email' => 'nonexistent@example.com',
            'role_id' => 3
        ]);

        if (!$result) {
            echo "✅ Failed user update logged correctly\n";
        } else {
            echo "❌ Expected failure but got success\n";
        }

        // Test deleting non-existent user
        $result = $this->superAdminController->deleteUser(999999);

        if (!$result) {
            echo "✅ Failed user deletion logged correctly\n";
        } else {
            echo "❌ Expected failure but got success\n";
        }

        return true;
    }

    public function verifyLogsCreated()
    {
        echo "\n🧪 Verifying logs were created...\n";

        // Check if CSV log files were created
        $logDir = __DIR__ . '/../logs/csv/';
        $csvFiles = glob($logDir . '*/*.csv');

        if (!empty($csvFiles)) {
            echo "✅ CSV log files found:\n";
            foreach ($csvFiles as $file) {
                echo "   - " . basename($file) . "\n";
            }
        } else {
            echo "⚠️  No CSV log files found\n";
        }

        // Check database logs
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as log_count FROM logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "✅ Database logs created in last hour: {$result['log_count']}\n";

        return true;
    }

    public function runAllTests()
    {
        echo "🚀 Starting Logging Implementation Tests\n";
        echo "=====================================\n";

        $this->setupTestData();

        $tests = [
            'testUpdateUserLogging',
            'testDeleteUserLogging',
            'testBackupDatabaseLogging',
            'testFailedOperationsLogging',
            'verifyLogsCreated'
        ];

        $results = [];

        foreach ($tests as $test) {
            try {
                $results[$test] = $this->$test();
            } catch (Exception $e) {
                echo "❌ Test {$test} failed with exception: " . $e->getMessage() . "\n";
                $results[$test] = false;
            }
        }

        $this->cleanupTestData();

        echo "\n📊 Test Results Summary:\n";
        echo "======================\n";

        $passed = 0;
        $total = count($results);

        foreach ($results as $test => $result) {
            $status = $result ? '✅ PASS' : '❌ FAIL';
            echo "{$test}: {$status}\n";
            if ($result) $passed++;
        }

        echo "\n🎯 Overall: {$passed}/{$total} tests passed\n";

        if ($passed === $total) {
            echo "🎉 All logging tests passed! Implementation is working correctly.\n";
        } else {
            echo "⚠️  Some tests failed. Please review the implementation.\n";
        }

        return $passed === $total;
    }
}

// Run the tests
$test = new LoggingTest();
$success = $test->runAllTests();

// Exit with appropriate code
exit($success ? 0 : 1);
?>
