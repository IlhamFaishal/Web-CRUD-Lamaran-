<?php

require_once __DIR__ . '/../models/LaporanModel.php';

class LaporanController {
    private $model;

    public function __construct() {
        requireLogin();
        $this->model = new LaporanModel();
    }

    public function index() {
        $filterType = isset($_GET['type']) ? $_GET['type'] : 'harian';
        
        if ($filterType == 'harian') {
            $filterValue = isset($_GET['value']) ? $_GET['value'] : date('Y-m-d');
        } elseif ($filterType == 'bulanan') {
            $filterValue = isset($_GET['value']) ? $_GET['value'] : date('Y-m');
        } elseif ($filterType == 'tahunan') {
            $filterValue = isset($_GET['value']) ? $_GET['value'] : date('Y');
        } else {
            $filterValue = date('Y-m-d');
        }

        $laporan = $this->model->getLaporan($filterType, $filterValue);
        $chartData = $this->model->getChartData($filterType, $filterValue);

        // Prepare Chart JS Format
        $labels = [];
        $values = [];
        
        // Populate defaults based on type
        // This is a simple implementation, ideally we fill missing gaps (e.g. valid hours/days)
        // For simplicity: just map existing data
        
        foreach ($chartData as $row) {
            $labels[] = $row['label'];
            $values[] = $row['total'];
        }

        // Stats
        $totalOmzet = 0;
        $totalTransaksi = count($laporan);
        foreach ($laporan as $row) {
            $totalOmzet += $row['total_harga'];
        }

        view('laporan/index', [
            'laporan' => $laporan,
            'labels' => json_encode($labels),
            'values' => json_encode($values),
            'filterType' => $filterType,
            'filterValue' => $filterValue,
            'totalOmzet' => $totalOmzet,
            'totalTransaksi' => $totalTransaksi
        ]);
    }
}
