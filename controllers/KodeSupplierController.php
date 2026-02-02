<?php

require_once __DIR__ . '/../models/KodeSupplier.php';

class KodeSupplierController {
    private $model;
    
    public function __construct() {
        $this->model = new KodeSupplier();
    }
    
    // Display list of kode supplier
    public function index() {
        $data = $this->model->getAll();
        require_once __DIR__ . '/../views/kode_supplier/index.php';
    }
    
    // Show create form
    public function create() {
        require_once __DIR__ . '/../views/kode_supplier/create.php';
    }
    
    // Store new kode supplier
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/kode-supplier');
            exit;
        }
        
        // Validation
        $errors = [];
        
        if (empty($_POST['kode_supplier'])) {
            $errors[] = "Kode supplier harus diisi";
        } elseif ($this->model->kodeExists($_POST['kode_supplier'])) {
            $errors[] = "Kode supplier sudah digunakan";
        }
        
        if (empty($_POST['nama_supplier'])) {
            $errors[] = "Nama supplier harus diisi";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/kode-supplier/create');
            exit;
        }
        
        // Create data
        $data = [
            'kode_supplier' => strtoupper(trim($_POST['kode_supplier'])),
            'nama_supplier' => trim($_POST['nama_supplier']),
            'alamat' => trim($_POST['alamat'] ?? ''),
            'telepon' => trim($_POST['telepon'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'keterangan' => trim($_POST['keterangan'] ?? '')
        ];
        
        if ($this->model->create($data)) {
            $_SESSION['success'] = "Kode supplier berhasil ditambahkan";
        } else {
            $_SESSION['error'] = "Gagal menambahkan kode supplier";
        }
        
        header('Location: ' . BASE_URL . '/kode-supplier');
        exit;
    }
    
    // Show edit form
    public function edit($id) {
        $supplier = $this->model->getById($id);
        
        if (!$supplier) {
            $_SESSION['error'] = "Data tidak ditemukan";
            header('Location: ' . BASE_URL . '/kode-supplier');
            exit;
        }
        
        require_once __DIR__ . '/../views/kode_supplier/edit.php';
    }
    
    // Update kode supplier
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/kode-supplier');
            exit;
        }
        
        // Validation
        $errors = [];
        
        if (empty($_POST['kode_supplier'])) {
            $errors[] = "Kode supplier harus diisi";
        } elseif ($this->model->kodeExists($_POST['kode_supplier'], $id)) {
            $errors[] = "Kode supplier sudah digunakan";
        }
        
        if (empty($_POST['nama_supplier'])) {
            $errors[] = "Nama supplier harus diisi";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '/kode-supplier/edit/' . $id);
            exit;
        }
        
        // Update data
        $data = [
            'kode_supplier' => strtoupper(trim($_POST['kode_supplier'])),
            'nama_supplier' => trim($_POST['nama_supplier']),
            'alamat' => trim($_POST['alamat'] ?? ''),
            'telepon' => trim($_POST['telepon'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'keterangan' => trim($_POST['keterangan'] ?? '')
        ];
        
        if ($this->model->update($id, $data)) {
            $_SESSION['success'] = "Kode supplier berhasil diupdate";
        } else {
            $_SESSION['error'] = "Gagal mengupdate kode supplier";
        }
        
        header('Location: ' . BASE_URL . '/kode-supplier');
        exit;
    }
    
    // Delete kode supplier
    public function delete($id) {
        if ($this->model->delete($id)) {
            $_SESSION['success'] = "Kode supplier berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus kode supplier";
        }
        
        header('Location: ' . BASE_URL . '/kode-supplier');
        exit;
    }
}
