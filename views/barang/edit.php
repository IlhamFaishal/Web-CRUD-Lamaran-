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

        <div style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/barang/update/<?= $barang['id_barang'] ?>" method="POST" enctype="multipart/form-data">
                
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

                <div class="form-group">
                    <label>Kode Barang (Unik)</label>
                    <input type="text" name="kode_barang" class="form-control" value="<?= $barang['kode_barang'] ?>" required>
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
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="<?= BASE_URL ?>/barang" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
