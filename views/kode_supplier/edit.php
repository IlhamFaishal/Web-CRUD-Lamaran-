<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Edit Kode Supplier</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 700px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/kode-supplier/update/<?= $supplier['id_kode_supplier'] ?>" method="POST">
                <div class="form-group">
                    <label>Kode Supplier <span style="color: red;">*</span></label>
                    <input type="text" name="kode_supplier" class="form-control" value="<?= $_SESSION['old']['kode_supplier'] ?? $supplier['kode_supplier'] ?>" required autofocus style="text-transform: uppercase;">
                    <small style="color: #666;">Contoh: SUP001, SUP002</small>
                </div>
                
                <div class="form-group">
                    <label>Nama Supplier <span style="color: red;">*</span></label>
                    <input type="text" name="nama_supplier" class="form-control" value="<?= $_SESSION['old']['nama_supplier'] ?? $supplier['nama_supplier'] ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="<?= BASE_URL ?>/kode-supplier" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php unset($_SESSION['old']); ?>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
