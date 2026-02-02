<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Tambah Barang Baru</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 800px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/barang/store" method="POST" enctype="multipart/form-data">
                
                <!-- PILIH SUPPLIER -->
                <div class="form-group" style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
                    <label for="supplier_id" style="font-weight: bold; color: #2c3e50;">Pilih Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-control select2">
                        <option value="">-- Tidak Ada / Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id_kode_supplier'] ?>" <?= (isset($_SESSION['old']['supplier_id']) && $_SESSION['old']['supplier_id'] == $sup['id_kode_supplier']) ? 'selected' : '' ?>>
                                [<?= $sup['kode_supplier'] ?>] - <?= $sup['nama_supplier'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: #666;">Pilih supplier barang ini (Opsional)</small>
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar Produk</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                    <small>Format: JPG, PNG, GIF (Max 2MB)</small>
                </div>

                <!-- PILIH KODE BARANG DARI MASTER -->
                <div class="form-group" style="background-color: #e8f6f3; padding: 15px; border-radius: 5px; border: 1px solid #d4efdf;">
                    <label style="font-weight: bold; color: #16a085;">Kategori Barang (Prefix)</label>
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <select id="kode_prefix" name="kode_prefix" class="form-control" required style="background-color: #fff;">
                                <option value="">-- Pilih Jenis Barang --</option>
                                <?php foreach ($masterKode as $mk): ?>
                                    <option value="<?= $mk['kode_prefix'] ?>" data-nama="<?= $mk['nama_prefix'] ?>" <?= (isset($_SESSION['old']['kode_prefix']) && $_SESSION['old']['kode_prefix'] == $mk['kode_prefix']) ? 'selected' : '' ?>>
                                        <?= $mk['kode_prefix'] ?> (<?= $mk['nama_prefix'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <small style="color: #666; margin-top: 5px; display: block;">
                        <i class="fas fa-info-circle"></i> Kode Barang akan sama dengan Prefix yang dipilih. <br>
                        Contoh: Jika pilih <strong>BS</strong>, kode barang adalah <strong>BS</strong>.
                    </small>
                </div>

                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" required placeholder="Contoh: Meja Makan Jati" value="<?= $_SESSION['old']['nama_barang'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= (isset($_SESSION['old']['kategori_id']) && $_SESSION['old']['kategori_id'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                                <?= $kat['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <select id="satuan" name="satuan" class="form-control" required>
                        <option value="Unit" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Unit') ? 'selected' : '' ?>>Unit</option>
                        <option value="Set" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Set') ? 'selected' : '' ?>>Set</option>
                        <option value="Pcs" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Pcs') ? 'selected' : '' ?>>Pcs</option>
                        <option value="Buah" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Buah') ? 'selected' : '' ?>>Buah</option>
                        <option value="Batang" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Batang') ? 'selected' : '' ?>>Batang</option>
                        <option value="Lembar" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Lembar') ? 'selected' : '' ?>>Lembar</option>
                        <option value="Meter" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Meter') ? 'selected' : '' ?>>Meter</option>
                        <option value="Kg" <?= (isset($_SESSION['old']['satuan']) && $_SESSION['old']['satuan'] == 'Kg') ? 'selected' : '' ?>>Kg</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_jual">Harga Jual (Rp)</label>
                    <input type="number" id="harga_jual" name="harga_jual" class="form-control" min="0" required placeholder="0" value="<?= $_SESSION['old']['harga_jual'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label for="stok">Stok Awal</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required placeholder="0" value="<?= $_SESSION['old']['stok'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Simpan Barang</button>
                    <a href="<?= BASE_URL ?>/barang" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<script>
// Sedikit script helper untuk user experience
document.getElementById('kode_prefix').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var namaPrefix = selectedOption.getAttribute('data-nama');
    var namaBarangInput = document.getElementById('nama_barang');
    
    // Auto-fill nama barang depannya (optional, agar admin terbantu)
    // if(namaPrefix && namaBarangInput.value === '') {
    //     namaBarangInput.value = namaPrefix + " ";
    // }
    
    document.getElementById('kode_suffix').focus();
});
</script>

<?php 
// Clear session old data
unset($_SESSION['old']); 
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
