<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thorough testing script for all backend controllers (direct instantiation)

require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/controllers/superAdminController.php';
require_once __DIR__ . '/../backend/controllers/adminController.php';
require_once __DIR__ . '/../backend/controllers/professorController.php';
require_once __DIR__ . '/../backend/controllers/studentController.php';
require_once __DIR__ . '/../backend/controllers/authController.php';

// Check if $pdo is set and valid
if (!isset($pdo) || !$pdo) {
    die("Database connection (\$pdo) is not initialized.\n");
}

// SuperAdmin tests
echo "========== Testing SuperAdmin Controller ==========\n";
$superAdminController = new SuperAdminController($pdo);

$users = $superAdminController->getAllUsers();
if (is_array($users)) {
    echo "✓ getAllUsers returned " . count($users) . " users.\n";
} else {
    echo "✗ getAllUsers failed.\n";
}

$logs = $superAdminController->getSystemLogs();
if (is_array($logs)) {
    echo "✓ getSystemLogs returned " . count($logs) . " logs.\n";
} else {
    echo "✗ getSystemLogs failed.\n";
}

if (is_array($users) && count($users) > 0 && isset($users[0]['id'])) {
    $userId = $users[0]['id'];
    $result = $superAdminController->deactivateUser($userId);
    if ($result) {
        echo "✓ deactivateUser succeeded on user ID $userId.\n";
    } else {
        echo "✗ deactivateUser failed.\n";
    }
} else {
    echo "✗ No users found to test deactivateUser.\n";
}

// Admin tests
echo "\n========== Testing Admin Controller ==========\n";
$adminController = new AdminController($pdo);

$students = $adminController->getAllStudents();
if (is_array($students)) {
    echo "✓ getAllStudents returned " . count($students) . " students.\n";
} else {
    echo "✗ getAllStudents failed.\n";
}

// Test adding a student (if fewer than expected students exist)
if (is_array($students) && count($students) < 5 && is_array($users) && count($users) > 5 && isset($users[5]['id'])) {
    $userId = $users[5]['id']; // Use a student user
    $result = $adminController->addStudent(['user_id' => $userId, 'program' => 'CS', 'year' => 3]);
    if ($result) {
        echo "✓ addStudent succeeded.\n";
    } else {
        echo "✗ addStudent failed.\n";
    }
} else {
    echo "Skipped addStudent test (not enough users or students).\n";
}

// Professor tests
echo "\n========== Testing Professor Controller ==========\n";
// Set mock session for professor (assuming user ID 3 is a professor)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_id'] = 3; // Assuming user ID 3 is a professor
$professorController = new ProfessorController($pdo);

$professorStudents = $professorController->getMyStudents();
if (is_array($professorStudents)) {
    echo "✓ getMyStudents returned " . count($professorStudents) . " students.\n";
} else {
    echo "✗ getMyStudents failed.\n";
}

$courses = $professorController->getMyCourses();
if (is_array($courses)) {
    echo "✓ getMyCourses returned " . count($courses) . " courses.\n";
} else {
    echo "✗ getMyCourses failed.\n";
}

// Test creating a grade if students and courses exist
if (is_array($professorStudents) && count($professorStudents) > 0 && is_array($courses) && count($courses) > 0) {
    $studentId = $professorStudents[0]['id'];
    $courseId = $courses[0]['id'];
    $gradeData = [
        'student_id' => $studentId,
        'course_id' => $courseId,
        'midterm_quizzes' => 15,
        'midterm_exam' => 25,
        'final_quizzes' => 15,
        'final_exam' => 25
    ];
    $result = $professorController->addGrade($gradeData);
    if ($result) {
        echo "✓ addGrade succeeded.\n";
    } else {
        echo "✗ addGrade failed.\n";
    }
} else {
    echo "Skipped addGrade test (not enough students or courses).\n";
}

// Student tests
echo "\n========== Testing Student Controller ==========\n";
$studentController = new StudentController($pdo);

$studentGrades = $studentController->getMyGrades();
if (is_array($studentGrades)) {
    echo "✓ getMyGrades returned " . count($studentGrades) . " grades.\n";
} else {
    echo "✗ getMyGrades failed.\n";
}

$studentCourses = $studentController->getMyCourses();
if (is_array($studentCourses)) {
    echo "✓ getMyCourses returned " . count($studentCourses) . " courses.\n";
} else {
    echo "✗ getMyCourses failed.\n";
}

// Auth tests
echo "\n========== Testing Auth Controller ==========\n";
$authController = new AuthController($pdo);

// Test login with existing user
if (is_array($users) && count($users) > 0 && isset($users[0]['email'])) {
    $testUser = $users[0];
    $result = $authController->apiLogin($testUser['email'], 'password123');
    if (isset($result['success']) && $result['success']) {
        echo "✓ login succeeded for user: " . $testUser['email'] . "\n";
    } else {
        echo "✗ login failed for user: " . $testUser['email'] . "\n";
    }
} else {
    echo "Skipped login test (no users found).\n";
}

// Test registration
$registerEmail = 'test_' . time() . '@example.com';
$result = $authController->register([
    'name' => 'Test User',
    'email' => $registerEmail,
    'password' => 'password123',
    'role' => 'student'
]);
if (isset($result['success']) && $result['success']) {
    echo "✓ register succeeded for $registerEmail.\n";
} else {
    echo "✗ register failed for $registerEmail.\n";
}

echo "\n========== Thorough controller testing completed ==========\n";
