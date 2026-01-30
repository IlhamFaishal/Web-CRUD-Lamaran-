<?php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pos_besi_kayu');


$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 


$scriptDir = rtrim($scriptDir, '/');

define('BASE_URL', $protocol . "://" . $host . $scriptDir);


define('ASSET_URL', str_replace('/public', '/assets', BASE_URL));

date_default_timezone_set('Asia/Jakarta');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
