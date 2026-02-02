<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();

echo "--- SEARCH PRODUCT 'PENGHAPUS' ---\n";
$stmt = $db->query("SELECT * FROM barang WHERE nama_barang LIKE '%penghapus%' OR nama_barang LIKE '%hapus%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "No item found.\n";
} else {
    foreach ($rows as $r) {
        echo "Item: [{$r['id_barang']}] {$r['nama_barang']} - Supplier: {$r['supplier_id']}\n";
        
        // Cek Pembelian
        $stmt2 = $db->prepare("SELECT * FROM pembelian WHERE barang_id = ?");
        $stmt2->execute([$r['id_barang']]);
        $beli = $stmt2->fetchAll();
        echo "  Purchases (Pembelian): " . count($beli) . " records\n";
        foreach($beli as $b) {
             echo "    - ID: {$b['id_pembelian']}, Supplier: {$b['supplier_id']}, Total: {$b['total_harga']}\n";
        }

        // Cek Transaksi (Penjualan)
        $stmt3 = $db->prepare("SELECT d.*, t.tanggal FROM transaksi_detail d JOIN transaksi t ON d.transaksi_id = t.id_transaksi WHERE barang_id = ?");
        $stmt3->execute([$r['id_barang']]);
        $jual = $stmt3->fetchAll();
        echo "  Sales (Penjualan): " . count($jual) . " records\n";
    }
}
