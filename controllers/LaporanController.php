<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
require_once 'models/Transaksi.php';

class LaporanController {
    private $transaksiModel;

    public function __construct($db) {
        $this->transaksiModel = new Transaksi($db);
    }

    public function index() {
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        $master_data = $this->transaksiModel->getLaporanModul1();
        $semua_transaksi = $this->transaksiModel->getLaporanModul2();
        $transaksi_filter = $this->transaksiModel->getLaporanModul3($start_date, $end_date);
        $total_pendapatan = $this->transaksiModel->getCumulativeRevenue();

        require_once 'views/cetak_laporan.php';
    }
}
