<?php

class PembelianModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO pembelian (no_faktur, tanggal, supplier_id, barang_id, qty, harga_satuan, dpp, ppn, total_harga) 
                VALUES (:faktur, :tanggal, :supplier_id, :barang_id, :qty, :harga, :dpp, :ppn, :total)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':faktur', $data['no_faktur']);
        $stmt->bindValue(':tanggal', $data['tanggal']);
        $stmt->bindValue(':supplier_id', $data['supplier_id']);
        $stmt->bindValue(':barang_id', $data['barang_id']);
        $stmt->bindValue(':qty', $data['qty']);
        $stmt->bindValue(':harga', $data['harga_satuan']);
        $stmt->bindValue(':dpp', $data['dpp']);
        $stmt->bindValue(':ppn', $data['ppn']);
        $stmt->bindValue(':total', $data['total_harga']);
        
        $this->db->beginTransaction();
        try {
            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Get purchases by supplier and date range
    public function getBySupplierPeriod($supplierId, $startDate, $endDate) {
        $sql = "SELECT p.*, b.nama_barang, b.kode_barang, b.stok 
                FROM pembelian p
                JOIN barang b ON p.barang_id = b.id_barang
                WHERE p.supplier_id = :sid AND p.tanggal BETWEEN :start AND :end
                ORDER BY p.tanggal ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $supplierId);
        $stmt->bindValue(':start', $startDate);
        $stmt->bindValue(':end', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get Total Purchase per Supplier in Period
    public function getRekapByPeriod($startDate, $endDate) {
        $sql = "SELECT p.supplier_id, s.nama_supplier, 
                       SUM(p.dpp) as total_dpp, 
                       SUM(p.ppn) as total_ppn, 
                       SUM(p.total_harga) as total_pembelian
                FROM pembelian p
                JOIN kode_supplier s ON p.supplier_id = s.id_kode_supplier
                WHERE p.tanggal BETWEEN :start AND :end
                GROUP BY p.supplier_id, s.nama_supplier";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':start', $startDate);
        $stmt->bindValue(':end', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pembelian WHERE id_pembelian = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $old = $this->findById($id);
        if (!$old) return false;

        $this->db->beginTransaction();
        try {
            // 2. Update Purchase Record
            $sql = "UPDATE pembelian SET 
                    tanggal = :tanggal, 
                    supplier_id = :supplier_id, 
                    barang_id = :barang_id, 
                    qty = :qty, 
                    harga_satuan = :harga, 
                    dpp = :dpp, 
                    ppn = :ppn, 
                    total_harga = :total 
                    WHERE id_pembelian = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':tanggal', $data['tanggal']);
            $stmt->bindValue(':supplier_id', $data['supplier_id']);
            $stmt->bindValue(':barang_id', $data['barang_id']);
            $stmt->bindValue(':qty', $data['qty']);
            $stmt->bindValue(':harga', $data['harga_satuan']);
            $stmt->bindValue(':dpp', $data['dpp']);
            $stmt->bindValue(':ppn', $data['ppn']);
            $stmt->bindValue(':total', $data['total_harga']);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        $old = $this->findById($id);
        if (!$old) return false;

        $this->db->beginTransaction();
        try {
            // 2. Delete Record
            $stmt = $this->db->prepare("DELETE FROM pembelian WHERE id_pembelian = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
