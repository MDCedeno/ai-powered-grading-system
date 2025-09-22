<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/CsvLogger.php';

$authController = new AuthController($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'login') {
        $authController->login();
    } elseif ($action === 'signup') {
        $authController->signup();
    }
}

class AuthController {
    private $userModel;
    private $csvLogger;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->csvLogger = new CsvLogger($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->verifyPassword($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role'] = $this->getRoleName($user['role_id']);

                // Log successful login
                $roleName = $this->getRoleName($user['role_id']);
                $this->csvLogger->writeLog(
                    $user['id'],
                    'authentication',
                    'login',
                    "Successful login for {$roleName}",
                    1,
                    null
                );

                // Redirect based on role
                $this->redirectBasedOnRole($user['role_id']);
            } else {
                // Log failed login attempt
                $this->csvLogger->writeLog(
                    null,
                    'authentication',
                    'login_failed',
                    "Failed login attempt for email: {$email}",
                    0,
                    'Invalid email or password'
                );

                $_SESSION['error'] = 'Invalid email or password';
                header('Location: ../frontend/views/login.php');
                exit;
            }
        }
    }

    public function apiLogin($email, $password) {
        $user = $this->userModel->verifyPassword($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role'] = $this->getRoleName($user['role_id']);

            return ['success' => true, 'user' => $user, 'role' => $this->getRoleName($user['role_id'])];
        } else {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['password'];

            // Map role to role_id
            $role_id = $this->getRoleId($role);
            if (!$role_id) {
                $_SESSION['error'] = 'Invalid role selected';
                header('Location: ../frontend/views/signup.php');
                exit;
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: ../frontend/views/signup.php');
                exit;
            }

            if ($this->userModel->create($name, $email, $password, $role_id)) {
                // Log successful account creation
                $roleName = $this->getRoleName($role_id);
                $this->csvLogger->writeLog(
                    null, // No user_id yet for new accounts
                    'account_lifecycle',
                    'account_created',
                    "New {$roleName} account created: {$email}",
                    1,
                    null
                );

                $_SESSION['success'] = 'Account created successfully. Please login.';
                header('Location: ../frontend/views/login.php');
                exit;
            } else {
                // Log failed account creation
                $this->csvLogger->writeLog(
                    null,
                    'account_lifecycle',
                    'account_creation_failed',
                    "Failed to create account for email: {$email}",
                    0,
                    'Database error during account creation'
                );

                $_SESSION['error'] = 'Failed to create account';
                header('Location: ../frontend/views/signup.php');
                exit;
            }
        }
    }

    private function getRoleId($role) {
        $roles = [
            'student' => 4, // Assuming Student is id 4
            'professor' => 3 // Assuming Professor is id 3
        ];
        return $roles[$role] ?? null;
    }

    private function getRoleName($role_id) {
        switch ($role_id) {
            case 1:
                return 'Super Admin';
            case 2:
                return 'MIS Admin';
            case 3:
                return 'Professor';
            case 4:
                return 'Student';
            default:
                return 'Unknown';
        }
    }

    private function redirectBasedOnRole($role_id) {
        switch ($role_id) {
            case 1: // Super Admin
                header('Location: ../../frontend/views/super-admin/super-admin.php');
                break;
            case 2: // MIS Admin
                header('Location: ../../frontend/views/admin/mis-admin.php');
                break;
            case 3: // Professor
                header('Location: ../../frontend/views/professor/professor.php');
                break;
            case 4: // Student
                header('Location: ../../frontend/views/student/student.php');
                break;
            default:
                header('Location: ../../frontend/views/login.php');
        }
        exit;
    }

    public function register($data) {
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $role = $data['role'];

        $role_id = $this->getRoleId($role);
        if (!$role_id) {
            return ['success' => false, 'message' => 'Invalid role selected'];
        }

        if ($this->userModel->findByEmail($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        if ($this->userModel->create($name, $email, $password, $role_id)) {
            return ['success' => true, 'message' => 'Account created successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to create account'];
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }
}
?>
