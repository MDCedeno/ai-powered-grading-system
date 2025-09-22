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

// Remove query string from request URI
$path = strtok($request, '?');

// Remove router.php prefix if present
$scriptName = basename($_SERVER['SCRIPT_NAME']); // router.php
$pos = strpos($path, '/' . $scriptName);
if ($pos !== false) {
    $path = substr($path, $pos + strlen($scriptName) + 1);
    $path = '/' . ltrim($path, '/');
}

// SuperAdmin routes
if (strpos($path, '/api/superadmin') === 0) {
    $controller = new SuperAdminController($pdo);
    if ($path == '/api/superadmin/users' && $method == 'GET') {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $sort = $_GET['sort'] ?? 'name';
        $limit = $_GET['limit'] ?? 10;
        $userId = $_GET['userId'] ?? '';
        $users = $controller->getAllUsers($search, $role, $sort, $limit, $userId);
        echo json_encode($users);
    } elseif ($path == '/api/superadmin/users/deactivate' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->deactivateUser($data['user_id']);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/logs' && $method == 'GET') {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $logType = $_GET['logType'] ?? '';
        $logLevel = $_GET['logLevel'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $limit = $_GET['limit'] ?? 10;
        $logs = $controller->getSystemLogs($search, $status, $logType, $logLevel, $sort, $limit);
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
    } elseif ($path == '/api/superadmin/auto-backup' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $enabled = isset($data['enabled']) ? (bool)$data['enabled'] : false;
        $result = $controller->setAutoBackupStatus($enabled);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/auto-backup-interval' && $method == 'GET') {
        $interval = $controller->getAutoBackupInterval();
        echo json_encode(['interval' => $interval]);
    } elseif ($path == '/api/superadmin/auto-backup-interval' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->setAutoBackupInterval($data['interval']);
        echo json_encode(['success' => $result]);
    } elseif ($path == '/api/superadmin/restore' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controller->restoreDatabase($data['filename']);
        echo json_encode($result);
    } elseif ($path == '/api/superadmin/backup-files' && $method == 'GET') {
        $files = $controller->getBackupFiles();
        echo json_encode($files);
    } elseif ($path == '/api/superadmin/logs/export-csv' && $method == 'GET') {
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $logType = $_GET['log_type'] ?? null;
        $status = $_GET['status'] ?? null;
        $result = $controller->exportLogsToCsv($startDate, $endDate, $logType, $status);
        echo json_encode($result);
    } elseif ($path == '/api/superadmin/logs/csv-files' && $method == 'GET') {
        $limit = $_GET['limit'] ?? 10;
        $files = $controller->getCsvLogFiles($limit);
        echo json_encode($files);
    } elseif ($path == '/api/superadmin/logs/csv-config' && $method == 'GET') {
        $config = $controller->getCsvConfig();
        echo json_encode($config);
    } elseif ($path == '/api/superadmin/logs/csv-stats' && $method == 'GET') {
        $stats = $controller->getCsvStats();
        echo json_encode($stats);
    } elseif ($path == '/api/superadmin/logs/csv-cleanup' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $retentionDays = $data['retention_days'] ?? 30;
        $result = $controller->cleanupOldCsvLogs($retentionDays);
        echo json_encode($result);
    } elseif ($path == '/api/superadmin/logs/csv-settings' && $method == 'GET') {
        $enabled = $controller->isCsvLoggingEnabled();
        $retentionDays = $controller->getCsvRetentionDays();
        echo json_encode([
            'csv_logging_enabled' => $enabled,
            'csv_retention_days' => $retentionDays
        ]);
    } elseif ($path == '/api/superadmin/logs/csv-settings' && $method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $enabled = isset($data['enabled']) ? (bool)$data['enabled'] : null;
        $retentionDays = isset($data['retention_days']) ? (int)$data['retention_days'] : null;

        $results = [];
        if ($enabled !== null) {
            $results['csv_logging_enabled'] = $controller->setCsvLoggingEnabled($enabled);
        }
        if ($retentionDays !== null) {
            $results['csv_retention_days'] = $controller->setCsvRetentionDays($retentionDays);
        }

        echo json_encode(['success' => true, 'results' => $results]);
    } elseif ($path == '/api/superadmin/logs/csv-download' && $method == 'GET') {
        $filename = $_GET['filename'] ?? '';
        if (empty($filename)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Filename is required']);
        } else {
            $result = $controller->downloadCsvFile($filename);
            if ($result['success']) {
                // Set headers for file download
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                header('Content-Length: ' . $result['size']);
                readfile($result['file_path']);
                exit;
            } else {
                http_response_code(404);
                echo json_encode($result);
            }
        }
    } elseif ($path == '/api/superadmin/logs/csv-delete' && $method == 'DELETE') {
        $filename = $_GET['filename'] ?? '';
        if (empty($filename)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Filename is required']);
        } else {
            $result = $controller->deleteCsvFile($filename);
            echo json_encode($result);
        }
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
