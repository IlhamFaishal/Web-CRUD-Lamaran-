<?php

// ==============================
// SESSION & AUTH HELPER
// ==============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('auth/login');
    }
}

function currentUser() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    return null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ==============================
// NAVIGATION HELPER
// ==============================

function redirect($url) {
    if (strpos($url, 'http') !== 0 && strpos($url, '/') !== 0) {
        $url = BASE_URL . '/' . $url;
    }
    header("Location: " . $url);
    exit;
}

function back() {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: " . BASE_URL);
    }
    exit;
}

// ==============================
// UTILS & FORMATTING
// ==============================

function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(stripslashes(trim($data)));
}

function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function formatRupiah($angka) {
    if (abs($angka) < 1) $angka = 0; 
    return "Rp " . number_format($angka, 0, ',', '.');
}

function view($view, $data = []) {
    extract($data);
    $viewPath = __DIR__ . '/../views/' . $view . '.php';
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View $view not found at $viewPath");
    }
}

// --- 1. Fungsi Umum & Debugging ---

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die(); // Matikan proses agar fokus lihat data
}

// ==============================
// VALIDATION & FLASH MESSAGE
// ==============================

function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " wajib diisi.";
        }
    }
    return $errors;
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
