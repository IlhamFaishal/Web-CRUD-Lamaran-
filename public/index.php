<?php
// === ENTRY POINT APLIKASI ===
// Semua request dari user akan masuk lewat file ini pertama kali.
// File ini bertugas sebagai "Resepsionis" yang mengarahkan user ke Controller yang tepat.

session_start(); // Memulai sesi untuk login user

// 1. Load semua file konfigurasi yang dibutuhkan
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// 2. Load Helpers (Kumpulan fungsi bantuan agar kodingan lebih rapi)
require_once __DIR__ . '/../helpers/functions.php';

// 3. Load Router Logic (Logika penentu arah)
require_once __DIR__ . '/../routes/web.php';

// Catatan:
// Logika routing ada di file routes/web.php agar file index ini tetap bersih.
