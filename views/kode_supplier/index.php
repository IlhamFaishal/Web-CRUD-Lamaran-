<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Data Kode Supplier</h1>
            <button onclick="openModal('add')" class="btn btn-primary">Tambah Kode Supplier</button>
        </div>

        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-fit">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="120">Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data</td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($data as $supplier): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($supplier['kode_supplier']) ?></strong></td>
                        <td><?= htmlspecialchars($supplier['nama_supplier']) ?></td>
                        <td style="display: flex; gap: 2px;">
                            <button onclick="openModal('edit', '<?= $supplier['id_kode_supplier'] ?>', '<?= htmlspecialchars($supplier['kode_supplier']) ?>', '<?= htmlspecialchars($supplier['nama_supplier']) ?>')" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</button>
                            <a href="<?= BASE_URL ?>/kode-supplier/delete/<?= $supplier['id_kode_supplier'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus kode supplier ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="supplierModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Kode Supplier</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="supplierForm" action="" method="POST">
                <div class="form-group">
                    <label>Kode Supplier <span style="color: red;">*</span></label>
                    <input type="text" name="kode_supplier" id="kode_supplier" class="form-control" required style="text-transform: uppercase;">
                    <small style="color: #666;">Contoh: SUP001, SUP002</small>
                </div>
                
                <div class="form-group">
                    <label>Nama Supplier <span style="color: red;">*</span></label>
                    <input type="text" name="nama_supplier" id="nama_supplier" class="form-control" required>
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
        const modal = document.getElementById('supplierModal');
        const form = document.getElementById('supplierForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const kodeInput = document.getElementById('kode_supplier');
        const namaInput = document.getElementById('nama_supplier');

        function openModal(mode, id = null, kode = '', nama = '') {
            modal.classList.add('show');
            
            if (mode === 'add') {
                modalTitle.textContent = "Tambah Kode Supplier";
                form.action = baseUrl + "/kode-supplier/store";
                submitBtn.textContent = "Simpan";
                kodeInput.value = "";
                namaInput.value = "";
            } else {
                modalTitle.textContent = "Edit Kode Supplier";
                form.action = baseUrl + "/kode-supplier/update/" + id;
                submitBtn.textContent = "Update";
                kodeInput.value = kode;
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

    </div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
