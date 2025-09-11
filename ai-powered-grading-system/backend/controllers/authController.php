<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/user.php';

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

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
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

                // Redirect based on role
                $this->redirectBasedOnRole($user['role_id']);
            } else {
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
                $_SESSION['success'] = 'Account created successfully. Please login.';
                header('Location: ../frontend/views/login.php');
                exit;
            } else {
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
