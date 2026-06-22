<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
class Transaksi {
    private $conn;
    private $table_name = "transaksi_sewa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '', $sort_by = 'tgl_sewa', $sort_order = 'DESC') {
        $allowed_sort = ['id_transaksi', 'nama_pelanggan', 'nama_kendaraan', 'tgl_sewa', 'tgl_kembali', 'total_biaya', 'status_transaksi'];
        if (!in_array($sort_by, $allowed_sort)) {
            $sort_by = 'tgl_sewa';
        }
        $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT t.*, p.nama_pelanggan, k.nama_kendaraan, k.no_plat, k.jenis_kendaraan 
                  FROM " . $this->table_name . " t
                  JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan";
        
        if (!empty($search)) {
            $query .= " WHERE p.nama_pelanggan LIKE :search OR k.nama_kendaraan LIKE :search OR k.no_plat LIKE :search";
        }
        $query .= " ORDER BY " . $sort_by . " " . $sort_order;

        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $search_param = "%" . $search . "%";
            $stmt->bindParam(":search", $search_param);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT t.*, p.nama_pelanggan, k.nama_kendaraan, k.no_plat, k.jenis_kendaraan 
                  FROM " . $this->table_name . " t
                  JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
                  WHERE t.id_transaksi = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function calculateCost($jenis_kendaraan, $tgl_sewa, $tgl_kembali, $tgl_pengembalian_riil = null) {
        $start = strtotime($tgl_sewa);
        $end = strtotime($tgl_kembali);
        $diff = $end - $start;
        $lama_sewa = ceil($diff / (60 * 60 * 24));
        if ($lama_sewa <= 0) {
            $lama_sewa = 1;
        }

        if ($jenis_kendaraan === 'Motor') {
            $tarif_dasar = 80000;
        } elseif ($jenis_kendaraan === 'City Car') {
            $tarif_dasar = 350000;
        } elseif ($jenis_kendaraan === 'SUV/Premium') {
            $tarif_dasar = 700000;
        } else {
            $tarif_dasar = 0;
        }

        $denda = 0.0;
        $diskon = 0.0;
        $subtotal = $tarif_dasar * $lama_sewa;

        if ($lama_sewa >= 7) {
            $diskon = 0.10 * $subtotal;
        } elseif ($lama_sewa >= 3) {
            $diskon = 0.05 * $subtotal;
        } else {
            $diskon = 0.0;
        }

        if (!empty($tgl_pengembalian_riil)) {
            $riil_end = strtotime($tgl_pengembalian_riil);
            $late_diff = $riil_end - $end;
            if ($late_diff > 0) {
                $hari_keterlambatan = ceil($late_diff / (60 * 60 * 24));
                if ($jenis_kendaraan === 'Motor') {
                    $denda = $hari_keterlambatan * 25000;
                } elseif ($jenis_kendaraan === 'City Car') {
                    $denda = $hari_keterlambatan * 75000;
                } elseif ($jenis_kendaraan === 'SUV/Premium') {
                    $denda = $hari_keterlambatan * 150000;
                } else {
                    $denda = $hari_keterlambatan * 50000;
                }
            }
        }

        $total_biaya = ($tarif_dasar * $lama_sewa) + $denda - $diskon;
        if ($total_biaya < 0) {
            $total_biaya = 0;
        }

        return [
            'lama_sewa' => $lama_sewa,
            'tarif_dasar' => $tarif_dasar,
            'denda' => $denda,
            'diskon' => $diskon,
            'total_biaya' => $total_biaya
        ];
    }

    public function create($id_pelanggan, $id_kendaraan, $tgl_sewa, $tgl_kembali, $tgl_pengembalian_riil = null, $status_transaksi = 'Berjalan') {
        $query_kendaraan = "SELECT jenis_kendaraan FROM kendaraan WHERE id_kendaraan = :id_kendaraan";
        $stmt_k = $this->conn->prepare($query_kendaraan);
        $stmt_k->bindParam(":id_kendaraan", $id_kendaraan);
        $stmt_k->execute();
        $k_data = $stmt_k->fetch(PDO::FETCH_ASSOC);
        if (!$k_data) {
            return false;
        }

        $costs = $this->calculateCost($k_data['jenis_kendaraan'], $tgl_sewa, $tgl_kembali, $tgl_pengembalian_riil);

        $query = "INSERT INTO " . $this->table_name . " 
                  (id_pelanggan, id_kendaraan, tgl_sewa, tgl_kembali, tgl_pengembalian_riil, lama_sewa, tarif_dasar, denda, diskon, total_biaya, status_transaksi) 
                  VALUES (:id_pelanggan, :id_kendaraan, :tgl_sewa, :tgl_kembali, :tgl_pengembalian_riil, :lama_sewa, :tarif_dasar, :denda, :diskon, :total_biaya, :status_transaksi)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->bindParam(":id_kendaraan", $id_kendaraan);
        $stmt->bindParam(":tgl_sewa", $tgl_sewa);
        $stmt->bindParam(":tgl_kembali", $tgl_kembali);
        
        $riil_val = empty($tgl_pengembalian_riil) ? null : $tgl_pengembalian_riil;
        $stmt->bindParam(":tgl_pengembalian_riil", $riil_val);
        $stmt->bindParam(":lama_sewa", $costs['lama_sewa']);
        $stmt->bindParam(":tarif_dasar", $costs['tarif_dasar']);
        $stmt->bindParam(":denda", $costs['denda']);
        $stmt->bindParam(":diskon", $costs['diskon']);
        $stmt->bindParam(":total_biaya", $costs['total_biaya']);
        $stmt->bindParam(":status_transaksi", $status_transaksi);
        
        if ($stmt->execute()) {
            if ($status_transaksi === 'Berjalan') {
                $query_update = "UPDATE kendaraan SET status = 'Disewa' WHERE id_kendaraan = :id_kendaraan";
            } else {
                $query_update = "UPDATE kendaraan SET status = 'Tersedia' WHERE id_kendaraan = :id_kendaraan";
            }
            $stmt_u = $this->conn->prepare($query_update);
            $stmt_u->bindParam(":id_kendaraan", $id_kendaraan);
            $stmt_u->execute();
            return true;
        }
        return false;
    }

