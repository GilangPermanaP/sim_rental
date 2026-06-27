<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/TarifSewa.php';

class TarifSewaController {
    private $tarifSewaModel;

    public function __construct($db) {
        $this->tarifSewaModel = new TarifSewa($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'jenis_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';

        $tariffs = $this->tarifSewaModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_tarif_sewa.php';
    }

    public function create() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jenis = trim($_POST['jenis_kendaraan'] ?? '');
            $tarif = $_POST['tarif_per_hari'] ?? '';

            if (empty($jenis) || $tarif === '') {
                $error = 'Semua field wajib diisi';
            } elseif (!is_numeric($tarif) || floatval($tarif) < 0) {
                $error = 'Tarif per hari harus berupa angka positif';
            } elseif ($this->tarifSewaModel->checkJenisDuplikat($jenis)) {
                $error = 'Jenis kendaraan sudah memiliki tarif terdaftar';
            } else {
                if ($this->tarifSewaModel->create($jenis, floatval($tarif))) {
                    header("Location: index.php?action=master_tarif_sewa");
                    exit;
                } else {
                    $error = 'Gagal menyimpan data tarif';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'jenis_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $tariffs = $this->tarifSewaModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_tarif_sewa.php';
    }

    public function update() {
        $error = '';
        $id = $_POST['id_tarif'] ?? $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=master_tarif_sewa");
            exit;
        }

        $tariff = $this->tarifSewaModel->getById($id);
        if (!$tariff) {
            header("Location: index.php?action=master_tarif_sewa");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jenis = trim($_POST['jenis_kendaraan'] ?? '');
            $tarif = $_POST['tarif_per_hari'] ?? '';

            if (empty($jenis) || $tarif === '') {
                $error = 'Semua field wajib diisi';
            } elseif (!is_numeric($tarif) || floatval($tarif) < 0) {
                $error = 'Tarif per hari harus berupa angka positif';
            } elseif ($this->tarifSewaModel->checkJenisDuplikat($jenis, $id)) {
                $error = 'Jenis kendaraan sudah memiliki tarif terdaftar oleh baris lain';
            } else {
                if ($this->tarifSewaModel->update($id, $jenis, floatval($tarif))) {
                    header("Location: index.php?action=master_tarif_sewa");
                    exit;
                } else {
                    $error = 'Gagal memperbarui data tarif';
                }
            }
        }
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'jenis_kendaraan';
        $sort_order = $_GET['sort_order'] ?? 'ASC';
        $tariffs = $this->tarifSewaModel->getAll($search, $sort_by, $sort_order);
        require_once 'views/master_tarif_sewa.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tarifSewaModel->delete($id);
        }
        header("Location: index.php?action=master_tarif_sewa");
        exit;
    }
}
