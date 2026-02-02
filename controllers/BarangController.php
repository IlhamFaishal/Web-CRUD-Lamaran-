<?php

// ... imports
require_once __DIR__ . '/../models/BarangModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';
require_once __DIR__ . '/../models/KodeSupplier.php';
require_once __DIR__ . '/../models/MasterKodeBarang.php';

class BarangController {
    private $model;
    private $kategoriModel;
    private $supplierModel;
    private $masterKodeModel;

    public function __construct() {
        requireLogin();
        $this->model = new BarangModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new KodeSupplier();
        $this->masterKodeModel = new MasterKodeBarang();
    }

    public function index() {
        $barang = $this->model->getAll();
        $kategori = $this->kategoriModel->getAll();
        $suppliers = $this->supplierModel->getAll(); // Add this
        $masterKode = $this->masterKodeModel->getAll(); // Add this
        view('barang/index', [
            'barang' => $barang, 
            'kategori' => $kategori,
            'suppliers' => $suppliers, // Add this
            'masterKode' => $masterKode // Add this
        ]);
    }

    public function create() {
        $kategori = $this->kategoriModel->getAll();
        $suppliers = $this->supplierModel->getAll();
        $masterKode = $this->masterKodeModel->getAll();
        view('barang/create', [
            'kategori' => $kategori, 
            'suppliers' => $suppliers,
            'masterKode' => $masterKode
        ]);
    }

    private function uploadImage($file) {
        $targetDir = __DIR__ . '/../assets/uploads/products/';
        
        // Check error
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        // Check file type
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return false;

        // Check size (max 500KB)
        if ($file['size'] > 500 * 1024) return false;

        // Generate filename
        $filename = uniqid() . '.' . $ext;
        
        // Create directory if not exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
            return $filename;
        }

        return false;
    }

    public function store() {
        if (!isPost()) redirect('barang');
        
        $data = $_POST;
        
        // KODE BARANG = KODE PREFIX (Sesuai Request)
        if (isset($data['kode_prefix'])) {
            $data['kode_barang'] = $data['kode_prefix'];
        }

        $data = sanitize($data);

        // Validation
        $errors = validateRequired($data, ['kode_prefix', 'nama_barang', 'kategori_id', 'satuan', 'harga_jual', 'stok']);
        
        // Cek Duplikasi (Kode + Supplier harus Unik)
        $existing = $this->model->findByKodeAndSupplier($data['kode_barang'], $data['supplier_id'] ?? null);
        if ($existing) {
             $errors[] = "Kode Barang '{$data['kode_barang']}' sudah ada untuk Supplier ini.";
        }

        if ($data['harga_jual'] < 0) $errors[] = "Harga Jual tidak boleh negatif.";
        if ($data['stok'] < 0) $errors[] = "Stok tidak boleh negatif.";

        // Handle File Upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImage($_FILES['gambar']);
            if ($upload === false) {
                $errors[] = "Gagal upload gambar. Pastikan format foto valid dan ukuran < 500KB.";
            } else {
                $gambar = $upload;
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            // Keep input values
            $_SESSION['old'] = $data;
            redirect('barang/create');
        }

        $data['gambar'] = $gambar;

        if ($this->model->create($data)) {
            setFlashMessage('success', 'Barang berhasil ditambahkan.');
            unset($_SESSION['old']);
            redirect('barang');
        } else {
            setFlashMessage('error', 'Gagal menambahkan barang.');
            redirect('barang/create');
        }
    }

    public function edit($id) {
        $barang = $this->model->findById($id);
        $kategori = $this->kategoriModel->getAll();
        $suppliers = $this->supplierModel->getAll();
        $masterKode = $this->masterKodeModel->getAll();
        
        if (!$barang) redirect('barang');
        
        view('barang/edit', [
            'barang' => $barang, 
            'kategori' => $kategori,
            'suppliers' => $suppliers,
            'masterKode' => $masterKode
        ]);
    }

    public function update($id) {
        if (!isPost()) redirect('barang');
        
        $data = $_POST;
        
        $data = $_POST;
        
        // KODE BARANG = KODE PREFIX (Sesuai Request Update)
        if (isset($data['kode_prefix'])) {
            $data['kode_barang'] = $data['kode_prefix'];
        }

        $data = sanitize($data);

        $errors = validateRequired($data, ['kode_prefix', 'nama_barang', 'kategori_id', 'satuan', 'harga_jual', 'stok']);

        // Check Unique Code
        // Jika kode berubah, atau tetap sama, kita cek apakah ada barang LAIN dengan kode ini DAN supplier ini
        $existing = $this->model->findByKodeAndSupplier($data['kode_barang'], $data['supplier_id'] ?? null);
        if ($existing && $existing['id_barang'] != $id) {
            $errors[] = "Kode Barang '{$data['kode_barang']}' sudah digunakan barang lain dengan Supplier ini.";
        }

        if ($data['harga_jual'] < 0) $errors[] = "Harga Jual tidak boleh negatif.";
        if ($data['stok'] < 0) $errors[] = "Stok tidak boleh negatif.";

        // Handle File Upload
        $oldItem = $this->model->findById($id);
        $gambar = $oldItem['gambar'];

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImage($_FILES['gambar']);
            if ($upload === false) {
                $errors[] = "Gagal upload gambar. Pastikan format foto valid dan ukuran < 500KB.";
            } else {
                // Delete old image
                if ($gambar && file_exists(__DIR__ . '/../assets/uploads/products/' . $gambar)) {
                    unlink(__DIR__ . '/../assets/uploads/products/' . $gambar);
                }
                $gambar = $upload;
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect("barang/edit/$id");
        }

        $data['gambar'] = $gambar;

        if ($this->model->update($id, $data)) {
            setFlashMessage('success', 'Barang berhasil diupdate.');
            redirect('barang');
        } else {
            setFlashMessage('error', 'Gagal update barang.');
            redirect("barang/edit/$id");
        }
    }

    public function delete($id) {
        try {
            $item = $this->model->findById($id);
            
            if ($this->model->delete($id)) {
                // Soft delete: Do not delete image file, just mark as inactive in DB.
                setFlashMessage('success', 'Barang berhasil dihapus (diarsipkan).');
            } else {
                setFlashMessage('error', 'Gagal menghapus barang.');
            }
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal hapus, kemungkinan barang sudah ada di transaksi.');
        }
        redirect('barang');
    }
}
