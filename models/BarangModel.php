<?php

class BarangModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        // Join with Kategori and Supplier for display
        $sql = "SELECT b.*, k.nama_kategori, s.nama_supplier, s.kode_supplier 
                FROM barang b 
                LEFT JOIN kategori k ON b.kategori_id = k.id_kategori 
                LEFT JOIN kode_supplier s ON b.supplier_id = s.id_kode_supplier
                WHERE b.is_active = 1
                ORDER BY b.id_barang DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id_barang = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Find specific item by Code AND Supplier (for unique check)
    public function findByKodeAndSupplier($kode, $supplierId) {
        $sql = "SELECT * FROM barang WHERE kode_barang = :kode";
        if ($supplierId) {
            $sql .= " AND (supplier_id = :sid OR supplier_id IS NULL)"; 
            // Better: strict equality. If supplier NULL (general item), check NULL.
            // But form always sends supplier_id.
            $sql = "SELECT * FROM barang WHERE kode_barang = :kode AND supplier_id = :sid";
        } else {
            // If no supplier specified, maybe searching for item with NO supplier?
            $sql = "SELECT * FROM barang WHERE kode_barang = :kode AND supplier_id IS NULL";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kode', $kode);
        if ($supplierId) {
            $stmt->bindParam(':sid', $supplierId);
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByKode($kode) {
        // Now findByKode might return multiple items.
        // POS search uses LIKE, that's fine.
        // This exact match is used by validation.
        // We keep it as "Return ANY item with this code" ? 
        // No, let's keep it but for the Controller we will switch to findByKodeAndSupplier.
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE kode_barang = :kode");
        $stmt->bindParam(':kode', $kode);
        $stmt->execute();
        return $stmt->fetch(); // Returns first match
    }

    // Get last code by prefix for auto-numbering
    public function getLastCodeByPrefix($prefix) {
        // Cari kode barang yang diawali dengan prefix
        // Order by length dulu (biar A10 > A2), lalu string
        // Atau lebih aman: Ambil semua yang like prefix% lalu sorting php, atau regex di sql mysql 8
        // Simpelnya: Order by kode_barang DESC limit 1
        $param = $prefix . "%";
        $stmt = $this->db->prepare("SELECT kode_barang FROM barang WHERE kode_barang LIKE :prefix ORDER BY LENGTH(kode_barang) DESC, kode_barang DESC LIMIT 1");
        $stmt->bindParam(':prefix', $param);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, satuan, harga_jual, stok, gambar) 
                VALUES (:kode, :nama, :kategori_id, :supplier_id, :satuan, :harga, :stok, :gambar)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kode', $data['kode_barang']);
        $stmt->bindParam(':nama', $data['nama_barang']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
        $stmt->bindValue(':supplier_id', $data['supplier_id'] ?? null);
        $stmt->bindParam(':satuan', $data['satuan']);
        $stmt->bindParam(':harga', $data['harga_jual']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindValue(':gambar', $data['gambar'] ?? null);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $sql = "UPDATE barang SET 
                kode_barang = :kode,
                nama_barang = :nama,
                kategori_id = :kategori_id,
                supplier_id = :supplier_id,
                satuan = :satuan,
                harga_jual = :harga,
                stok = :stok,
                gambar = :gambar
                WHERE id_barang = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kode', $data['kode_barang']);
        $stmt->bindParam(':nama', $data['nama_barang']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
        $stmt->bindValue(':supplier_id', $data['supplier_id'] ?? null);
        $stmt->bindParam(':satuan', $data['satuan']);
        $stmt->bindParam(':harga', $data['harga_jual']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindValue(':gambar', $data['gambar'] ?? null);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE barang SET is_active = 0 WHERE id_barang = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getFiltered($keyword = '', $categoryId = null, $sort = '') {
        $sql = "SELECT b.*, k.nama_kategori, s.nama_supplier 
                FROM barang b 
                LEFT JOIN kategori k ON b.kategori_id = k.id_kategori 
                LEFT JOIN kode_supplier s ON b.supplier_id = s.id_kode_supplier
                WHERE b.is_active = 1";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (b.kode_barang LIKE :keyword OR b.nama_barang LIKE :keyword)";
            $params[':keyword'] = "%$keyword%";
        }

        if (!empty($categoryId)) {
            $sql .= " AND b.kategori_id = :cat_id";
            $params[':cat_id'] = $categoryId;
        }

        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY b.harga_jual ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY b.harga_jual DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY b.nama_barang ASC";
                break;
            default:
                $sql .= " ORDER BY b.id_barang DESC";
                break;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Search for POS
    public function search($keyword) {
        $sql = "SELECT b.*, s.nama_supplier 
                FROM barang b 
                LEFT JOIN kode_supplier s ON b.supplier_id = s.id_kode_supplier
                WHERE b.is_active = 1 AND (b.kode_barang LIKE :keyword OR b.nama_barang LIKE :keyword) 
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $like = "%$keyword%";
        $stmt->bindParam(':keyword', $like);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
