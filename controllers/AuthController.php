<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/User.php';

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function login() {
        if (isset($_SESSION['user'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Username dan password wajib diisi';
            } else {
                $user = $this->userModel->login($username, $password);
                if ($user) {
                    $_SESSION['user'] = $user;
                    $_SESSION['role'] = $user['role'];
                    header("Location: index.php?action=dashboard");
                    exit;
                } else {
                    $error = 'Username atau password salah';
                }
            }
        }
        require_once 'views/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
