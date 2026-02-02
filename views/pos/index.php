<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div class="pos-container">
            <!-- LEFT: Product List -->
            <div class="pos-left">
                <div class="header-actions" style="margin-bottom: 20px; flex-wrap: wrap;">
                    <h2>Master Barang</h2>
                    
                    <form method="GET" action="<?= BASE_URL ?>/pos" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1; justify-content: flex-end;">
                        <!-- Category Filter -->
                        <select name="cat" class="form-control" style="width: 150px; padding: 8px;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= $currentCat == $kat['id_kategori'] ? 'selected' : '' ?>>
                                    <?= $kat['nama_kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Sort Filter -->
                        <select name="sort" class="form-control" style="width: 150px; padding: 8px;" onchange="this.form.submit()">
                            <option value="">Urutan Default</option>
                            <option value="price_asc" <?= $currentSort == 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                            <option value="price_desc" <?= $currentSort == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                            <option value="name_asc" <?= $currentSort == 'name_asc' ? 'selected' : '' ?>>Nama A-Z</option>
                        </select>

                        <input type="text" name="q" placeholder="Cari Kode / Nama..." value="<?= $keyword ?>" style="padding: 8px; width: 200px; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 15px;">Cari</button>
                    </form>
                </div>

                <div class="product-grid">
                    <?php foreach ($barang as $brg): ?>
                        <div class="product-card">
                            <div class="product-img-container">
                                <?php if ($brg['gambar']): ?>
                                    <img src="<?= ASSET_URL ?>/uploads/products/<?= $brg['gambar'] ?>" alt="<?= $brg['nama_barang'] ?>" class="product-img">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                                <span class="badge" style="position: absolute; top: 10px; right: 10px; background: <?= $brg['stok'] > 5 ? '#2ecc71' : '#e74c3c' ?>; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    Stok: <?= $brg['stok'] ?>
                                </span>
                            </div>
                            <div class="product-details">
                                <h4 class="product-title" title="<?= $brg['nama_barang'] ?>">
                                    <?= $brg['nama_barang'] ?>
                                    <?php if (!empty($brg['nama_supplier'])): ?>
                                        <br><small style="font-size: 0.7em; color: #555;">[<?= $brg['nama_supplier'] ?>]</small>
                                    <?php endif; ?>
                                </h4>
                                <p class="product-price"><?= formatRupiah($brg['harga_jual']) ?></p>
                                
                                <?php if ($brg['stok'] > 0): ?>
                                    <a href="<?= BASE_URL ?>/pos/addToCart/<?= $brg['id_barang'] ?>" class="btn btn-primary btn-block">+ Keranjang</a>
                                <?php else: ?>
                                    <button class="btn btn-danger btn-block" disabled style="opacity: 0.7;">Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT: Cart & Checkout -->
            <div class="pos-right">
                <h3>Keranjang</h3>
                <hr>
                
                <?php if (empty($cart)): ?>
                    <p style="text-align: center; color: #999;">Keranjang Kosong</p>
                <?php else: ?>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table" style="font-size: 0.9rem;">
                            <?php foreach ($cart as $id => $item): ?>
                            <tr>
                                <td>
                                    <strong><?= $item['name'] ?></strong><br>
                                    <small><?= formatRupiah($item['price']) ?></small>
                                </td>
                                <td width="60">
                                    <form action="<?= BASE_URL ?>/pos/updateQty" method="POST" style="display: flex; justify-content: center;">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" class="cart-qty-input">
                                        <button type="submit" style="display: none;"></button>
                                    </form>
                                </td>
                                <td align="right"><?= formatRupiah($item['subtotal']) ?></td>
                                <td width="30">
                                    <a href="<?= BASE_URL ?>/pos/deleteItem/<?= $id ?>" class="btn-remove" title="Hapus">&times;</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="total-display">
                    Total: <br><span style="font-size: 1.2em;"><?= formatRupiah($totalCart) ?></span>
                </div>

                <form action="<?= BASE_URL ?>/pos/checkout" method="POST" onsubmit="return confirm('Proses Transaksi?')">
                    <div class="form-group">
                        <label>Bayar (Rp)</label>
                        <input type="number" name="bayar" class="form-control" required min="<?= $totalCart ?>" placeholder="Input Nominal">
                    </div>
                    <?php if (!empty($cart)): ?>
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.2rem; padding: 15px;">BAYAR</button>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/pos/reset" class="btn btn-danger" style="display: block; text-align: center; margin-top: 10px;">Reset</a>
                </form>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
