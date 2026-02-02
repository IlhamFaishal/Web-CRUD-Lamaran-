<?php

class PembayaranModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO pembayaran_hutang (tanggal, supplier_id, jumlah_bayar, keterangan) 
                VALUES (:tanggal, :sid, :jumlah, :ket)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tanggal', $data['tanggal']);
        $stmt->bindValue(':sid', $data['supplier_id']);
        $stmt->bindValue(':jumlah', $data['jumlah_bayar']);
        $stmt->bindValue(':ket', $data['keterangan'] ?? '');
        return $stmt->execute();
    }

    public function getBySupplierPeriod($supplierId, $startDate, $endDate) {
        $sql = "SELECT * FROM pembayaran_hutang 
                WHERE supplier_id = :sid AND tanggal BETWEEN :start AND :end
                ORDER BY tanggal ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $supplierId);
        $stmt->bindValue(':start', $startDate);
        $stmt->bindValue(':end', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getTotalBySupplierPeriod($supplierId, $startDate, $endDate) {
         $sql = "SELECT SUM(jumlah_bayar) as total FROM pembayaran_hutang 
                WHERE supplier_id = :sid AND tanggal BETWEEN :start AND :end";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $supplierId);
        $stmt->bindValue(':start', $startDate);
        $stmt->bindValue(':end', $endDate);
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }
}
