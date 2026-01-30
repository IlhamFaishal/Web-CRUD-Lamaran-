<?php

// === SIMPLE ROUTER LOGIC ===
// Ambil URL yang diminta user. Jika kosong, arahkan ke halaman login.
// Contoh: website.com/barang/create -> url = barang/create
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'auth/login';

// Bersihkan URL dari karakter aneh untuk keamanan
$url = filter_var($url, FILTER_SANITIZE_URL);

// Pecah URL menjadi bagian-bagian.
// [0] => Controller (Misal: barang)
// [1] => Method/Fungsi (Misal: create)
// [2] => Parameter (Misal: ID barang)
$urlParts = explode('/', $url);

// Tentukan nama Controller (Huruf depan besar + 'Controller')
// Misal: barang -> BarangController
$controllerName = ucfirst($urlParts[0]) . 'Controller';

// Tentukan nama Method/Fungsi. Defaultnya adalah 'index' jika tidak ada.
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';

// Sisa URL dianggap sebagai parameter (misal ID untuk edit/hapus)
$params = array_slice($urlParts, 2);

// Khusus: Jika user mengakses root (kosong), arahkan ke Login.
if ($urlParts[0] == '') {
    $controllerName = 'AuthController';
    $methodName = 'login';
}

// Cek apakah file Controller yang diminta ada?
$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName;
    
    // Cek apakah fungsi yang diminta ada di dalam Controller itu?
    if (method_exists($controller, $methodName)) {
        // Panggil fungsinya dan kirim parameter jika ada
        call_user_func_array([$controller, $methodName], $params);
    } else {
        die("Method $methodName tidak ditemukan di controller $controllerName");
    }
} else {
    // Jika Controller tidak ditemukan (404)
    die("404 - Halaman tidak ditemukan. (Controller: $controllerName)");
}
