<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Data Kategori</h1>
            <button onclick="openModal('add')" class="btn btn-primary">Tambah Kategori</button>
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
                        <td style="display: flex; gap: 2px;">
                            <button onclick="openModal('edit', '<?= $cat['id_kategori'] ?>', '<?= htmlspecialchars($cat['nama_kategori']) ?>')" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</button>
                            <a href="<?= BASE_URL ?>/kategori/delete/<?= $cat['id_kategori'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="kategoriModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Kategori</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="kategoriForm" action="" method="POST">
                <div class="form-group">
                    <label>Nama Kategori <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success" id="submitBtn">Simpan</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const baseUrl = "<?= BASE_URL ?>";
        const modal = document.getElementById('kategoriModal');
        const form = document.getElementById('kategoriForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const namaInput = document.getElementById('nama_kategori');

        function openModal(mode, id = null, nama = '') {
            modal.classList.add('show');
            
            if (mode === 'add') {
                modalTitle.textContent = "Tambah Kategori";
                form.action = baseUrl + "/kategori/store";
                submitBtn.textContent = "Simpan";
                namaInput.value = "";
            } else {
                modalTitle.textContent = "Edit Kategori";
                form.action = baseUrl + "/kategori/update/" + id;
                submitBtn.textContent = "Update";
                namaInput.value = nama;
            }
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        // Close on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>

    </div> <!-- Close Wrapper explicitly if needed by layout logic, usually Footer closes it -->
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
