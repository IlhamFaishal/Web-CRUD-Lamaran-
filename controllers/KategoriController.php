<?php

require_once __DIR__ . '/../models/KategoriModel.php';

class KategoriController {
    private $model;

    public function __construct() {
        requireLogin();
        $this->model = new KategoriModel();
    }

    public function index() {
        $kategori = $this->model->getAll();
        view('kategori/index', ['kategori' => $kategori]);
    }

    public function create() {
        view('kategori/create');
    }

    public function store() {
        if (!isPost()) redirect('kategori');

        $nama = sanitize($_POST['nama_kategori']);
        
        if (empty($nama)) {
            setFlashMessage('error', 'Nama kategori wajib diisi.');
            redirect('kategori/create');
        }

        if ($this->model->findByName($nama)) {
            setFlashMessage('error', 'Kategori ini sudah ada.');
            redirect('kategori/create');
        }

        if ($this->model->create(['nama_kategori' => $nama])) {
            setFlashMessage('success', 'Kategori berhasil ditambahkan.');
            redirect('kategori');
        } else {
            setFlashMessage('error', 'Gagal menambahkan kategori.');
            redirect('kategori/create');
        }
    }

    public function edit($id) {
        $kategori = $this->model->findById($id);
        if (!$kategori) redirect('kategori');
        view('kategori/edit', ['kategori' => $kategori]);
    }

    public function update($id) {
        if (!isPost()) redirect('kategori');

        $nama = sanitize($_POST['nama_kategori']);
        
        if (empty($nama)) {
            setFlashMessage('error', 'Nama kategori wajib diisi.');
            redirect("kategori/edit/$id");
        }

        // Check duplicate if name changed
        $existing = $this->model->findByName($nama);
        if ($existing && $existing['id_kategori'] != $id) {
             setFlashMessage('error', 'Nama kategori sudah digunakan.');
             redirect("kategori/edit/$id");
        }

        if ($this->model->update($id, ['nama_kategori' => $nama])) {
            setFlashMessage('success', 'Kategori berhasil diupdate.');
            redirect('kategori');
        } else {
            setFlashMessage('error', 'Gagal update kategori.');
            redirect("kategori/edit/$id");
        }
    }

    public function delete($id) {
        try {
            if ($this->model->delete($id)) {
                setFlashMessage('success', 'Kategori berhasil dihapus.');
            } else {
                setFlashMessage('error', 'Gagal menghapus kategori.');
            }
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal hapus, kategori sedang digunakan di barang.');
        }
        redirect('kategori');
    }
}
