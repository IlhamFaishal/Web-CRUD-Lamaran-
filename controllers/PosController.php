<?php

require_once __DIR__ . '/../models/BarangModel.php';
require_once __DIR__ . '/../models/TransaksiModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';
require_once __DIR__ . '/../models/PembayaranModel.php';

class PosController {
    private $barangModel;
    private $transaksiModel;
    private $kategoriModel;
    private $pembayaranModel;

    public function __construct() {
        requireLogin();
        $this->barangModel = new BarangModel();
        $this->transaksiModel = new TransaksiModel();
        $this->kategoriModel = new KategoriModel();
        $this->pembayaranModel = new PembayaranModel();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index() {
        $keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';
        $catId = isset($_GET['cat']) ? sanitize($_GET['cat']) : '';
        $sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : '';

        $barang = $this->barangModel->getFiltered($keyword, $catId, $sort);
        $kategori = $this->kategoriModel->getAll();

        // Calculate Total
        $totalCart = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalCart += $item['subtotal'];
        }

        view('pos/index', [
            'barang' => $barang, 
            'kategori' => $kategori,
            'cart' => $_SESSION['cart'],
            'totalCart' => $totalCart,
            'keyword' => $keyword,
            'currentCat' => $catId,
            'currentSort' => $sort
        ]);
    }

    public function addToCart($id) {
        $barang = $this->barangModel->findById($id);
        if (!$barang) redirect('pos');

        // Check stock
        if ($barang['stok'] <= 0) {
            setFlashMessage('error', 'Stok habis!');
            redirect('pos');
        }

        if (isset($_SESSION['cart'][$id])) {
            // Check if adding 1 more exceeds stock
            if ($_SESSION['cart'][$id]['qty'] + 1 > $barang['stok']) {
                setFlashMessage('error', 'Stok tidak mumpuni!');
                redirect('pos');
            }
            $_SESSION['cart'][$id]['qty']++;
            $_SESSION['cart'][$id]['subtotal'] = $_SESSION['cart'][$id]['qty'] * $_SESSION['cart'][$id]['price'];
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $barang['id_barang'],
                'code' => $barang['kode_barang'],
                'name' => $barang['nama_barang'],
                'price' => $barang['harga_jual'],
                'supplier_id' => $barang['supplier_id'], // Track supplier
                'qty' => 1,
                'subtotal' => $barang['harga_jual']
            ];
        }
        redirect('pos');
    }

    public function updateQty() {
        if (!isPost()) redirect('pos');
        
        $id = $_POST['id'];
        $qty = intval($_POST['qty']);

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            // Check stock again
            $barang = $this->barangModel->findById($id);
            if ($qty > $barang['stok']) {
                setFlashMessage('error', 'Stok tidak cukup!');
            } else {
                $_SESSION['cart'][$id]['qty'] = $qty;
                $_SESSION['cart'][$id]['subtotal'] = $qty * $_SESSION['cart'][$id]['price'];
            }
        }
        redirect('pos');
    }

    public function deleteItem($id) {
        unset($_SESSION['cart'][$id]);
        redirect('pos');
    }

    public function reset() {
        $_SESSION['cart'] = [];
        redirect('pos');
    }

    public function checkout() {
        if (!isPost()) redirect('pos');

        if (empty($_SESSION['cart'])) {
            setFlashMessage('error', 'Keranjang kosong!');
            redirect('pos');
        }

        $bayar = floatval(str_replace('.', '', $_POST['bayar'])); // Remove dots if existing logic puts them, but input type number usually clean.
        // Assuming input is raw number from type="number"
        $bayar = $_POST['bayar'];
        
        $totalCart = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalCart += $item['subtotal'];
        }

        if ($bayar < $totalCart) {
            setFlashMessage('error', 'Uang pembayaran kurang!');
            redirect('pos');
        }

        $kembalian = $bayar - $totalCart;
        $noTransaksi = $this->transaksiModel->generateNoTransaksi();

        $data = [
            'no_transaksi' => $noTransaksi,
            'total_harga' => $totalCart,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'details' => $_SESSION['cart']
        ];

        try {
            $transaksiId = $this->transaksiModel->create($data);
            
            // Group cart items by supplier and calculate payment per supplier
            $supplierPayments = [];
            foreach ($_SESSION['cart'] as $item) {
                $supplierId = $item['supplier_id'];
                
                // Skip items without supplier
                if (!$supplierId) continue;
                
                if (!isset($supplierPayments[$supplierId])) {
                    $supplierPayments[$supplierId] = 0;
                }
                
                // Add this item's subtotal to supplier's payment
                $supplierPayments[$supplierId] += $item['subtotal'];
            }
            
            // Record payment for each supplier
            foreach ($supplierPayments as $supplierId => $amount) {
                $paymentData = [
                    'supplier_id' => $supplierId,
                    'tanggal' => date('Y-m-d'),
                    'jumlah_bayar' => $amount,
                    'keterangan' => 'Pembayaran otomatis dari transaksi POS #' . $noTransaksi
                ];
                
                $this->pembayaranModel->create($paymentData);
            }
            
            $_SESSION['cart'] = []; // Clear cart
            
            // Redirect to receipt
            echo "<script>
                alert('Transaksi Berhasil! Kembalian: " . formatRupiah($kembalian) . "');
                window.location.href = '" . BASE_URL . "/pos/struk/$transaksiId';
            </script>";
            exit;

        } catch (Exception $e) {
            setFlashMessage('error', 'Transaksi Gagal: ' . $e->getMessage());
            redirect('pos');
        }
    }

    public function struk($id) {
        $transaksi = $this->transaksiModel->findById($id);
        if (!$transaksi) redirect('pos');
        
        $details = $this->transaksiModel->getDetails($id);
        
        require_once __DIR__ . '/../views/pos/struk.php';
    }

    public function history() {
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $startDate = null;
        $endDate = null;

        if ($filter == 'today') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($filter == '7days') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter == '30days') {
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
        }

        $history = $this->transaksiModel->getAll($startDate, $endDate);
        
        // Calculate statistics
        $totalSales = 0;
        $totalTransactions = count($history);
        $dailySales = [];
        $categorySales = [];
        
        foreach ($history as $row) {
            $totalSales += $row['total_harga'];
            
            // Group by date for daily sales chart
            $date = date('Y-m-d', strtotime($row['tanggal']));
            if (!isset($dailySales[$date])) {
                $dailySales[$date] = 0;
            }
            $dailySales[$date] += $row['total_harga'];
        }
        
        // Get category sales data
        $categorySales = $this->transaksiModel->getSalesByCategory($startDate, $endDate);
        
        $averageSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        // Prepare chart data
        $chartLabels = array_keys($dailySales);
        $chartData = array_values($dailySales);
        
        view('pos/history', [
            'history' => $history,
            'currentFilter' => $filter,
            'stats' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'average_sale' => $averageSale
            ],
            'chartData' => [
                'labels' => $chartLabels,
                'data' => $chartData
            ],
            'categorySales' => $categorySales
        ]);
    }
}
