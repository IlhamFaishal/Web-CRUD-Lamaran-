<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Data Barang</h1>
            <button onclick="openModal()" class="btn btn-primary">Tambah Barang</button>
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
                        <td><?= $brg['satuan'] ?></td>
                        <td><?= formatRupiah($brg['harga_jual']) ?></td>
                        <td><?= $brg['stok'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/barang/edit/<?= $brg['id_barang'] ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                            <a href="<?= BASE_URL ?>/barang/delete/<?= $brg['id_barang'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Barang -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 style="margin-top: 0;">Tambah Barang</h2>
            <form action="<?= BASE_URL ?>/barang/store" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="modal_gambar">Gambar Produk</label>
                    <input type="file" id="modal_gambar" name="gambar" class="form-control" accept="image/*">
                    <small>Format: JPG, PNG, GIF (Max 2MB)</small>
                </div>
                <div class="form-group">
                    <label for="modal_kode">Kode Barang (Unik)</label>
                    <input type="text" id="modal_kode" name="kode_barang" class="form-control" required placeholder="Contoh: BRG001">
                </div>
                <div class="form-group">
                    <label for="modal_nama">Nama Barang</label>
                    <input type="text" id="modal_nama" name="nama_barang" class="form-control" required placeholder="Contoh: Meja Makan Jati">
                </div>
                <div class="form-group">
                    <label for="modal_kategori">Kategori</label>
                    <select id="modal_kategori" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_satuan">Satuan</label>
                    <select id="modal_satuan" name="satuan" class="form-control" required>
                        <option value="Unit">Unit</option>
                        <option value="Set">Set</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Buah">Buah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_harga">Harga Jual</label>
                    <input type="number" id="modal_harga" name="harga_jual" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modal_stok">Stok Awal</label>
                    <input type="number" id="modal_stok" name="stok" class="form-control" required>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()" style="flex: 1; background: #ccc; border: none; color: #333;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            var modal = document.getElementById('addModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
