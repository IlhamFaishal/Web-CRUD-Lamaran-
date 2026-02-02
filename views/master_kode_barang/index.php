<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Master Kode Barang</h1>
            <button onclick="openModal('add')" class="btn btn-primary">Tambah Kode Barang</button>
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
                        <th width="50">No</th>
                        <th width="150">Kode Prefix</th>
                        <th>Nama Prefix</th>
                        <th>Deskripsi</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data</td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($data as $kode): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong style="font-size: 1.1rem; color: #3498db;"><?= htmlspecialchars($kode['kode_prefix']) ?></strong></td>
                        <td><?= htmlspecialchars($kode['nama_prefix']) ?></td>
                        <td><?= htmlspecialchars($kode['deskripsi'] ?? '-') ?></td>
                        <td style="display: flex; gap: 2px;">
                            <button onclick="openModal('edit', '<?= $kode['id_master_kode'] ?>', '<?= htmlspecialchars($kode['kode_prefix']) ?>', '<?= htmlspecialchars($kode['nama_prefix']) ?>', '<?= htmlspecialchars($kode['deskripsi'] ?? '') ?>')" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</button>
                            <a href="<?= BASE_URL ?>/master-kode-barang/delete/<?= $kode['id_master_kode'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus kode prefix ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="masterKodeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Master Kode Barang</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="masterKodeForm" action="" method="POST">
                <div class="form-group">
                    <label>Kode Prefix <span style="color: red;">*</span></label>
                    <input type="text" name="kode_prefix" id="kode_prefix" class="form-control" required style="text-transform: uppercase;" maxlength="10">
                    <small style="color: #666;">Contoh: BS, KY, AL</small>
                </div>
                
                <div class="form-group">
                    <label>Nama Prefix <span style="color: red;">*</span></label>
                    <input type="text" name="nama_prefix" id="nama_prefix" class="form-control" required>
                    <small style="color: #666;">Contoh: Besi, Kayu, Aluminium</small>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
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
        const modal = document.getElementById('masterKodeModal');
        const form = document.getElementById('masterKodeForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const kodeInput = document.getElementById('kode_prefix');
        const namaInput = document.getElementById('nama_prefix');
        const deskripsiInput = document.getElementById('deskripsi');

        function openModal(mode, id = null, kode = '', nama = '', deskripsi = '') {
            modal.classList.add('show');
            
            if (mode === 'add') {
                modalTitle.textContent = "Tambah Master Kode Barang";
                form.action = baseUrl + "/master-kode-barang/store";
                submitBtn.textContent = "Simpan";
                kodeInput.value = "";
                namaInput.value = "";
                deskripsiInput.value = "";
            } else {
                modalTitle.textContent = "Edit Master Kode Barang";
                form.action = baseUrl + "/master-kode-barang/update/" + id;
                submitBtn.textContent = "Update";
                kodeInput.value = kode;
                namaInput.value = nama;
                deskripsiInput.value = deskripsi;
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
