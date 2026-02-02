<?php

require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn;

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

   
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            
          
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
           
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
