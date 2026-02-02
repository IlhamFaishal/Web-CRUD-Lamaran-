<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Edit Barang</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 800px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/barang/update/<?= $barang['id_barang'] ?>" method="POST" enctype="multipart/form-data">
                
                <!-- PILIH SUPPLIER -->
                <div class="form-group" style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
                    <label for="supplier_id" style="font-weight: bold; color: #2c3e50;">Pilih Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-control select2">
                        <option value="">-- Tidak Ada / Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id_kode_supplier'] ?>" <?= ($sup['id_kode_supplier'] == $barang['supplier_id']) ? 'selected' : '' ?>>
                                [<?= $sup['kode_supplier'] ?>] - <?= $sup['nama_supplier'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Gambar Produk</label>
                    <?php if ($barang['gambar']): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= ASSET_URL ?>/uploads/products/<?= $barang['gambar'] ?>" alt="Produk" style="max-width: 150px; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                    <small>Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>

                <!-- EDIT KODE BARANG -->
                <div class="form-group" style="background-color: #e8f6f3; padding: 15px; border-radius: 5px; border: 1px solid #d4efdf;">
                    <label style="font-weight: bold; color: #16a085;">Kategori Barang (Prefix)</label>
                    
                    <!-- INPUT HIDDEN UNTUK KODE LAMA JIKA TIDAK DIUBAH -->
                    
                    <select id="kode_prefix" name="kode_prefix" class="form-control" required style="background-color: #fff;">
                        <option value="">-- Pilih Jenis Barang --</option>
                        <?php foreach ($masterKode as $mk): ?>
                            <!-- Logic selects based on full match since now Kode Barang == Prefix -->
                            <option value="<?= $mk['kode_prefix'] ?>" <?= ($mk['kode_prefix'] == $barang['kode_barang']) ? 'selected' : '' ?>>
                                <?= $mk['kode_prefix'] ?> (<?= $mk['nama_prefix'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <small style="color: red; margin-top: 5px; display: block;">
                        <i class="fas fa-exclamation-triangle"></i> Perhatian: Kode Barang sama dengan Prefix. Mengubah ini akan memeriksa ulang ketersediaan kode.
                    </small>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="<?= $barang['nama_barang'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= ($kat['id_kategori'] == $barang['kategori_id']) ? 'selected' : '' ?>>
                                <?= $kat['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <select name="satuan" class="form-control" required>
                        <option value="Unit" <?= ($barang['satuan'] == 'Unit') ? 'selected' : '' ?>>Unit</option>
                        <option value="Set" <?= ($barang['satuan'] == 'Set') ? 'selected' : '' ?>>Set</option>
                        <option value="Pcs" <?= ($barang['satuan'] == 'Pcs') ? 'selected' : '' ?>>Pcs</option>
                        <option value="Buah" <?= ($barang['satuan'] == 'Buah') ? 'selected' : '' ?>>Buah</option>
                        <option value="Batang" <?= ($barang['satuan'] == 'Batang') ? 'selected' : '' ?>>Batang</option>
                        <option value="Lembar" <?= ($barang['satuan'] == 'Lembar') ? 'selected' : '' ?>>Lembar</option>
                        <option value="Meter" <?= ($barang['satuan'] == 'Meter') ? 'selected' : '' ?>>Meter</option>
                        <option value="Kg" <?= ($barang['satuan'] == 'Kg') ? 'selected' : '' ?>>Kg</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" min="0" value="<?= $barang['harga_jual'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" min="0" value="<?= $barang['stok'] ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update Barang</button>
                    <a href="<?= BASE_URL ?>/barang" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
