<?php
if (!class_exists('PDO')) {
    echo "Missing PHP PDO extension. Install and enable PDO (e.g. php-mysqlnd / php-mysql).";
    exit;
}

class Database {
    private $host = "localhost";
    private $db_name = "portfolio";
    private $username = "root";
    private $password = "1234567890";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>