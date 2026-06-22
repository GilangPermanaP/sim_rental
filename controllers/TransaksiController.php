<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/Transaksi.php';
require_once 'models/Kendaraan.php';
require_once 'models/Pelanggan.php';

class TransaksiController {
    private $transaksiModel;
    private $kendaraanModel;
    private $pelangganModel;

    public function __construct($db) {
        $this->transaksiModel = new Transaksi($db);
        $this->kendaraanModel = new Kendaraan($db);
        $this->pelangganModel = new Pelanggan($db);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'tgl_sewa';
        $sort_order = $_GET['sort_order'] ?? 'DESC';

        $transactions = $this->transaksiModel->getAll($search, $sort_by, $sort_order);
        
        $vehicles = $this->kendaraanModel->getAll('', 'nama_kendaraan', 'ASC');
        $customers = $this->pelangganModel->getAll('', 'nama_pelanggan', 'ASC');

        $is_vehicles_empty = empty($vehicles);

        require_once 'views/form_transaksi.php';
    }

    public function create() {
        $error = '';
        
        $vehicles = $this->kendaraanModel->getAll('', 'nama_kendaraan', 'ASC');
        if (empty($vehicles)) {
            $error = 'Transaksi ditolak. Master data kendaraan masih kosong.';
            $transactions = $this->transaksiModel->getAll('', 'tgl_sewa', 'DESC');
            $customers = $this->pelangganModel->getAll('', 'nama_pelanggan', 'ASC');
            $is_vehicles_empty = true;
            require_once 'views/form_transaksi.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pelanggan = $_POST['id_pelanggan'] ?? '';
            $id_kendaraan = $_POST['id_kendaraan'] ?? '';
            $tgl_sewa = $_POST['tgl_sewa'] ?? '';
            $tgl_kembali = $_POST['tgl_kembali'] ?? '';

            if (empty($id_pelanggan) || empty($id_kendaraan) || empty($tgl_sewa) || empty($tgl_kembali)) {
                $error = 'Semua field sewa wajib diisi';
            } elseif (strtotime($tgl_kembali) < strtotime($tgl_sewa)) {
                $error = 'Tanggal kembali tidak boleh mendahului tanggal sewa';
            } else {
                if ($this->transaksiModel->create($id_pelanggan, $id_kendaraan, $tgl_sewa, $tgl_kembali, null, 'Berjalan')) {
                    header("Location: index.php?action=form_transaksi");
                    exit;
                } else {
                    $error = 'Gagal memproses transaksi';
                }
            }
        }

        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'tgl_sewa';
        $sort_order = $_GET['sort_order'] ?? 'DESC';
        $transactions = $this->transaksiModel->getAll($search, $sort_by, $sort_order);
        $customers = $this->pelangganModel->getAll('', 'nama_pelanggan', 'ASC');
        $is_vehicles_empty = false;
        require_once 'views/form_transaksi.php';
    }

    public function update() {
        $error = '';
        $id = $_POST['id_transaksi'] ?? $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=form_transaksi");
            exit;
        }

        $transaction = $this->transaksiModel->getById($id);
        if (!$transaction) {
            header("Location: index.php?action=form_transaksi");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pelanggan = $_POST['id_pelanggan'] ?? $transaction['id_pelanggan'];
            $id_kendaraan = $_POST['id_kendaraan'] ?? $transaction['id_kendaraan'];
            $tgl_sewa = $_POST['tgl_sewa'] ?? $transaction['tgl_sewa'];
            $tgl_kembali = $_POST['tgl_kembali'] ?? $transaction['tgl_kembali'];
            $tgl_pengembalian_riil = $_POST['tgl_pengembalian_riil'] ?? '';
            $status_transaksi = $_POST['status_transaksi'] ?? 'Berjalan';

            if (empty($id_pelanggan) || empty($id_kendaraan) || empty($tgl_sewa) || empty($tgl_kembali)) {
                $error = 'Semua field sewa wajib diisi';
            } elseif (strtotime($tgl_kembali) < strtotime($tgl_sewa)) {
                $error = 'Tanggal kembali tidak boleh mendahului tanggal sewa';
            } elseif ($status_transaksi === 'Selesai' && empty($tgl_pengembalian_riil)) {
                $error = 'Tanggal pengembalian riil wajib diisi jika status Selesai';
            } else {
                $riil = empty($tgl_pengembalian_riil) ? null : $tgl_pengembalian_riil;
                if ($this->transaksiModel->update($id, $id_pelanggan, $id_kendaraan, $tgl_sewa, $tgl_kembali, $riil, $status_transaksi)) {
                    header("Location: index.php?action=form_transaksi");
                    exit;
                } else {
                    $error = 'Gagal memperbarui transaksi';
                }
            }
        }

        $search = $_GET['search'] ?? '';
        $sort_by = $_GET['sort_by'] ?? 'tgl_sewa';
        $sort_order = $_GET['sort_order'] ?? 'DESC';
        $transactions = $this->transaksiModel->getAll($search, $sort_by, $sort_order);
        $vehicles = $this->kendaraanModel->getAll('', 'nama_kendaraan', 'ASC');
        $customers = $this->pelangganModel->getAll('', 'nama_pelanggan', 'ASC');
        $is_vehicles_empty = empty($vehicles);
        require_once 'views/form_transaksi.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->transaksiModel->delete($id);
        }
        header("Location: index.php?action=form_transaksi");
        exit;
    }
}
