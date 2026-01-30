<?php

require_once __DIR__ . '/../models/BarangModel.php';
require_once __DIR__ . '/../models/KategoriModel.php';

class BarangController {
    private $model;
    private $kategoriModel;

    public function __construct() {
        requireLogin();
        $this->model = new BarangModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index() {
        $barang = $this->model->getAll();
        $kategori = $this->kategoriModel->getAll();
        view('barang/index', ['barang' => $barang, 'kategori' => $kategori]);
    }

    public function create() {
        $kategori = $this->kategoriModel->getAll();
        view('barang/create', ['kategori' => $kategori]);
    }

    private function uploadImage($file) {
        $targetDir = __DIR__ . '/../assets/uploads/products/';
        
        // Check error
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        // Check file type
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return false;

        // Check size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) return false;

        // Generate filename
        $filename = uniqid() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
            return $filename;
        }

        return false;
    }

    public function store() {
        if (!isPost()) redirect('barang');
        
        $data = $_POST;
        $data = sanitize($data);

        // Validation
        $errors = validateRequired($data, ['kode_barang', 'nama_barang', 'kategori_id', 'satuan', 'harga_jual', 'stok']);
        
        if ($this->model->findByKode($data['kode_barang'])) {
            $errors[] = "Kode Barang sudah ada.";
        }

        if ($data['harga_jual'] < 0) $errors[] = "Harga Jual tidak boleh negatif.";
        if ($data['stok'] < 0) $errors[] = "Stok tidak boleh negatif.";

        // Handle File Upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImage($_FILES['gambar']);
            if ($upload === false) {
                $errors[] = "Gagal upload gambar. Pastikan format JPG/PNG dan ukuran < 2MB.";
            } else {
                $gambar = $upload;
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('barang/create');
        }

        $data['gambar'] = $gambar;

        if ($this->model->create($data)) {
            setFlashMessage('success', 'Barang berhasil ditambahkan.');
            redirect('barang');
        } else {
            setFlashMessage('error', 'Gagal menambahkan barang.');
            redirect('barang/create');
        }
    }

    public function edit($id) {
        $barang = $this->model->findById($id);
        $kategori = $this->kategoriModel->getAll();
        
        if (!$barang) redirect('barang');
        
        view('barang/edit', ['barang' => $barang, 'kategori' => $kategori]);
    }

    public function update($id) {
        if (!isPost()) redirect('barang');
        
        $data = $_POST;
        $data = sanitize($data);

        $errors = validateRequired($data, ['kode_barang', 'nama_barang', 'kategori_id', 'satuan', 'harga_jual', 'stok']);

        // Check Unique Code
        $existing = $this->model->findByKode($data['kode_barang']);
        if ($existing && $existing['id_barang'] != $id) {
            $errors[] = "Kode Barang sudah ada.";
        }

        if ($data['harga_jual'] < 0) $errors[] = "Harga Jual tidak boleh negatif.";
        if ($data['stok'] < 0) $errors[] = "Stok tidak boleh negatif.";

        // Handle File Upload
        $oldItem = $this->model->findById($id);
        $gambar = $oldItem['gambar'];

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImage($_FILES['gambar']);
            if ($upload === false) {
                $errors[] = "Gagal upload gambar. Pastikan format JPG/PNG dan ukuran < 2MB.";
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
                // Delete image
                if ($item['gambar'] && file_exists(__DIR__ . '/../assets/uploads/products/' . $item['gambar'])) {
                    unlink(__DIR__ . '/../assets/uploads/products/' . $item['gambar']);
                }
                setFlashMessage('success', 'Barang berhasil dihapus.');
            } else {
                setFlashMessage('error', 'Gagal menghapus barang.');
            }
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal hapus, kemungkinan barang sudah ada di transaksi.');
        }
        redirect('barang');
    }
}
