<?php

require_once __DIR__ . '/../models/LaporanModel.php'; 
require_once __DIR__ . '/../models/PembelianModel.php';
require_once __DIR__ . '/../models/SaldoHutangModel.php';
require_once __DIR__ . '/../models/PembayaranModel.php';
require_once __DIR__ . '/../models/KodeSupplier.php';
require_once __DIR__ . '/../models/BarangModel.php';

class LaporanController {
    private $pembelianModel;
    private $saldoModel;
    private $pembayaranModel;
    private $supplierModel;
    private $barangModel;

    public function __construct() {
        requireLogin();
        $this->pembelianModel = new PembelianModel();
        $this->saldoModel = new SaldoHutangModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->supplierModel = new KodeSupplier();
        $this->barangModel = new BarangModel();
    }

    public function index() {
        // Fetch Items for Test Input
        $barangList = $this->barangModel->getAll();
        $allSuppliers = $this->supplierModel->getAll();
        
        // Check if filter was submitted
        $filterSubmitted = isset($_GET['filter_submit']);
        
        // Handle period filter for rekapitulasi
        $period = isset($_GET['period']) ? $_GET['period'] : '';
        if ($period) {
            switch ($period) {
                case 'today':
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d');
                    break;
                case '7days':
                    $startDate = date('Y-m-d', strtotime('-7 days'));
                    $endDate = date('Y-m-d');
                    break;
                case '30days':
                    $startDate = date('Y-m-d', strtotime('-30 days'));
                    $endDate = date('Y-m-d');
                    break;
                default:
                    $startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
                    $endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');
            }
        } else {
            // Initialize variables
            $startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
            $endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');
        }
        
        $reportData = [];
        $selectedSupplier = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';
        
        $totalSaldoAwal = 0;
        $totalDPP = 0;
        $totalPPN = 0;
        $totalBeli = 0;
        $totalBayar = 0;
        $totalSaldoAkhir = 0;
        
        // Only process data if filter was submitted
        if ($filterSubmitted) {
            $year = date('Y', strtotime($startDate));
            
            // Filter Supplier
            if ($selectedSupplier && $selectedSupplier != 'all') {
                $suppliers = array_filter($allSuppliers, function($s) use ($selectedSupplier) {
                    return $s['id_kode_supplier'] == $selectedSupplier;
                });
            } else {
                $suppliers = $allSuppliers;
            }
            
            foreach ($suppliers as $s) {
                $sid = $s['id_kode_supplier'];
                
                // 1. Get Saldo Awal Tahun Ini (Input Manual)
                $saldoTahun = $this->saldoModel->getSaldoAwal($sid, $year);
                
                // 2. Hitung Mutasi dari Awal Tahun s/d Sebelum StartDate
                // Logic: Saldo = Awal - Pembelian + Pembayaran (Hutang = Negatif)
                $startOfYear = "$year-01-01";
                
                $prevPurchases = $this->pembelianModel->getBySupplierPeriod($sid, $startOfYear, date('Y-m-d', strtotime($startDate . ' -1 day')));
                $prevPaymentsTotal = $this->pembayaranModel->getTotalBySupplierPeriod($sid, $startOfYear, date('Y-m-d', strtotime($startDate . ' -1 day')));
                
                $sumPrevPurchase = 0;
                foreach ($prevPurchases as $p) $sumPrevPurchase += $p['total_harga'];
                
                // Balance Logic: Credit (Purchase) decreases balance, Debit (Payment) increases balance
                $currentSaldoAwal = $saldoTahun - $sumPrevPurchase + $prevPaymentsTotal;
                
                // 3. Activity in Period
                $periodPurchases = $this->pembelianModel->getBySupplierPeriod($sid, $startDate, $endDate);
                $periodPayments = $this->pembayaranModel->getBySupplierPeriod($sid, $startDate, $endDate);
                
                // Merge and Sort
                $history = [];
                foreach ($periodPurchases as $p) {
                    $p['type'] = 'purchase';
                    $p['sort_created'] = isset($p['created_at']) ? strtotime($p['created_at']) : 0;
                    $p['sort_id'] = $p['id_pembelian'];
                    $history[] = $p;
                }
                foreach ($periodPayments as $pay) {
                    $pay['type'] = 'payment';
                    $pay['sort_created'] = isset($pay['created_at']) ? strtotime($pay['created_at']) : 0;
                    $pay['sort_id'] = $pay['id_bayar']; // Note: distinct ID space, but useful for stable sort within same type
                    $history[] = $pay;
                }
                
                // Sort by Tanggal ASC, then Created At ASC, then ID ASC
                usort($history, function($a, $b) {
                    // 1. Primary: Tanggal (Transaction Date)
                    $t1 = strtotime($a['tanggal']);
                    $t2 = strtotime($b['tanggal']);
                    if ($t1 != $t2) {
                        return $t1 - $t2;
                    }

                    // 2. Secondary: Created At (Entry Time)
                    if ($a['sort_created'] != $b['sort_created']) {
                        return $a['sort_created'] - $b['sort_created'];
                    }

                    // 3. Tertiary: ID (Insertion Order - Fallback)
                    // Note: Comparing ID across types (Purchase vs Payment) isn't perfect but provides stability
                    return $a['sort_id'] - $b['sort_id'];
                });

                $sumDPP = 0;
                $sumPPN = 0;
                $sumTotalBeli = 0;
                $periodPaymentTotal = 0;
                
                foreach ($periodPurchases as $p) {
                    $sumDPP += $p['dpp'];
                    $sumPPN += $p['ppn'];
                    $sumTotalBeli += $p['total_harga'];
                }
                foreach ($periodPayments as $pay) {
                    $periodPaymentTotal += $pay['jumlah_bayar'];
                }
                
                $saldoAkhir = $currentSaldoAwal - $sumTotalBeli + $periodPaymentTotal;
                
                // Accumulate Global Totals
                $totalSaldoAwal += $currentSaldoAwal;
                $totalDPP += $sumDPP;
                $totalPPN += $sumPPN;
                $totalBeli += $sumTotalBeli;
                $totalBayar += $periodPaymentTotal;
                $totalSaldoAkhir += $saldoAkhir;
                
                $reportData[] = [
                    'supplier' => $s,
                    'saldo_awal' => $currentSaldoAwal,
                    'history' => $history, // Changed from 'purchases' to 'history'
                    'purchases' => $periodPurchases, // Keep for compatibility if needed elsewhere
                    'total_dpp' => $sumDPP,
                    'total_ppn' => $sumPPN,
                    'total_beli' => $sumTotalBeli,
                    'total_bayar' => $periodPaymentTotal,
                    'saldo_akhir' => $saldoAkhir
                ];
            }
        }

        view('laporan/index', [
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedSupplier' => $selectedSupplier,
            'allSuppliers' => $allSuppliers,
            'barangList' => $barangList,
            'filterSubmitted' => $filterSubmitted,
            'totals' => [
                'saldo_awal' => $totalSaldoAwal,
                'dpp' => $totalDPP,
                'ppn' => $totalPPN,
                'beli' => $totalBeli,
                'bayar' => $totalBayar,
                'saldo_akhir' => $totalSaldoAkhir
            ]
        ]);
    }
    