    public function update($id, $id_pelanggan, $id_kendaraan, $tgl_sewa, $tgl_kembali, $tgl_pengembalian_riil = null, $status_transaksi = 'Berjalan') {
        $old = $this->getById($id);
        if (!$old) {
            return false;
        }

        $query_kendaraan = "SELECT jenis_kendaraan FROM kendaraan WHERE id_kendaraan = :id_kendaraan";
        $stmt_k = $this->conn->prepare($query_kendaraan);
        $stmt_k->bindParam(":id_kendaraan", $id_kendaraan);
        $stmt_k->execute();
        $k_data = $stmt_k->fetch(PDO::FETCH_ASSOC);
        if (!$k_data) {
            return false;
        }

        $costs = $this->calculateCost($k_data['jenis_kendaraan'], $tgl_sewa, $tgl_kembali, $tgl_pengembalian_riil);

        $query = "UPDATE " . $this->table_name . " 
                  SET id_pelanggan = :id_pelanggan, id_kendaraan = :id_kendaraan, tgl_sewa = :tgl_sewa, tgl_kembali = :tgl_kembali, 
                      tgl_pengembalian_riil = :tgl_pengembalian_riil, lama_sewa = :lama_sewa, tarif_dasar = :tarif_dasar, 
                      denda = :denda, diskon = :diskon, total_biaya = :total_biaya, status_transaksi = :status_transaksi 
                  WHERE id_transaksi = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->bindParam(":id_kendaraan", $id_kendaraan);
        $stmt->bindParam(":tgl_sewa", $tgl_sewa);
        $stmt->bindParam(":tgl_kembali", $tgl_kembali);
        
        $riil_val = empty($tgl_pengembalian_riil) ? null : $tgl_pengembalian_riil;
        $stmt->bindParam(":tgl_pengembalian_riil", $riil_val);
        $stmt->bindParam(":lama_sewa", $costs['lama_sewa']);
        $stmt->bindParam(":tarif_dasar", $costs['tarif_dasar']);
        $stmt->bindParam(":denda", $costs['denda']);
        $stmt->bindParam(":diskon", $costs['diskon']);
        $stmt->bindParam(":total_biaya", $costs['total_biaya']);
        $stmt->bindParam(":status_transaksi", $status_transaksi);
        
        if ($stmt->execute()) {
            if ($old['id_kendaraan'] != $id_kendaraan) {
                $query_reset = "UPDATE kendaraan SET status = 'Tersedia' WHERE id_kendaraan = :id_kendaraan";
                $stmt_r = $this->conn->prepare($query_reset);
                $stmt_r->bindParam(":id_kendaraan", $old['id_kendaraan']);
                $stmt_r->execute();
            }

            if ($status_transaksi === 'Berjalan') {
                $query_update = "UPDATE kendaraan SET status = 'Disewa' WHERE id_kendaraan = :id_kendaraan";
            } else {
                $query_update = "UPDATE kendaraan SET status = 'Tersedia' WHERE id_kendaraan = :id_kendaraan";
            }
            $stmt_u = $this->conn->prepare($query_update);
            $stmt_u->bindParam(":id_kendaraan", $id_kendaraan);
            $stmt_u->execute();
            return true;
        }
        return false;
    }

    public function delete($id) {
        $old = $this->getById($id);
        if (!$old) {
            return false;
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id_transaksi = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            $query_reset = "UPDATE kendaraan SET status = 'Tersedia' WHERE id_kendaraan = :id_kendaraan";
            $stmt_r = $this->conn->prepare($query_reset);
            $stmt_r->bindParam(":id_kendaraan", $old['id_kendaraan']);
            $stmt_r->execute();
            return true;
        }
        return false;
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getCumulativeRevenue() {
        $query = "SELECT SUM(total_biaya) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getLaporanModul1() {
        $res = [];
        
        $query_k = "SELECT * FROM kendaraan ORDER BY nama_kendaraan ASC";
        $stmt_k = $this->conn->prepare($query_k);
        $stmt_k->execute();
        $res['kendaraan'] = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

        $query_p = "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC";
        $stmt_p = $this->conn->prepare($query_p);
        $stmt_p->execute();
        $res['pelanggan'] = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

        $query_t = "SELECT * FROM tarif_sewa ORDER BY jenis_kendaraan ASC";
        $stmt_t = $this->conn->prepare($query_t);
        $stmt_t->execute();
        $res['tarif'] = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

        return $res;
    }

    public function getLaporanModul2() {
        return $this->getAll('', 'tgl_sewa', 'DESC');
    }

    public function getLaporanModul3($start_date, $end_date) {
        $query = "SELECT t.*, p.nama_pelanggan, k.nama_kendaraan, k.no_plat, k.jenis_kendaraan 
                  FROM " . $this->table_name . " t
                  JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
                  WHERE t.tgl_sewa >= :start_date AND t.tgl_sewa <= :end_date
                  ORDER BY t.tgl_sewa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
