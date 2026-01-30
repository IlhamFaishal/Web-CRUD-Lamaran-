<?php

class TransaksiModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function generateNoTransaksi() {
        $today = date('Y');
        // Get last transaction of this year
        $sql = "SELECT no_transaksi FROM transaksi WHERE YEAR(tanggal) = :year ORDER BY id_transaksi DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':year', $today);
        $stmt->execute();
        $last = $stmt->fetch();

        if ($last) {
            $lastNo = $last['no_transaksi']; // TRX-2026-00001
            $sequence = intval(substr($lastNo, 9));
            $nextSequence = $sequence + 1;
        } else {
            $nextSequence = 1;
        }

        return 'TRX-' . $today . '-' . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            // 1. Insert Transaksi Header
            $sql = "INSERT INTO transaksi (no_transaksi, tanggal, total_harga, bayar, kembalian) 
                    VALUES (:no, NOW(), :total, :bayar, :kembalian)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':no', $data['no_transaksi']);
            $stmt->bindParam(':total', $data['total_harga']);
            $stmt->bindParam(':bayar', $data['bayar']);
            $stmt->bindParam(':kembalian', $data['kembalian']);
            $stmt->execute();
            $transaksiId = $this->db->lastInsertId();

            // 2. Insert Details & Update Stock
            foreach ($data['details'] as $item) {
                // Insert Detail
                $detailSql = "INSERT INTO transaksi_detail (transaksi_id, barang_id, qty, harga_satuan, subtotal) 
                              VALUES (:id, :barang_id, :qty, :harga, :subtotal)";
                $stmtDetail = $this->db->prepare($detailSql);
                $stmtDetail->bindParam(':id', $transaksiId);
                $stmtDetail->bindParam(':barang_id', $item['id']);
                $stmtDetail->bindParam(':qty', $item['qty']);
                $stmtDetail->bindParam(':harga', $item['price']);
                $stmtDetail->bindParam(':subtotal', $item['subtotal']);
                $stmtDetail->execute();

                // Update Stock
                // We assume stock validation happened in Controller
                $updateStock = "UPDATE barang SET stok = stok - :qty WHERE id_barang = :barang_id";
                $stmtStock = $this->db->prepare($updateStock);
                $stmtStock->bindParam(':qty', $item['qty']);
                $stmtStock->bindParam(':barang_id', $item['id']);
                $stmtStock->execute();
            }

            $this->db->commit();
            return $transaksiId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM transaksi WHERE id_transaksi = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getDetails($transaksiId) {
        $sql = "SELECT d.*, b.nama_barang, b.kode_barang 
                FROM transaksi_detail d 
                JOIN barang b ON d.barang_id = b.id_barang 
                WHERE d.transaksi_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $transaksiId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
