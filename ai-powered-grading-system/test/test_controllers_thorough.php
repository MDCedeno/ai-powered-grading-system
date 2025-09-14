<?php
// Thorough testing script for all backend controllers (direct instantiation)

require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/controllers/superAdminController.php';
require_once __DIR__ . '/../backend/controllers/adminController.php';
require_once __DIR__ . '/../backend/controllers/professorController.php';
require_once __DIR__ . '/../backend/controllers/studentController.php';
require_once __DIR__ . '/../backend/controllers/authController.php';

// SuperAdmin tests
echo "Testing SuperAdmin Controller...\n";
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

if (count($users) > 0) {
    $userId = $users[0]['id'];
    $result = $superAdminController->deactivateUser($userId);
    if ($result) {
        echo "✓ deactivateUser succeeded on user ID $userId.\n";
    } else {
        echo "✗ deactivateUser failed.\n";
    }
}

// Admin tests
echo "\nTesting Admin Controller...\n";
$adminController = new AdminController($pdo);

$students = $adminController->getAllStudents();
if (is_array($students)) {
    echo "✓ getAllStudents returned " . count($students) . " students.\n";
} else {
    echo "✗ getAllStudents failed.\n";
}

// Test adding a student (if fewer than expected students exist)
if (count($students) < 5 && count($users) > 5) {
    $userId = $users[5]['id']; // Use a student user
    $result = $adminController->addStudent(['user_id' => $userId, 'program' => 'CS', 'year' => 3]);
    if ($result) {
        echo "✓ addStudent succeeded.\n";
    } else {
        echo "✗ addStudent failed.\n";
    }
}

// Professor tests
echo "\nTesting Professor Controller...\n";
// Set mock session for professor (assuming user ID 3 is a professor)
session_start();
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
if (count($professorStudents) > 0 && count($courses) > 0) {
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
}

// Student tests
echo "\nTesting Student Controller...\n";
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
echo "\nTesting Auth Controller...\n";
$authController = new AuthController($pdo);

// Test login with existing user
if (count($users) > 0) {
    $testUser = $users[0];
    $result = $authController->apiLogin($testUser['email'], 'password123');
    if ($result['success']) {
        echo "✓ login succeeded for user: " . $testUser['email'] . "\n";
    } else {
        echo "✗ login failed for user: " . $testUser['email'] . "\n";
    }
}

// Test registration
$result = $authController->register([
    'name' => 'Test User',
    'email' => 'test_' . time() . '@example.com',
    'password' => 'password123',
    'role' => 'student'
]);
if ($result['success']) {
    echo "✓ register succeeded.\n";
} else {
    echo "✗ register failed.\n";
}

echo "\nThorough controller testing completed.\n";
