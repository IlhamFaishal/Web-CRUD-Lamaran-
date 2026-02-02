<?php

class MasterKodeBarang {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all master kode barang
    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM master_kode_barang ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }
    
    // Get master kode barang by ID
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM master_kode_barang WHERE id_master_kode = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }
    
    // Get by prefix
    public function getByPrefix($prefix) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM master_kode_barang WHERE kode_prefix = ?");
            $stmt->execute([$prefix]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error in getByPrefix: " . $e->getMessage());
            return null;
        }
    }
    
    // Create new master kode barang
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO master_kode_barang (kode_prefix, nama_prefix, deskripsi) 
                VALUES (?, ?, ?)
            ");
            
            return $stmt->execute([
                strtoupper($data['kode_prefix']),
                $data['nama_prefix'],
                $data['deskripsi'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Error in create: " . $e->getMessage());
            return false;
        }
    }
    
    // Update master kode barang
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE master_kode_barang 
                SET kode_prefix = ?, nama_prefix = ?, deskripsi = ?
                WHERE id_master_kode = ?
            ");
            
            return $stmt->execute([
                strtoupper($data['kode_prefix']),
                $data['nama_prefix'],
                $data['deskripsi'] ?? null,
                $id
            ]);
        } catch(PDOException $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete master kode barang
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM master_kode_barang WHERE id_master_kode = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if prefix exists (for validation)
    public function prefixExists($prefix, $excludeId = null) {
        try {
            if ($excludeId) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM master_kode_barang WHERE kode_prefix = ? AND id_master_kode != ?");
                $stmt->execute([strtoupper($prefix), $excludeId]);
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM master_kode_barang WHERE kode_prefix = ?");
                $stmt->execute([strtoupper($prefix)]);
            }
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error in prefixExists: " . $e->getMessage());
            return false;
        }
    }
    
    // Search master kode barang
    public function search($keyword) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM master_kode_barang 
                WHERE kode_prefix LIKE ? OR nama_prefix LIKE ?
                ORDER BY created_at DESC
            ");
            $searchTerm = "%{$keyword}%";
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in search: " . $e->getMessage());
            return [];
        }
    }
}
