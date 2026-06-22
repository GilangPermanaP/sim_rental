<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/Pelanggan.php';
require_once 'models/Kendaraan.php';
require_once 'models/Transaksi.php';

class DashboardController {
    private $pelangganModel;
    private $kendaraanModel;
    private $transaksiModel;

    public function __construct($db) {
        $this->pelangganModel = new Pelanggan($db);
        $this->kendaraanModel = new Kendaraan($db);
        $this->transaksiModel = new Transaksi($db);
    }

    public function index() {
        $stats = [
            'pelanggan' => $this->pelangganModel->getCount(),
            'kendaraan' => $this->kendaraanModel->getCount(),
            'transaksi' => $this->transaksiModel->getCount(),
            'revenue' => $this->transaksiModel->getCumulativeRevenue()
        ];
        require_once 'views/dashboard.php';
    }
}
