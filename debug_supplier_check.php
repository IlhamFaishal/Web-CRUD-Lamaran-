<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();

echo "Finding Supplier...\n";
$stmt = $db->query("SELECT * FROM kode_supplier WHERE nama_supplier LIKE '%supplier 4%' OR nama_supplier LIKE '%penghapus%'");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($suppliers)) {
    echo "No supplier found matching 'supplier 4' or 'penghapus'\n";
    exit;
}

foreach ($suppliers as $s) {
    echo "Found Supplier: [{$s['id_kode_supplier']}] {$s['nama_supplier']} ({$s['kode_supplier']})\n";
    
    // Check transactions
    $stmt2 = $db->prepare("SELECT COUNT(*) FROM pembelian WHERE supplier_id = ?");
    $stmt2->execute([$s['id_kode_supplier']]);
    $count = $stmt2->fetchColumn();
    echo "  -> Has $count transactions (pembelian)\n";
    
    // Check details
    $stmt3 = $db->prepare("SELECT id_pembelian, tanggal, total_harga FROM pembelian WHERE supplier_id = ? LIMIT 5");
    $stmt3->execute([$s['id_kode_supplier']]);
    $rows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r) {
        echo "     - ID: {$r['id_pembelian']}, Date: {$r['tanggal']}, Total: {$r['total_harga']}\n";
    }
}
