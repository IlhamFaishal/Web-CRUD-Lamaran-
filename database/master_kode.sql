-- Database Schema untuk Master Kode (Kode Supplier dan Kode Barang)

-- Tabel Kode Supplier
CREATE TABLE `kode_supplier` (
  `id_kode_supplier` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_supplier` VARCHAR(50) NOT NULL,
  `nama_supplier` VARCHAR(200) NOT NULL,
  `alamat` TEXT,
  `telepon` VARCHAR(20),
  `email` VARCHAR(100),
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kode_supplier`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Kode Barang (Master Kode untuk Barang)
CREATE TABLE `master_kode_barang` (
  `id_master_kode` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_prefix` VARCHAR(10) NOT NULL,
  `nama_prefix` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_master_kode`),
  UNIQUE KEY `kode_prefix` (`kode_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data contoh untuk Kode Supplier
INSERT INTO `kode_supplier` (`kode_supplier`, `nama_supplier`, `alamat`, `telepon`, `email`, `keterangan`) VALUES
('SUP001', 'PT Besi Jaya', 'Jl. Industri No. 123, Jakarta', '021-12345678', 'info@besijaya.com', 'Supplier besi berkualitas'),
('SUP002', 'CV Kayu Makmur', 'Jl. Raya Bogor KM 45, Bogor', '0251-987654', 'kayu@makmur.co.id', 'Supplier kayu terpercaya'),
('SUP003', 'UD Logam Sejahtera', 'Jl. Gatot Subroto No. 88, Bandung', '022-5556677', 'logam@sejahtera.com', 'Supplier logam dan besi');

-- Insert data contoh untuk Master Kode Barang
INSERT INTO `master_kode_barang` (`kode_prefix`, `nama_prefix`, `deskripsi`) VALUES
('BS', 'Besi', 'Prefix untuk produk besi'),
('KY', 'Kayu', 'Prefix untuk produk kayu'),
('LG', 'Logam', 'Prefix untuk produk logam'),
('AL', 'Aluminium', 'Prefix untuk produk aluminium'),
('ST', 'Stainless', 'Prefix untuk produk stainless steel');
