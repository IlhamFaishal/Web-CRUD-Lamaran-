<?php

require_once __DIR__ . '/../models/MasterKodeBarang.php';

class MasterKodeBarangController {
    private $model;
    
    public function __construct() {
        $this->model = new MasterKodeBarang();
    }
    
    // Display list of master kode barang
    public function index() {
        $data = $this->model->getAll();
        require_once __DIR__ . '/../views/master_kode_barang/index.php';
    }
    
    // Show create form
    public function create() {
        require_once __DIR__ . '/../views/master_kode_barang/create.php';
    }
    
    // Store new master kode barang
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/master-kode-barang');
            exit;
        }
        
        // Validation
        $errors = [];
        
        if (empty($_POST['kode_prefix'])) {
            $errors[] = "Kode prefix harus diisi";
        } elseif ($this->model->prefixExists($_POST['kode_prefix'])) {
            $errors[] = "Kode prefix sudah digunakan";
        }
        
        if (empty($_POST['nama_prefix'])) {
            $errors[] = "Nama prefix harus diisi";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/master-kode-barang/create');
            exit;
        }
        
        // Create data
        $data = [
            'kode_prefix' => trim($_POST['kode_prefix']),
            'nama_prefix' => trim($_POST['nama_prefix']),
            'deskripsi' => trim($_POST['deskripsi'] ?? '')
        ];
        
        if ($this->model->create($data)) {
            $_SESSION['success'] = "Master kode barang berhasil ditambahkan";
        } else {
            $_SESSION['error'] = "Gagal menambahkan master kode barang";
        }
        
        header('Location: ' . BASE_URL . '/master-kode-barang');
        exit;
    }
    
    // Show edit form
    public function edit($id) {
        $kode = $this->model->getById($id);
        
        if (!$kode) {
            $_SESSION['error'] = "Data tidak ditemukan";
            header('Location: ' . BASE_URL . '/master-kode-barang');
            exit;
        }
        
        require_once __DIR__ . '/../views/master_kode_barang/edit.php';
    }
    
    // Update master kode barang
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/master-kode-barang');
            exit;
        }
        
        // Validation
        $errors = [];
        
        if (empty($_POST['kode_prefix'])) {
            $errors[] = "Kode prefix harus diisi";
        } elseif ($this->model->prefixExists($_POST['kode_prefix'], $id)) {
            $errors[] = "Kode prefix sudah digunakan";
        }
        
        if (empty($_POST['nama_prefix'])) {
            $errors[] = "Nama prefix harus diisi";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/master-kode-barang/edit/' . $id);
            exit;
        }
        
        // Update data
        $data = [
            'kode_prefix' => trim($_POST['kode_prefix']),
            'nama_prefix' => trim($_POST['nama_prefix']),
            'deskripsi' => trim($_POST['deskripsi'] ?? '')
        ];
        
        if ($this->model->update($id, $data)) {
            $_SESSION['success'] = "Master kode barang berhasil diupdate";
        } else {
            $_SESSION['error'] = "Gagal mengupdate master kode barang";
        }
        
        header('Location: ' . BASE_URL . '/master-kode-barang');
        exit;
    }
    
    // Delete master kode barang
    public function delete($id) {
        if ($this->model->delete($id)) {
            $_SESSION['success'] = "Master kode barang berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus master kode barang";
        }
        
        header('Location: ' . BASE_URL . '/master-kode-barang');
        exit;
    }
}
