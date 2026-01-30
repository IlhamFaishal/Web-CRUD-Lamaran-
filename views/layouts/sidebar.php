        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>POS BESI KAYU</h3>
                <small>Welcome, <?= currentUser()['username'] ?></small>
            </div>

            <ul class="components">
                <li class="<?= (strpos($_GET['url'] ?? '', 'kategori') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/kategori">Master Kategori</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'barang') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/barang">Master Barang</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'pos') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/pos">Transaksi POS</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'laporan') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/laporan">Laporan</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/auth/logout" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c;">Logout</a>
                </li>
            </ul>
        </nav>
