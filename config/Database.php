<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
class Database {
    private $host = '127.0.0.1';
    private $port = '3306';
    private $db_name = 'db_rental_ayu';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

            // Auto-check if table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'user'");
            if ($stmt->rowCount() == 0) {
                $this->initializeDatabase();
            }
            
            // Apply role schema and seeders dynamically
            $this->runMigrations();
        } catch(PDOException $exception) {
            // Code 1049 is "Unknown database"
            if ($exception->getCode() == 1049 || strpos($exception->getMessage(), 'Unknown database') !== false) {
                $this->initializeDatabase();
                $this->runMigrations();
            } else {
                die("Connection error: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }

    private function runMigrations() {
        try {
            // Verify if column 'role' exists in 'user' table
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM user LIKE 'role'");
            if ($checkColumn->rowCount() === 0) {
                $this->conn->exec("ALTER TABLE user ADD COLUMN role ENUM('Admin', 'Operator') NOT NULL DEFAULT 'Admin'");
            }

            // Seed admin account if missing
            $stmtAdmin = $this->conn->prepare("SELECT * FROM user WHERE username = 'admin'");
            $stmtAdmin->execute();
            if ($stmtAdmin->rowCount() === 0) {
                $adminPass = password_hash('admin', PASSWORD_BCRYPT);
                $stmtInsert = $this->conn->prepare("INSERT INTO user (username, password, nama_lengkap, role) VALUES ('admin', :pass, 'AM Admin', 'Admin')");
                $stmtInsert->execute([':pass' => $adminPass]);
            } else {
                $this->conn->exec("UPDATE user SET role = 'Admin' WHERE username = 'admin'");
            }

            // Seed operator account if missing
            $stmtOp = $this->conn->prepare("SELECT * FROM user WHERE username = 'operator'");
            $stmtOp->execute();
            if ($stmtOp->rowCount() === 0) {
                $opPass = password_hash('operator', PASSWORD_BCRYPT);
                $stmtInsert = $this->conn->prepare("INSERT INTO user (username, password, nama_lengkap, role) VALUES ('operator', :pass, 'AM Operator', 'Operator')");
                $stmtInsert->execute([':pass' => $opPass]);
            }
        } catch (PDOException $e) {
            // Suppress error or handle gracefully
        }
    }

    private function initializeDatabase() {
        try {
            $baseConn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->username, $this->password);
            $baseConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $baseConn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);

            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

            $sqlPath = dirname(__DIR__) . '/db_rental.sql';
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            die("Database initialization failed: " . $e->getMessage());
        }
    }

    public function getBaseConnection() {
        try {
            $conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $exception) {
            die("Connection error: " . $exception->getMessage());
        }
    }
}
