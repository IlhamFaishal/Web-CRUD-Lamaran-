<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();

echo "--- ALL SUPPLIERS ---\n";
$stmt = $db->query("SELECT * FROM kode_supplier");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($suppliers as $s) {
    echo "[{$s['id_kode_supplier']}] {$s['nama_supplier']} ({$s['kode_supplier']})\n";
}

echo "\n--- ALL PURCHASES (Limit 20) ---\n";
$stmt = $db->query("SELECT p.id_pembelian, p.supplier_id, s.nama_supplier, p.total_harga 
                    FROM pembelian p 
                    LEFT JOIN kode_supplier s ON p.supplier_id = s.id_kode_supplier
                    LIMIT 20");
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($purchases as $p) {
    echo "ID: {$p['id_pembelian']}, Supplier: [{$p['supplier_id']}] {$p['nama_supplier']}, Total: {$p['total_harga']}\n";
}
