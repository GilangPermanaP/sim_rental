<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
class Pelanggan {
    private $conn;
    private $table_name = "pelanggan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '', $sort_by = 'nama_pelanggan', $sort_order = 'ASC') {
        $allowed_sort = ['id_pelanggan', 'nama_pelanggan', 'no_telp', 'alamat', 'nik'];
        if (!in_array($sort_by, $allowed_sort)) {
            $sort_by = 'nama_pelanggan';
        }
        $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($search)) {
            $query .= " WHERE nama_pelanggan LIKE :search OR nik LIKE :search OR no_telp LIKE :search";
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkNikDuplikat($nik, $id = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nik = :nik";
        if ($id !== null) {
            $query .= " AND id_pelanggan != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nik", $nik);
        if ($id !== null) {
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($nama, $no_telp, $alamat, $nik) {
        $query = "INSERT INTO " . $this->table_name . " (nama_pelanggan, no_telp, alamat, nik) VALUES (:nama, :no_telp, :alamat, :nik)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":no_telp", $no_telp);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":nik", $nik);
        return $stmt->execute();
    }

    public function update($id, $nama, $no_telp, $alamat, $nik) {
        $query = "UPDATE " . $this->table_name . " SET nama_pelanggan = :nama, no_telp = :no_telp, alamat = :alamat, nik = :nik WHERE id_pelanggan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":no_telp", $no_telp);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":nik", $nik);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pelanggan = :id";
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
