ALTER TABLE barang ADD COLUMN is_active TINYINT(1) DEFAULT 1;
UPDATE barang SET is_active = 1;
