<?php

class KodeSupplier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all kode supplier
    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kode_supplier ORDER BY nama_supplier ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }
    
    // Get kode supplier by ID
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kode_supplier WHERE id_kode_supplier = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }
    
    // Get kode supplier by kode
    public function getByKode($kode) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kode_supplier WHERE kode_supplier = ?");
            $stmt->execute([$kode]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error in getByKode: " . $e->getMessage());
            return null;
        }
    }
    
    // Create new kode supplier
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO kode_supplier (kode_supplier, nama_supplier, alamat, telepon, email, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $data['kode_supplier'],
                $data['nama_supplier'],
                $data['alamat'] ?? null,
                $data['telepon'] ?? null,
                $data['email'] ?? null,
                $data['keterangan'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Error in create: " . $e->getMessage());
            return false;
        }
    }
    
    // Update kode supplier
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE kode_supplier 
                SET kode_supplier = ?, nama_supplier = ?, alamat = ?, telepon = ?, email = ?, keterangan = ?
                WHERE id_kode_supplier = ?
            ");
            
            return $stmt->execute([
                $data['kode_supplier'],
                $data['nama_supplier'],
                $data['alamat'] ?? null,
                $data['telepon'] ?? null,
                $data['email'] ?? null,
                $data['keterangan'] ?? null,
                $id
            ]);
        } catch(PDOException $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete kode supplier
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM kode_supplier WHERE id_kode_supplier = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if kode exists (for validation)
    public function kodeExists($kode, $excludeId = null) {
        try {
            if ($excludeId) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM kode_supplier WHERE kode_supplier = ? AND id_kode_supplier != ?");
                $stmt->execute([$kode, $excludeId]);
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM kode_supplier WHERE kode_supplier = ?");
                $stmt->execute([$kode]);
            }
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error in kodeExists: " . $e->getMessage());
            return false;
        }
    }
    
    // Search kode supplier
    public function search($keyword) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM kode_supplier 
                WHERE kode_supplier LIKE ? OR nama_supplier LIKE ? OR telepon LIKE ?
                ORDER BY created_at DESC
            ");
            $searchTerm = "%{$keyword}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in search: " . $e->getMessage());
            return [];
        }
    }
}
