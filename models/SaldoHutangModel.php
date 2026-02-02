<?php

class SaldoHutangModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getSaldoAwal($supplierId, $tahun) {
        $stmt = $this->db->prepare("SELECT saldo_awal FROM saldo_awal_hutang WHERE supplier_id = :sid AND tahun = :tahun");
        $stmt->bindValue(':sid', $supplierId);
        $stmt->bindValue(':tahun', $tahun);
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }

    public function setSaldoAwal($supplierId, $tahun, $saldo) {
        // Check if exists
        $current = $this->getSaldoAwal($supplierId, $tahun);
        
        // Use UPSERT logic or Check-then-Insert/Update
        $sql = "INSERT INTO saldo_awal_hutang (supplier_id, tahun, saldo_awal) 
                VALUES (:sid, :tahun, :saldo)
                ON DUPLICATE KEY UPDATE saldo_awal = :saldo_update";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $supplierId);
        $stmt->bindValue(':tahun', $tahun);
        $stmt->bindValue(':saldo', $saldo);
        $stmt->bindValue(':saldo_update', $saldo);
        return $stmt->execute();
    }
}
