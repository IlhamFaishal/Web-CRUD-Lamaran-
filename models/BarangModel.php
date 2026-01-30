<?php

class BarangModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        // Join with Kategori for display
        $sql = "SELECT b.*, k.nama_kategori 
                FROM barang b 
                LEFT JOIN kategori k ON b.kategori_id = k.id_kategori 
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

    public function findByKode($kode) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE kode_barang = :kode");
        $stmt->bindParam(':kode', $kode);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, satuan, harga_jual, stok, gambar) 
                VALUES (:kode, :nama, :kategori_id, :satuan, :harga, :stok, :gambar)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kode', $data['kode_barang']);
        $stmt->bindParam(':nama', $data['nama_barang']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
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
                satuan = :satuan,
                harga_jual = :harga,
                stok = :stok,
                gambar = :gambar
                WHERE id_barang = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kode', $data['kode_barang']);
        $stmt->bindParam(':nama', $data['nama_barang']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
        $stmt->bindParam(':satuan', $data['satuan']);
        $stmt->bindParam(':harga', $data['harga_jual']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindValue(':gambar', $data['gambar'] ?? null);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM barang WHERE id_barang = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getFiltered($keyword = '', $categoryId = null, $sort = '') {
        $sql = "SELECT b.*, k.nama_kategori 
                FROM barang b 
                LEFT JOIN kategori k ON b.kategori_id = k.id_kategori 
                WHERE 1=1";
        
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
        $sql = "SELECT * FROM barang 
                WHERE kode_barang LIKE :keyword OR nama_barang LIKE :keyword 
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $like = "%$keyword%";
        $stmt->bindParam(':keyword', $like);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
