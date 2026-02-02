<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();

$db->beginTransaction();

try {
    // 1. Get Item ID for 'Penghapus'
    $stmt = $db->query("SELECT id_barang FROM barang WHERE nama_barang LIKE '%penghapus%'");
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        throw new Exception("Item 'Penghapus' not found.");
    }
    
    $id = $item['id_barang'];
    echo "Found Item 'Penghapus' with ID: $id\n";
    
    // 2. Delete from Transaksi Detail (Sales)
    // First, find which Transaction IDs are affected to update totals later if needed
    $stmt = $db->prepare("SELECT DISTINCT transaksi_id FROM transaksi_detail WHERE barang_id = ?");
    $stmt->execute([$id]);
    $transaksiIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($transaksiIds)) {
        echo "Deleting sales details from " . count($transaksiIds) . " transactions...\n";
        
        // Delete Details
        $stmt = $db->prepare("DELETE FROM transaksi_detail WHERE barang_id = ?");
        $stmt->execute([$id]);
        
        // 3. Cleanup Parent Transaksi
        // If transaction has no more details, delete it. Else update total.
        foreach ($transaksiIds as $tid) {
            $stmt = $db->prepare("SELECT SUM(subtotal) FROM transaksi_detail WHERE transaksi_id = ?");
            $stmt->execute([$tid]);
            $newTotal = $stmt->fetchColumn();
            
            if ($newTotal == 0 || $newTotal === null) {
                // Empty transaction, delete
                $db->prepare("DELETE FROM transaksi WHERE id_transaksi = ?")->execute([$tid]);
                echo "  Deleted empty transaction #$tid\n";
            } else {
                // Update total
                $db->prepare("UPDATE transaksi SET total_harga = ? WHERE id_transaksi = ?")->execute([$newTotal, $tid]);
                echo "  Updated transaction #$tid total to $newTotal\n";
            }
        }
    } else {
        echo "No sales transactions found for this item.\n";
    }
    
    // 4. Delete from Pembelian (Purchases) just in case
    $stmt = $db->prepare("DELETE FROM pembelian WHERE barang_id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo "Deleted " . $stmt->rowCount() . " purchase records.\n";
    }
    
    $db->commit();
    echo "SUCCESS: Data for 'Penghapus' deleted.\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
