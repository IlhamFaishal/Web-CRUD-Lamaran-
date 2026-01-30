<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Data Kategori</h1>
            <a href="<?= BASE_URL ?>/kategori/create" class="btn btn-primary">Tambah Kategori</a>
        </div>

        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Nama Kategori</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori as $cat): ?>
                    <tr>
                        <td><?= $cat['id_kategori'] ?></td>
                        <td><?= $cat['nama_kategori'] ?></td>
                        <td style="display: flex; gap: 5px;">
                            <a href="<?= BASE_URL ?>/kategori/edit/<?= $cat['id_kategori'] ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                            <a href="<?= BASE_URL ?>/kategori/delete/<?= $cat['id_kategori'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    </div> <!-- Close Wrapper explicitly if needed by layout logic, usually Footer closes it -->
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
