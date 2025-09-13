<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/adminController.php';
require_once __DIR__ . '/../controllers/professorController.php';
require_once __DIR__ . '/../controllers/studentController.php';
require_once __DIR__ . '/../controllers/superAdminController.php';

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Check if path is provided in query
$path = $_GET['path'] ?? '';

// If not in query, parse from URI
if (!$path) {
    // Remove query string
    $request = strtok($request, '?');

    // Extract path after /routes/api.php or just use the request if accessed via router
    if (strpos($request, '/routes/api.php') !== false) {
        $path = substr($request, strpos($request, '/routes/api.php') + strlen('/routes/api.php'));
    } elseif (strpos($request, '/api/') !== false) {
        // If accessed via router, the path starts with /api/
        $path = $request;
    } else {
        $path = $request;
    }
}

// SuperAdmin routes
if (strpos($path, '/api/superadmin') === 0) {
    $controller = new SuperAdminController($pdo);
    if ($path == '/api/superadmin/users' && $method == 'GET') {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $sort = $_GET['sort'] ?? 'name';
        $users = $controller->getAllUsers($search, $role, $sort);
        echo json_encode($users);
    } elseif ($path == '/api/superadmin/users/deactivate' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->deactivateUser($data['user_id']);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/logs' && $method == 'GET') {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $logs = $controller->getSystemLogs($search, $status, $sort);
        echo json_encode($logs);
    } elseif ($path == '/api/superadmin/users/activate' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->activateUser($data['user_id']);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/superadmin\/users\/(\d+)/', $path, $matches) && $method == 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateUser($id, $data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/superadmin\/users\/(\d+)/', $path, $matches) && $method == 'DELETE') {
        $id = $matches[1];
        $result = $controller->deleteUser($id);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/stats' && $method == 'GET') {
        $stats = $controller->getSystemStats();
        echo json_encode($stats);
    } elseif ($path == '/api/superadmin/backup' && $method == 'POST') {
        $result = $controller->backupDatabase();
        echo json_encode($result);
    } elseif ($path == '/api/superadmin/ai-config' && $method == 'GET') {
        $config = $controller->getAIConfig();
        echo json_encode($config);
    } elseif ($path == '/api/superadmin/ai-config' && $method == 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateAIConfig($data);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/settings' && $method == 'GET') {
        $settings = $controller->getSystemSettings();
        echo json_encode($settings);
    } elseif ($path == '/api/superadmin/settings' && $method == 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateSystemSettings($data);
        echo json_encode(['success' => $result]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'SuperAdmin endpoint not found']);
    }
}
// Admin routes
elseif (strpos($path, '/api/admin') === 0) {
    $controller = new AdminController($pdo);
    if ($path == '/api/admin/students' && $method == 'GET') {
        $students = $controller->getAllStudents();
        echo json_encode($students);
    } elseif ($path == '/api/admin/professors' && $method == 'GET') {
        $professors = $controller->getProfessors();
        echo json_encode($professors);
    } elseif ($path == '/api/admin/courses' && $method == 'GET') {
        $courses = $controller->getAllCourses();
        echo json_encode($courses);
    } elseif ($path == '/api/admin/grades' && $method == 'GET') {
        $grades = $controller->getAllGrades();
        echo json_encode($grades);
    } elseif ($path == '/api/admin/reports' && $method == 'GET') {
        $reports = $controller->generateReports();
        echo json_encode($reports);
    } elseif ($path == '/api/admin/audit-logs' && $method == 'GET') {
        $logs = $controller->getAuditLogs();
        echo json_encode($logs);
    } elseif ($path == '/api/admin/students' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->addStudent($data);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/admin/courses' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->addCourse($data);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/admin/grades' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->addGrade($data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/students\/(\d+)/', $path, $matches) && $method == 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateStudent($id, $data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/courses\/(\d+)/', $path, $matches) && $method == 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateCourse($id, $data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/grades\/(\d+)/', $path, $matches) && $method == 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateGrade($id, $data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/students\/(\d+)/', $path, $matches) && $method == 'DELETE') {
        $id = $matches[1];
        $result = $controller->deleteStudent($id);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/courses\/(\d+)/', $path, $matches) && $method == 'DELETE') {
        $id = $matches[1];
        $result = $controller->deleteCourse($id);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/admin\/grades\/(\d+)/', $path, $matches) && $method == 'DELETE') {
        $id = $matches[1];
        $result = $controller->deleteGrade($id);
        echo json_encode(['success' => $result]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Admin endpoint not found']);
    }
}
// Professor routes
elseif (strpos($path, '/api/professor') === 0) {
    $controller = new ProfessorController($pdo);
    if ($path == '/api/professor/students' && $method == 'GET') {
        $students = $controller->getMyStudents();
        echo json_encode($students);
    } elseif ($path == '/api/professor/courses' && $method == 'GET') {
        $courses = $controller->getMyCourses();
        echo json_encode($courses);
    } elseif ($path == '/api/professor/grades' && $method == 'GET') {
        $grades = $controller->getMyGrades();
        echo json_encode($grades);
    } elseif ($path == '/api/professor/grades' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->addGrade($data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/professor\/grades\/(\d+)/', $path, $matches) && $method == 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->updateGrade($id, $data);
        echo json_encode(['success' => $result]);
    } elseif (preg_match('/\/api\/professor\/grades\/(\d+)/', $path, $matches) && $method == 'DELETE') {
        $id = $matches[1];
        $result = $controller->deleteGrade($id);
        echo json_encode(['success' => $result]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Professor endpoint not found']);
    }
}
// Student routes
elseif (strpos($path, '/api/student') === 0) {
    $controller = new StudentController($pdo);
    if ($path == '/api/student/grades' && $method == 'GET') {
        $grades = $controller->getMyGrades();
        echo json_encode($grades);
    } elseif ($path == '/api/student/courses' && $method == 'GET') {
        $courses = $controller->getMyCourses();
        echo json_encode($courses);
    } elseif ($path == '/api/student/notifications' && $method == 'GET') {
        $notifications = $controller->getNotifications();
        echo json_encode($notifications);
    } elseif ($path == '/api/student/quizzes' && $method == 'GET') {
        $quizzes = $controller->getQuizzes();
        echo json_encode($quizzes);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Student endpoint not found']);
    }
}
// Auth routes
elseif (strpos($path, '/api/auth') === 0) {
    $controller = new AuthController($pdo);
    if ($path == '/api/auth/login' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->apiLogin($data['email'], $data['password']);
        echo json_encode($result);
    } elseif ($path == '/api/auth/register' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->register($data);
        echo json_encode($result);
    } elseif ($path == '/api/auth/logout' && $method == 'POST') {
        $result = $controller->logout();
        echo json_encode(['success' => $result]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Auth endpoint not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found']);
}
?>
