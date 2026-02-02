        <!-- Hamburger Menu Button (Mobile Only) -->
        <button class="menu-toggle" id="menuToggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Sidebar Overlay (Mobile Only) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>
        
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>POS BESI KAYU</h3>
                <small>Welcome, <?= currentUser()['username'] ?></small>
            </div>

            <ul class="components">
                <li class="<?= (strpos($_GET['url'] ?? '', 'kode-supplier') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/kode-supplier">Supplier</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'kategori') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/kategori">Master Kategori</a>
                </li>
                <!-- Master Barang Menu with Dropdown -->
                <li class="<?= (strpos($_GET['url'] ?? '', 'barang') !== false || strpos($_GET['url'] ?? '', 'master-kode-barang') !== false) ? 'active' : '' ?>" style="position: relative;">
                    <a href="#masterBarangSubmenu" data-toggle="collapse" aria-expanded="false" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <span>Master Barang</span>
                        <span style="font-size: 0.8rem;">▼</span>
                    </a>
                    <ul class="collapse list-unstyled" id="masterBarangSubmenu" style="padding-left: 20px; margin-top: 5px;">
                        <li class="<?= (strpos($_GET['url'] ?? '', 'barang') !== false && strpos($_GET['url'] ?? '', 'create') === false && strpos($_GET['url'] ?? '', 'edit') === false) ? 'active' : '' ?>">
                            <a href="<?= BASE_URL ?>/barang" style="padding: 8px 15px; font-size: 0.9rem;">Data Barang</a>
                        </li>
                        <li class="<?= (strpos($_GET['url'] ?? '', 'master-kode-barang') !== false) ? 'active' : '' ?>">
                            <a href="<?= BASE_URL ?>/master-kode-barang" style="padding: 8px 15px; font-size: 0.9rem;">Kode Barang</a>
                        </li>
                    </ul>
                </li>
                
                <li class="<?= (strpos($_GET['url'] ?? '', 'pos') !== false && strpos($_GET['url'] ?? '', 'structure') === false && strpos($_GET['url'] ?? '', 'history') === false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/pos">Transaksi POS</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'history') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/pos/history">Riwayat Transaksi</a>
                </li>
                <li class="<?= (strpos($_GET['url'] ?? '', 'laporan') !== false) ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/laporan">Laporan</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/auth/logout" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c;">Logout</a>
                </li>
            </ul>
            
            <script>
            // Mobile menu toggle
            function toggleMobileMenu() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
            
            // Dropdown and navigation management
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.querySelector('[data-toggle="collapse"]');
                const submenu = document.getElementById('masterBarangSubmenu');
                const sidebarLinks = document.querySelectorAll('.sidebar a');
                
                // Initially hide submenu
                submenu.style.display = 'none';
                submenu.classList.remove('show');
                
                // Only auto-expand if currently on a submenu page
                if (document.querySelector('#masterBarangSubmenu .active')) {
                    submenu.style.display = 'block';
                    submenu.classList.add('show');
                }
                
                // Toggle dropdown on click
                if (toggle && submenu) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                            submenu.classList.remove('show');
                        } else {
                            submenu.style.display = 'block';
                            submenu.classList.add('show');
                        }
                    });
                }
                
                // Close dropdown when clicking other menu items
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // If it's not the dropdown toggle and not a submenu item
                        if (!this.hasAttribute('data-toggle') && !this.closest('#masterBarangSubmenu')) {
                            // Close the dropdown
                            if (submenu) {
                                submenu.style.display = 'none';
                                submenu.classList.remove('show');
                            }
                            
                            // Close mobile menu if on mobile
                            if (window.innerWidth <= 768) {
                                toggleMobileMenu();
                            }
                        } else if (this.closest('#masterBarangSubmenu')) {
                            // If clicking submenu item, close mobile menu
                            if (window.innerWidth <= 768) {
                                toggleMobileMenu();
                            }
                        }
                    });
                });
            });
            </script>
        </nav>
