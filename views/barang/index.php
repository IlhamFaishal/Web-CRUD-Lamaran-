<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Data Barang</h1>
            <button onclick="openModal('add')" class="btn btn-primary">Tambah Barang</button>
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
                        <th>Gambar</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $brg): ?>
                    <tr>
                        <td>
                            <?php if ($brg['gambar']): ?>
                                <img src="<?= ASSET_URL ?>/uploads/products/<?= $brg['gambar'] ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span style="color: #999; font-size: 0.8rem;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $brg['kode_barang'] ?></td>
                        <td><?= $brg['nama_barang'] ?></td>
                        <td><?= $brg['nama_kategori'] ?></td>
                        <td><?= $brg['nama_supplier'] ?? '-' ?></td>
                        <td><?= $brg['satuan'] ?></td>
                        <td><?= formatRupiah($brg['harga_jual']) ?></td>
                        <td><?= $brg['stok'] ?></td>
                        <td style="display: flex; gap: 2px;">
                            <button 
                                onclick="openModal(this)"
                                data-mode="edit"
                                data-id="<?= $brg['id_barang'] ?>"
                                data-kode="<?= htmlspecialchars($brg['kode_barang']) ?>"
                                data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>"
                                data-kategori="<?= $brg['kategori_id'] ?>"
                                data-supplier="<?= $brg['supplier_id'] ?>"
                                data-satuan="<?= $brg['satuan'] ?>"
                                data-harga="<?= $brg['harga_jual'] ?>"
                                data-stok="<?= $brg['stok'] ?>"
                                data-gambar="<?= $brg['gambar'] ?>"
                                class="btn btn-primary" 
                                style="padding: 5px 10px; font-size: 0.8rem;">
                                Edit
                            </button>
                            <a href="<?= BASE_URL ?>/barang/delete/<?= $brg['id_barang'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="barangModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 800px;"> <!-- Wider for Barang -->
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Barang Baru</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="barangForm" action="" method="POST" enctype="multipart/form-data">
                
                <!-- PILIH SUPPLIER -->
                <div class="form-group" style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
                    <label for="supplier_id" style="font-weight: bold; color: #2c3e50;">Pilih Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-control">
                        <option value="">-- Tidak Ada / Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id_kode_supplier'] ?>">
                                [<?= $sup['kode_supplier'] ?>] - <?= $sup['nama_supplier'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PILIH KODE BARANG DARI MASTER -->
                <div class="form-group" style="background-color: #e8f6f3; padding: 15px; border-radius: 5px; border: 1px solid #d4efdf;">
                    <label style="font-weight: bold; color: #16a085;">Kategori Barang (Prefix)</label>
                    <select id="kode_prefix" name="kode_prefix" class="form-control" required style="background-color: #fff;">
                        <option value="">-- Pilih Jenis Barang --</option>
                        <?php foreach ($masterKode as $mk): ?>
                            <option value="<?= $mk['kode_prefix'] ?>" data-nama="<?= $mk['nama_prefix'] ?>">
                                <?= $mk['kode_prefix'] ?> (<?= $mk['nama_prefix'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: #666; margin-top: 5px; display: block;">
                        Kode Barang = Prefix yang dipilih.
                    </small>
                </div>

                <div class="form-group">
                    <label>Gambar Produk</label>
                    <div id="imagePreview" style="margin-bottom: 10px; display: none;">
                        <img src="" alt="Preview" style="max-width: 100px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                    <small>Biarkan kosong jika tidak ingin mengubah gambar. Max 500KB (JPG, PNG, GIF, WEBP)</small>
                </div>

                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" required placeholder="Contoh: Meja Makan Jati">
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>">
                                <?= $kat['nama_kategori'] ?>
                            </option>
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
                        <option value="Batang">Batang</option>
                        <option value="Lembar">Lembar</option>
                        <option value="Meter">Meter</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_jual">Harga Jual (Rp)</label>
                    <input type="number" id="harga_jual" name="harga_jual" class="form-control" min="0" required placeholder="0">
                </div>

                <div class="form-group">
                    <label for="stok">Stok Awal</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required placeholder="0">
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
        const assetUrl = "<?= ASSET_URL ?>";
        const modal = document.getElementById('barangModal');
        const form = document.getElementById('barangForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const imagePreview = document.getElementById('imagePreview');
        const imageTag = imagePreview.querySelector('img');

        function openModal(trigger) {
            modal.classList.add('show');
            
            // Check if string 'add' or element passed
            let mode = 'add';
            if (typeof trigger === 'object') {
                mode = trigger.dataset.mode;
            } else if (trigger === 'add') {
                mode = 'add';
            }

            if (mode === 'add') {
                modalTitle.textContent = "Tambah Barang Baru";
                form.action = baseUrl + "/barang/store";
                submitBtn.textContent = "Simpan Barang";
                form.reset();
                imagePreview.style.display = 'none';
            } else {
                modalTitle.textContent = "Edit Barang";
                const id = trigger.dataset.id;
                form.action = baseUrl + "/barang/update/" + id;
                submitBtn.textContent = "Update Barang";
                
                // Fill fields
                document.getElementById('nama_barang').value = trigger.dataset.nama;
                document.getElementById('harga_jual').value = trigger.dataset.harga;
                document.getElementById('stok').value = trigger.dataset.stok;
                document.getElementById('kategori_id').value = trigger.dataset.kategori;
                document.getElementById('supplier_id').value = trigger.dataset.supplier || "";
                document.getElementById('satuan').value = trigger.dataset.satuan;
                
                // Logic for Prefix/Kode Barang
                // Since Kode Barang == Prefix, we set the select to match the code
                const kode = trigger.dataset.kode;
                const prefixSelect = document.getElementById('kode_prefix');
                prefixSelect.value = kode;
                
                // Image Preview
                const gambar = trigger.dataset.gambar;
                if (gambar) {
                    imageTag.src = assetUrl + "/uploads/products/" + gambar;
                    imagePreview.style.display = 'block';
                } else {
                    imagePreview.style.display = 'none';
                }
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







<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
