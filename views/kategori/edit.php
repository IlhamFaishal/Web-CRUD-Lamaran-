<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h2>Edit Kategori</h2>
        
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 500px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <form action="<?= BASE_URL ?>/kategori/update/<?= $kategori['id_kategori'] ?>" method="POST">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" value="<?= $kategori['nama_kategori'] ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="<?= BASE_URL ?>/kategori" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
