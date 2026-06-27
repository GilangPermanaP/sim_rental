<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/User.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'username';
        $sort_order = $_GET['sort_order'] ?? 'ASC';

        $users = $this->userModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_user.php';
    }

    public function create() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($nama_lengkap) || empty($role) || empty($password)) {
                $error = 'Semua field wajib diisi';
            } elseif ($this->userModel->checkUsernameDuplikat($username)) {
                $error = 'Username sudah terdaftar';
            } else {
                if ($this->userModel->create($username, $password, $nama_lengkap, $role)) {
                    header("Location: index.php?action=master_user");
                    exit;
                } else {
                    $error = 'Gagal menyimpan data user';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'username';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $users = $this->userModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_user.php';
    }

    public function update() {
        $error = '';
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=master_user");
            exit;
        }

        $user = $this->userModel->getById($id);
        if (!$user) {
            header("Location: index.php?action=master_user");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($nama_lengkap) || empty($role)) {
                $error = 'Semua field (kecuali password) wajib diisi';
            } elseif ($this->userModel->checkUsernameDuplikat($username, $id)) {
                $error = 'Username sudah terdaftar oleh user lain';
            } else {
                if ($this->userModel->update($id, $username, $password, $nama_lengkap, $role)) {
                    header("Location: index.php?action=master_user");
                    exit;
                } else {
                    $error = 'Gagal memperbarui data user';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'username';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $users = $this->userModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_user.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        // Prevent deleting logged-in user
        if ($id && $id != $_SESSION['user']['id']) {
            $this->userModel->delete($id);
        } elseif ($id == $_SESSION['user']['id']) {
            $_SESSION['error_message'] = "Gagal: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!";
        }
        header("Location: index.php?action=master_user");
        exit;
    }
}
