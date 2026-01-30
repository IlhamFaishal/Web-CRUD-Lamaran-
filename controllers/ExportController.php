<?php

require_once __DIR__ . '/../models/LaporanModel.php';

class ExportController {
    private $model;

    public function __construct() {
        requireLogin();
        $this->model = new LaporanModel();
    }

    public function download() {
        $type = $_GET['export_type']; // csv, excel, pdf
        $filterType = $_GET['type'];
        $filterValue = $_GET['value'];

        $data = $this->model->getLaporan($filterType, $filterValue);
        $filename = "Laporan_" . $filterType . "_" . $filterValue;

        if ($type == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['No Transaksi', 'Tanggal', 'Total', 'Bayar', 'Kembalian']);
            
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['no_transaksi'],
                    $row['tanggal'],
                    $row['total_harga'],
                    $row['bayar'],
                    $row['kembalian']
                ]);
            }
            fclose($output);
            exit;
        } elseif ($type == 'excel') {
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename.xls");
            
            echo "<table border='1'>";
            echo "<tr><th>No Transaksi</th><th>Tanggal</th><th>Total</th><th>Bayar</th><th>Kembalian</th></tr>";
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>" . $row['no_transaksi'] . "</td>";
                echo "<td>" . $row['tanggal'] . "</td>";
                echo "<td>" . $row['total_harga'] . "</td>";
                echo "<td>" . $row['bayar'] . "</td>";
                echo "<td>" . $row['kembalian'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit;
        } elseif ($type == 'pdf') {
            // For PDF, we render a print friendly page that triggers print or can be saved
            echo "<html><body onload='window.print()'>";
            echo "<h2>Laporan $filterType : $filterValue</h2>";
            echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'>";
            echo "<thead><tr><th>No Transaksi</th><th>Tanggal</th><th>Total</th><th>Bayar</th><th>Kembali</th></tr></thead><tbody>";
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>" . $row['no_transaksi'] . "</td>";
                echo "<td>" . $row['tanggal'] . "</td>";
                echo "<td>Rp " . number_format($row['total_harga'],0,',','.') . "</td>";
                echo "<td>Rp " . number_format($row['bayar'],0,',','.') . "</td>";
                echo "<td>Rp " . number_format($row['kembalian'],0,',','.') . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table></body></html>";
            exit;
        }
    }
}