    public function setSaldo() {
        if (isPost()) {
            $supplierId = $_POST['supplier_id'];
            $tahun = $_POST['tahun'];
            $saldo = $_POST['saldo_awal'];
            
            if ($this->saldoModel->setSaldoAwal($supplierId, $tahun, $saldo)) {
                setFlashMessage('success', 'Saldo awal berhasil disimpan.');
            } else {
                setFlashMessage('error', 'Gagal simpan saldo.');
            }
        }
        redirect('laporan');
    }

    // Unified Create or Update Purchase
    public function debit() {
        if (isPost()) {
            $qty = (int) $_POST['qty'];
            $hargaSatuan = (float) $_POST['harga_satuan'];
            
            $dpp = $qty * $hargaSatuan; 
            $ppn = $dpp * 0.11;
            $total = $dpp + $ppn;
            
            $data = [
                'no_faktur' => 'INV-' . time(),
                'tanggal' => $_POST['tanggal'],
                'supplier_id' => $_POST['supplier_id'],
                'barang_id' => $_POST['barang_id'],
                'qty' => $qty,
                'harga_satuan' => $hargaSatuan, 
                'dpp' => $dpp,
                'ppn' => $ppn,
                'total_harga' => $total
            ];

            if (!empty($_POST['id_pembelian'])) {
                // Update
                if ($this->pembelianModel->update($_POST['id_pembelian'], $data)) {
                    setFlashMessage('success', 'Pembelian berhasil diupdate.');
                } else {
                    setFlashMessage('error', 'Gagal update pembelian.');
                }
            } else {
                // Create
                if ($this->pembelianModel->create($data)) {
                    setFlashMessage('success', 'Pembelian berhasil ditambahkan.');
                } else {
                    setFlashMessage('error', 'Gagal tambah pembelian.');
                }
            }
            
            redirect('laporan');
        }
    }
    public function delete_pembelian($id) {
        if ($this->pembelianModel->delete($id)) {
            setFlashMessage('success', 'Pembelian berhasil dihapus.');
        } else {
            setFlashMessage('error', 'Gagal hapus pembelian.');
        }
        redirect('laporan');
    }
    
    public function bayar() { // Pelunasan
        // Note: Currently Pelunasan is mostly automatic from POS, but preserving method for potential manual future use
        if (isPost()) {
            $data = [
                'tanggal' => $_POST['tanggal'],
                'supplier_id' => $_POST['supplier_id'],
                'jumlah_bayar' => $_POST['jumlah_bayar'],
                'keterangan' => $_POST['keterangan']
            ];
            if ($this->pembayaranModel->create($data)) {
                setFlashMessage('success', 'Pembayaran berhasil ditambahkan.');
            } else {
                setFlashMessage('error', 'Gagal tambah pembayaran.');
            }
            redirect('laporan');
        }
    }
}
