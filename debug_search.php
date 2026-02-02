<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();

echo "--- SEARCH BY AMOUNT (approx 24975) ---\n";
// Check PEMBELIAN
$stmt = $db->query("SELECT * FROM pembelian WHERE total_harga > 20000");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "In 'pembelian' table:\n";
foreach ($rows as $r) {
    echo "  ID: {$r['id_pembelian']}, Supplier: {$r['supplier_id']}, Total: {$r['total_harga']}\n";
}

// Check TRANSAKSI
$stmt = $db->query("SELECT * FROM transaksi WHERE total_harga > 20000");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nIn 'transaksi' table:\n";
foreach ($rows as $r) {
    echo "  ID: {$r['id_transaksi']}, Total: {$r['total_harga']}\n";
}
