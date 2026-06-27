<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
class User {
    private $conn;
    private $table_name = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CRUD Model
    public function getAll($search = '', $sort_by = 'username', $sort_order = 'ASC') {
        $allowed_sort = ['id', 'username', 'nama_lengkap', 'role'];
        if (!in_array($sort_by, $allowed_sort)) {
            $sort_by = 'username';
        }
        $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT id, username, nama_lengkap, role FROM " . $this->table_name;
        if (!empty($search)) {
            $query .= " WHERE username LIKE :search OR nama_lengkap LIKE :search OR role LIKE :search";
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

    public function checkUsernameDuplikat($username, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        if ($id !== null) {
            $query .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        if ($id !== null) {
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($username, $password, $nama_lengkap, $role) {
        $query = "INSERT INTO " . $this->table_name . " (username, password, nama_lengkap, role) VALUES (:username, :password, :nama_lengkap, :role)";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":nama_lengkap", $nama_lengkap);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }

    public function update($id, $username, $password, $nama_lengkap, $role) {
        if (!empty($password)) {
            $query = "UPDATE " . $this->table_name . " SET username = :username, password = :password, nama_lengkap = :nama_lengkap, role = :role WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hashed_password);
        } else {
            $query = "UPDATE " . $this->table_name . " SET username = :username, nama_lengkap = :nama_lengkap, role = :role WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":nama_lengkap", $nama_lengkap);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
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
