<?php

require_once __DIR__ . '/../models/BarangModel.php';
require_once __DIR__ . '/../models/TransaksiModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';

class PosController {
    private $barangModel;
    private $transaksiModel;
    private $kategoriModel;

    public function __construct() {
        requireLogin();
        $this->barangModel = new BarangModel();
        $this->transaksiModel = new TransaksiModel();
        $this->kategoriModel = new KategoriModel();
        
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
}
