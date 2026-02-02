-- Drop the existing unique index on kode_barang
ALTER TABLE barang DROP INDEX kode_barang;

-- Add a new unique index on (kode_barang, supplier_id)
-- This allows same kode_barang for different suppliers
ALTER TABLE barang ADD CONSTRAINT unique_kode_supplier UNIQUE (kode_barang, supplier_id);
