<?php

class KategoriModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM kategori ORDER BY id_kategori DESC");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM kategori WHERE id_kategori = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByName($nama) {
        $stmt = $this->db->prepare("SELECT * FROM kategori WHERE nama_kategori = :nama");
        $stmt->bindParam(':nama', $nama);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO kategori (nama_kategori) VALUES (:nama)");
        $stmt->bindParam(':nama', $data['nama_kategori']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE kategori SET nama_kategori = :nama WHERE id_kategori = :id");
        $stmt->bindParam(':nama', $data['nama_kategori']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM kategori WHERE id_kategori = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
