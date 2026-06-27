<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');

class TarifSewa {
    private $conn;
    private $table_name = "tarif_sewa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '', $sort_by = 'jenis_kendaraan', $sort_order = 'ASC') {
        $allowed_sort = ['id_tarif', 'jenis_kendaraan', 'tarif_per_hari'];
        if (!in_array($sort_by, $allowed_sort)) {
            $sort_by = 'jenis_kendaraan';
        }
        $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($search)) {
            $query .= " WHERE jenis_kendaraan LIKE :search";
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_tarif = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkJenisDuplikat($jenis_kendaraan, $id = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE jenis_kendaraan = :jenis";
        if ($id !== null) {
            $query .= " AND id_tarif != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":jenis", $jenis_kendaraan);
        if ($id !== null) {
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($jenis_kendaraan, $tarif_per_hari) {
        $query = "INSERT INTO " . $this->table_name . " (jenis_kendaraan, tarif_per_hari) VALUES (:jenis, :tarif)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":jenis", $jenis_kendaraan);
        $stmt->bindParam(":tarif", $tarif_per_hari);
        return $stmt->execute();
    }

    public function update($id, $jenis_kendaraan, $tarif_per_hari) {
        $query = "UPDATE " . $this->table_name . " SET jenis_kendaraan = :jenis, tarif_per_hari = :tarif WHERE id_tarif = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":jenis", $jenis_kendaraan);
        $stmt->bindParam(":tarif", $tarif_per_hari);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_tarif = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
