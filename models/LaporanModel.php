<?php

class LaporanModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getLaporan($filterType, $filterValue) {
        $sql = "SELECT * FROM transaksi WHERE 1=1 ";
        
        if ($filterType == 'harian') {
            $sql .= "AND DATE(tanggal) = :val"; // '2026-01-30'
        } elseif ($filterType == 'bulanan') {
            $sql .= "AND DATE_FORMAT(tanggal, '%Y-%m') = :val"; // '2026-01'
        } elseif ($filterType == 'tahunan') {
            $sql .= "AND DATE_FORMAT(tanggal, '%Y') = :val"; // '2026'
        }

        $sql .= " ORDER BY tanggal DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':val', $filterValue);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getChartData($filterType, $filterValue) {
        // Chart Data specific aggregation
        if ($filterType == 'harian') {
            // Per Jam
            $sql = "SELECT HOUR(tanggal) as label, SUM(total_harga) as total 
                    FROM transaksi 
                    WHERE DATE(tanggal) = :val 
                    GROUP BY HOUR(tanggal)";
        } elseif ($filterType == 'bulanan') {
            // Per Tanggal
            $sql = "SELECT DAY(tanggal) as label, SUM(total_harga) as total 
                    FROM transaksi 
                    WHERE DATE_FORMAT(tanggal, '%Y-%m') = :val 
                    GROUP BY DAY(tanggal)";
        } elseif ($filterType == 'tahunan') {
             // Per Bulan
             $sql = "SELECT MONTH(tanggal) as label, SUM(total_harga) as total 
                     FROM transaksi 
                     WHERE DATE_FORMAT(tanggal, '%Y') = :val 
                     GROUP BY MONTH(tanggal)";
        } else {
             return [];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':val', $filterValue);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
