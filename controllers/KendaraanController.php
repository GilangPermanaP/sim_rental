<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/Kendaraan.php';

class KendaraanController {
    private $kendaraanModel;

    public function __construct($db) {
        $this->kendaraanModel = new Kendaraan($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';

        $vehicles = $this->kendaraanModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_kendaraan.php';
    }

    public function create() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kendaraan = $_POST['nama_kendaraan'] ?? '';
            $no_plat = $_POST['no_plat'] ?? '';
            $jenis_kendaraan = $_POST['jenis_kendaraan'] ?? '';
            $status = $_POST['status'] ?? 'Tersedia';

            if (empty($nama_kendaraan) || empty($no_plat) || empty($jenis_kendaraan)) {
                $error = 'Semua field wajib diisi';
            } elseif ($this->kendaraanModel->checkPlatDuplikat($no_plat)) {
                $error = 'Nomor plat kendaraan sudah terdaftar';
            } else {
                $foto = 'default.jpg';
                if (isset($_FILES['foto_kendaraan']) && $_FILES['foto_kendaraan']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['foto_kendaraan']['tmp_name'];
                    $fileName = $_FILES['foto_kendaraan']['name'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                        $uploadFileDir = './assets/images/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0777, true);
                        }
                        $dest_path = $uploadFileDir . $newFileName;
                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            $foto = $newFileName;
                        }
                    }
                }

                if ($this->kendaraanModel->create($nama_kendaraan, $no_plat, $jenis_kendaraan, $status, $foto)) {
                    header("Location: index.php?action=master_kendaraan");
                    exit;
                } else {
                    $error = 'Gagal menyimpan data kendaraan';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $vehicles = $this->kendaraanModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_kendaraan.php';
    }

    public function update() {
        $error = '';
        $id = $_POST['id_kendaraan'] ?? $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=master_kendaraan");
            exit;
        }

        $vehicle = $this->kendaraanModel->getById($id);
        if (!$vehicle) {
            header("Location: index.php?action=master_kendaraan");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kendaraan = $_POST['nama_kendaraan'] ?? '';
            $no_plat = $_POST['no_plat'] ?? '';
            $jenis_kendaraan = $_POST['jenis_kendaraan'] ?? '';
            $status = $_POST['status'] ?? 'Tersedia';

            if (empty($nama_kendaraan) || empty($no_plat) || empty($jenis_kendaraan)) {
                $error = 'Semua field wajib diisi';
            } elseif ($this->kendaraanModel->checkPlatDuplikat($no_plat, $id)) {
                $error = 'Nomor plat kendaraan sudah terdaftar oleh kendaraan lain';
            } else {
                $foto = $vehicle['foto_kendaraan'];
                if (isset($_FILES['foto_kendaraan']) && $_FILES['foto_kendaraan']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['foto_kendaraan']['tmp_name'];
                    $fileName = $_FILES['foto_kendaraan']['name'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                        $uploadFileDir = './assets/images/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0777, true);
                        }
                        $dest_path = $uploadFileDir . $newFileName;
                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            if ($foto !== 'default.jpg' && file_exists($uploadFileDir . $foto)) {
                                unlink($uploadFileDir . $foto);
                            }
                            $foto = $newFileName;
                        }
                    }
                }

                if ($this->kendaraanModel->update($id, $nama_kendaraan, $no_plat, $jenis_kendaraan, $status, $foto)) {
                    header("Location: index.php?action=master_kendaraan");
                    exit;
                } else {
                    $error = 'Gagal memperbarui data kendaraan';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $vehicles = $this->kendaraanModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_kendaraan.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $vehicle = $this->kendaraanModel->getById($id);
            if ($vehicle) {
                $foto = $vehicle['foto_kendaraan'];
                if ($foto !== 'default.jpg' && file_exists('./assets/images/' . $foto)) {
                    unlink('./assets/images/' . $foto);
                }
                $this->kendaraanModel->delete($id);
            }
        }
        header("Location: index.php?action=master_kendaraan");
        exit;
    }
}
