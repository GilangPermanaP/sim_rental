<?php
session_start();
define('SECURE_ACCESS', true);

$default_path = './assets/images/default.jpg';
if (!is_dir('./assets/images/')) {
    mkdir('./assets/images/', 0777, true);
}
if (!file_exists($default_path)) {
    $img = imagecreatetruecolor(100, 100);
    $bg = imagecolorallocate($img, 245, 198, 203);
    imagefill($img, 0, 0, $bg);
    imagejpeg($img, $default_path);
    imagedestroy($img);
}

require_once 'config/Database.php';

$action = $_GET['action'] ?? 'dashboard';

// Auth Guard
if ($action === 'login') {
    if (isset($_SESSION['user'])) {
        header("Location: index.php?action=dashboard");
        exit;
    }
} else {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?action=login");
        exit;
    }
}

// RBAC Guard
if (isset($_SESSION['role'])) {
    $forbidden = ['master_kendaraan', 'kendaraan_create', 'kendaraan_update', 'kendaraan_delete', 'cetak_laporan'];
    if ($_SESSION['role'] === 'Operator' && in_array($action, $forbidden)) {
        $_SESSION['error_message'] = "Error: Operator tidak memiliki hak akses ke modul ini!";
        header("Location: index.php?action=dashboard");
        exit;
    }
}

$database = new Database();
$db = $database->getConnection();

switch ($action) {
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->login();
        break;
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        break;
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController($db);
        $controller->index();
        break;
    case 'master_kendaraan':
        require_once 'controllers/KendaraanController.php';
        $controller = new KendaraanController($db);
        $controller->index();
        break;
    case 'kendaraan_create':
        require_once 'controllers/KendaraanController.php';
        $controller = new KendaraanController($db);
        $controller->create();
        break;
    case 'kendaraan_update':
        require_once 'controllers/KendaraanController.php';
        $controller = new KendaraanController($db);
        $controller->update();
        break;
    case 'kendaraan_delete':
        require_once 'controllers/KendaraanController.php';
        $controller = new KendaraanController($db);
        $controller->delete();
        break;
    case 'form_transaksi':
        require_once 'controllers/TransaksiController.php';
        $controller = new TransaksiController($db);
        $controller->index();
        break;
    case 'transaksi_create':
        require_once 'controllers/TransaksiController.php';
        $controller = new TransaksiController($db);
        $controller->create();
        break;
    case 'transaksi_update':
        require_once 'controllers/TransaksiController.php';
        $controller = new TransaksiController($db);
        $controller->update();
        break;
    case 'transaksi_delete':
        require_once 'controllers/TransaksiController.php';
        $controller = new TransaksiController($db);
        $controller->delete();
        break;
    case 'cetak_laporan':
        require_once 'controllers/LaporanController.php';
        $controller = new LaporanController($db);
        $controller->index();
        break;
    default:
        header("Location: index.php?action=dashboard");
        exit;
}
