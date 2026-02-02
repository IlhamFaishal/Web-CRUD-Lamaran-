<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Tambah Master Kode Barang</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/master-kode-barang/store" method="POST">
                <div class="form-group">
                    <label>Kode Prefix <span style="color: red;">*</span></label>
                    <input type="text" name="kode_prefix" class="form-control" value="<?= $_SESSION['old']['kode_prefix'] ?? '' ?>" required autofocus style="text-transform: uppercase;" maxlength="10">
                    <small style="color: #666;">Contoh: BS (Besi), KY (Kayu), AL (Aluminium) - Maksimal 10 karakter</small>
                </div>
                
                <div class="form-group">
                    <label>Nama Prefix <span style="color: red;">*</span></label>
                    <input type="text" name="nama_prefix" class="form-control" value="<?= $_SESSION['old']['nama_prefix'] ?? '' ?>" required>
                    <small style="color: #666;">Contoh: Besi, Kayu, Aluminium</small>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= $_SESSION['old']['deskripsi'] ?? '' ?></textarea>
                    <small style="color: #666;">Keterangan tambahan tentang kode prefix ini</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="<?= BASE_URL ?>/master-kode-barang" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php unset($_SESSION['old']); ?>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
