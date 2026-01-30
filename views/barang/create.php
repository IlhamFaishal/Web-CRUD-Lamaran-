<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Tambah Barang</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/barang/store" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="gambar">Gambar Produk</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                    <small>Format: JPG, PNG, GIF (Max 2MB)</small>
                </div>
                <div class="form-group">
                    <label for="kode_barang">Kode Barang (Unik)</label>
                    <input type="text" id="kode_barang" name="kode_barang" class="form-control" required placeholder="Contoh: BRG001">
                </div>
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" required placeholder="Contoh: Meja Makan Jati, Rak Besi 4 Susun">
                </div>
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <select id="satuan" name="satuan" class="form-control" required>
                        <option value="Unit">Unit</option>
                        <option value="Set">Set</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Buah">Buah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="harga_jual">Harga Jual</label>
                    <input type="number" id="harga_jual" name="harga_jual" class="form-control" min="0" required placeholder="Contoh: 50000">
                </div>
                <div class="form-group">
                    <label for="stok">Stok Awal</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required placeholder="Contoh: 100">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="<?= BASE_URL ?>/barang" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
