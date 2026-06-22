<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
class Kendaraan {
    private $conn;
    private $table_name = "kendaraan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '', $sort_by = 'nama_kendaraan', $sort_order = 'ASC') {
        $allowed_sort = ['id_kendaraan', 'nama_kendaraan', 'no_plat', 'jenis_kendaraan', 'status'];
        if (!in_array($sort_by, $allowed_sort)) {
            $sort_by = 'nama_kendaraan';
        }
        $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($search)) {
            $query .= " WHERE nama_kendaraan LIKE :search OR no_plat LIKE :search OR jenis_kendaraan LIKE :search";
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_kendaraan = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkPlatDuplikat($no_plat, $id = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE no_plat = :no_plat";
        if ($id !== null) {
            $query .= " AND id_kendaraan != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":no_plat", $no_plat);
        if ($id !== null) {
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($nama_kendaraan, $no_plat, $jenis_kendaraan, $status = 'Tersedia', $foto_kendaraan = 'default.jpg') {
        $query = "INSERT INTO " . $this->table_name . " (nama_kendaraan, no_plat, jenis_kendaraan, status, foto_kendaraan) VALUES (:nama_kendaraan, :no_plat, :jenis_kendaraan, :status, :foto_kendaraan)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama_kendaraan", $nama_kendaraan);
        $stmt->bindParam(":no_plat", $no_plat);
        $stmt->bindParam(":jenis_kendaraan", $jenis_kendaraan);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":foto_kendaraan", $foto_kendaraan);
        return $stmt->execute();
    }

    public function update($id, $nama_kendaraan, $no_plat, $jenis_kendaraan, $status, $foto_kendaraan) {
        $query = "UPDATE " . $this->table_name . " SET nama_kendaraan = :nama_kendaraan, no_plat = :no_plat, jenis_kendaraan = :jenis_kendaraan, status = :status, foto_kendaraan = :foto_kendaraan WHERE id_kendaraan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nama_kendaraan", $nama_kendaraan);
        $stmt->bindParam(":no_plat", $no_plat);
        $stmt->bindParam(":jenis_kendaraan", $jenis_kendaraan);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":foto_kendaraan", $foto_kendaraan);
        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id_kendaraan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":status", $status);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_kendaraan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
