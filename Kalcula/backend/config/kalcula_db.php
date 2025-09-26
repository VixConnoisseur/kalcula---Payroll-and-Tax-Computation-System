<?php
class KalculaDB {
    
    private $host = "localhost";    // XAMPP default
    private $db_name = "kalculadbs"; // Your database name
    private $username = "root";     // XAMPP default user
    private $password = "";         // XAMPP default has no password
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $exception) {
            die("Database connection error: " . $exception->getMessage());
        }

        return $this->conn; // This line fixes the original error!
    }
}