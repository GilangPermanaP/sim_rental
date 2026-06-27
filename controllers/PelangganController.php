<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/Pelanggan.php';

class PelangganController {
    private $pelangganModel;

    public function __construct($db) {
        $this->pelangganModel = new Pelanggan($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_pelanggan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';

        $customers = $this->pelangganModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_pelanggan.php';
    }

    public function create() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_pelanggan'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
            $nik = trim($_POST['nik'] ?? '');

            if (empty($nama) || empty($no_telp) || empty($alamat) || empty($nik)) {
                $error = 'Semua field wajib diisi';
            } elseif ($this->pelangganModel->checkNikDuplikat($nik)) {
                $error = 'NIK pelanggan sudah terdaftar';
            } else {
                if ($this->pelangganModel->create($nama, $no_telp, $alamat, $nik)) {
                    header("Location: index.php?action=master_pelanggan");
                    exit;
                } else {
                    $error = 'Gagal menyimpan data pelanggan';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_pelanggan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $customers = $this->pelangganModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_pelanggan.php';
    }

    public function update() {
        $error = '';
        $id = $_POST['id_pelanggan'] ?? $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=master_pelanggan");
            exit;
        }

        $customer = $this->pelangganModel->getById($id);
        if (!$customer) {
            header("Location: index.php?action=master_pelanggan");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_pelanggan'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
            $nik = trim($_POST['nik'] ?? '');

            if (empty($nama) || empty($no_telp) || empty($alamat) || empty($nik)) {
                $error = 'Semua field wajib diisi';
            } elseif ($this->pelangganModel->checkNikDuplikat($nik, $id)) {
                $error = 'NIK sudah terdaftar oleh pelanggan lain';
            } else {
                if ($this->pelangganModel->update($id, $nama, $no_telp, $alamat, $nik)) {
                    header("Location: index.php?action=master_pelanggan");
                    exit;
                } else {
                    $error = 'Gagal memperbarui data pelanggan';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'nama_pelanggan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $customers = $this->pelangganModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_pelanggan.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->pelangganModel->delete($id);
        }
        header("Location: index.php?action=master_pelanggan");
        exit;
    }
}
