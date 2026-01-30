<?php

require_once 'config.php';

// === KONEKSI DATABASE (SINGLETON PATTERN) ===
// Menggunakan teknik "Singleton" agar aplikasi tidak membuka-tutup koneksi berulang kali.
// Jadi koneksi hanya dibuat SEKALI, dan dipakai terus selama user browsing halaman itu.

class Database {
    private static $instance = null;
    private $conn;

    // Bagian setting ini otomatis mengambil dari config.php
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

    // Constructor ini private, supaya tidak ada yang bisa bikin "new Database()" sembarangan.
    private function __construct() {
        try {
            // Cara standar PHP terkoneksi ke MySQL menggunakan PDO (lebih aman dari SQL Injection)
            $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            
            // Setting agar kalau ada error di SQL, aplikasi lapor (Mode Exception)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Setting agar hasil query berupa Array Asosiatif (Nama Kolom => Nilai)
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }

    // Fungsi untuk memanggil koneksi database
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
