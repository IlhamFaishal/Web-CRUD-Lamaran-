<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h1>Laporan Kartu Hutang</h1>

        <!-- Filter Period -->
        <div class="card mb-4" style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
            <!-- Dynamic Filter Form -->
            <form method="GET" action="<?= BASE_URL ?>/laporan" id="filterForm" style="margin-bottom: 0;">
                <input type="hidden" name="filter_submit" value="1">
                
                <!-- Report Type Selection -->
                <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Jenis Laporan:</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="report_type" value="per_supplier" 
                                   <?= (!isset($_GET['report_type']) || $_GET['report_type'] == 'per_supplier') ? 'checked' : '' ?>
                                   onchange="toggleReportType()" style="margin-right: 8px;">
                            <span>Per Supplier</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="report_type" value="rekapitulasi" 
                                   <?= (isset($_GET['report_type']) && $_GET['report_type'] == 'rekapitulasi') ? 'checked' : '' ?>
                                   onchange="toggleReportType()" style="margin-right: 8px;">
                            <span>Supplier (Rekapitulasi)</span>
                        </label>
                    </div>
                </div>
                
                <!-- Per Supplier Filters -->
                <div id="perSupplierFilters" style="display: none;">
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Pilih Supplier:</label>
                            <select name="supplier_id" class="form-control" style="width: 100%;" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach ($allSuppliers as $s): ?>
                                    <option value="<?= $s['id_kode_supplier'] ?>" <?= $selectedSupplier == $s['id_kode_supplier'] ? 'selected' : '' ?>>
                                        <?= $s['nama_supplier'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Dari Tanggal:</label>
                            <input type="date" name="start" value="<?= $startDate ?>" class="form-control" style="width: 100%;">
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Sampai Tanggal:</label>
                            <input type="date" name="end" value="<?= $endDate ?>" class="form-control" style="width: 100%;">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" style="padding: 6px 20px; height: 38px;">
                                <i class="fa fa-search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Rekapitulasi Filters ("Supplier" Option) -->
                <div id="rekapitulasiFilters" style="display: none;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <label style="font-weight: 500; margin-bottom: 0;">Pilih Periode:</label>
                        <div class="btn-group">
                            <!-- Helper links that act as submit buttons for specific periods -->
                            <a href="<?= BASE_URL ?>/laporan?filter_submit=1&report_type=rekapitulasi&period=today" 
                               class="btn <?= (isset($_GET['period']) && $_GET['period'] == 'today') ? 'btn-primary' : 'btn-default' ?>" 
                               style="border: 1px solid #ccc;">Hari Ini</a>
                               
                            <a href="<?= BASE_URL ?>/laporan?filter_submit=1&report_type=rekapitulasi&period=7days" 
                               class="btn <?= (isset($_GET['period']) && $_GET['period'] == '7days') ? 'btn-primary' : 'btn-default' ?>" 
                               style="border: 1px solid #ccc;">7 Hari Terakhir</a>
                               
                            <a href="<?= BASE_URL ?>/laporan?filter_submit=1&report_type=rekapitulasi&period=30days" 
                               class="btn <?= (isset($_GET['period']) && $_GET['period'] == '30days') ? 'btn-primary' : 'btn-default' ?>" 
                               style="border: 1px solid #ccc;">30 Hari Terakhir</a>
                        </div>
                    </div>
                </div>
            </form>
            
            <script>
            function toggleReportType() {
                const reportType = document.querySelector('input[name="report_type"]:checked').value;
                const perSupplierDiv = document.getElementById('perSupplierFilters');
                const rekapDiv = document.getElementById('rekapitulasiFilters');
                const supplierSelect = document.querySelector('select[name="supplier_id"]');
                
                if (reportType === 'per_supplier') {
                    perSupplierDiv.style.display = 'block';
                    rekapDiv.style.display = 'none';
                    supplierSelect.setAttribute('required', 'required');
                } else {
                    perSupplierDiv.style.display = 'none';
                    rekapDiv.style.display = 'block';
                    supplierSelect.removeAttribute('required');
                }
            }
            // Init on load
            document.addEventListener("DOMContentLoaded", toggleReportType);
            </script>
        </div>
        
        <?php if (!$filterSubmitted): ?>
        <!-- Empty State Message -->
        <div style="text-align: center; padding: 60px 20px; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px;">
            <i class="fa fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">Cari Data yang Anda Inginkan</h3>
            <p style="color: #999;">Pilih supplier dan periode, lalu klik tombol "Tampilkan" untuk melihat laporan</p>
        </div>
        <?php else: ?>
        
        <!-- Export Buttons -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px; justify-content: space-between; align-items: center;">
            <div>
                <button class="btn btn-info" onclick="document.getElementById('modalSaldo').style.display='block'">+ Input Saldo Awal Tahunan</button>
                <button class="btn btn-warning" onclick="document.getElementById('modalBeli').style.display='block'">+ Input Pembelian (Test)</button>
            </div>
            <div style="display: flex; gap: 5px;">
                <button class="btn btn-danger" onclick="exportPDF()" title="Export PDF">
                    <i class="fa fa-file-pdf"></i> PDF
                </button>
                <button class="btn btn-success" onclick="exportExcel()" title="Export Excel">
                    <i class="fa fa-file-excel"></i> Excel
                </button>
                <button class="btn btn-primary" onclick="exportCSV()" title="Export CSV">
                    <i class="fa fa-file-csv"></i> CSV
                </button>
                <button class="btn" style="background: #0f9d58; color: white;" onclick="exportGoogleSheets()" title="Export to Google Sheets">
                    <i class="fa fa-table"></i> Sheets
                </button>
            </div>
        </div>

        <?php $flash = getFlashMessage(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'error' : 'success' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <!-- SECTION 1: REKAPITULASI -->
        <?php 
        $reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'per_supplier';
        // Only show Rekapitulasi if that report type is selected
        if ($reportType == 'rekapitulasi'): 
        ?>
        <div class="card mb-4" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div class="card-header" style="background: #f1c40f; padding: 10px 15px; font-weight: bold;">
                REKAPITULASI HUTANG DAGANG
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="margin: 0;">
                    <thead style="background: #eee;">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Supplier</th>
                            <th rowspan="2" style="vertical-align: middle;">Saldo Awal</th>
                            <th colspan="3" class="text-center">Pembelian</th>
                            <th rowspan="2" style="vertical-align: middle;">Pelunasan</th>
                            <th rowspan="2" style="vertical-align: middle;">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th>DPP</th>
                            <th>PPN (11%)</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): ?>
                        <tr>
                            <td><?= $row['supplier']['nama_supplier'] ?></td>
                            <td align="right"><?= formatRupiah($row['saldo_awal']) ?></td>
                            <td align="right"><?= formatRupiah($row['total_dpp']) ?></td>
                            <td align="right"><?= formatRupiah($row['total_ppn']) ?></td>
                            <td align="right"><?= formatRupiah($row['total_beli']) ?></td>
                            <td align="right"><?= formatRupiah($row['total_bayar']) ?></td>
                            <td align="right" style="font-weight: bold;"><?= formatRupiah($row['saldo_akhir']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot style="background: #eee; font-weight: bold;">
                        <tr>
                            <td>TOTAL</td>
                            <td align="right"><?= formatRupiah($totals['saldo_awal']) ?></td>
                            <td align="right"><?= formatRupiah($totals['dpp']) ?></td>
                            <td align="right"><?= formatRupiah($totals['ppn']) ?></td>
                            <td align="right"><?= formatRupiah($totals['beli']) ?></td>
                            <td align="right"><?= formatRupiah($totals['bayar']) ?></td>
                            <td align="right"><?= formatRupiah($totals['saldo_akhir']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- SECTION 2: DETAIL KARTU HUTANG -->
        <!-- Only show detail when specific supplier is selected, not for "all" -->
        <?php if ($selectedSupplier && $selectedSupplier != 'all'): ?>
        <?php foreach ($reportData as $row): ?>
        <div class="card mb-4" style="background: #fff; border: 1px solid #000; border-radius: 0; margin-top: 30px;">
            <div class="card-header" style="background: #f1c40f; padding: 10px 15px; font-weight: bold; border-bottom: 1px solid #000; color: #000;">
                <div style="display: flex; justify-content: space-between;">
                    <span>KARTU HUTANG: <?= strtoupper($row['supplier']['nama_supplier']) ?></span>
                    <span>Periode: <?= $startDate ?> s/d <?= $endDate ?></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" style="margin: 0; background: #fff; border: 1px solid #000;">
                    <thead class="text-center" style="background: #f8f9fa;">
                        <tr style="border-bottom: 1px solid #000;">
                            <th rowspan="2" style="vertical-align: middle; border: 1px solid #000;">Tanggal</th>
                            <th rowspan="2" style="vertical-align: middle; border: 1px solid #000;">Barang</th>
                            <th colspan="5" style="border: 1px solid #000;">Pembelian</th>
                            <th rowspan="2" style="vertical-align: middle; border: 1px solid #000;">Pelunasan</th>
                            <th rowspan="2" style="vertical-align: middle; border: 1px solid #000;">Saldo</th>
                        </tr>
                        <tr style="border-bottom: 1px solid #000;">
                            <th style="border: 1px solid #000;">Unit</th>
                            <th style="border: 1px solid #000;">@Harga</th>
                            <th style="border: 1px solid #000;">DPP</th>
                            <th style="border: 1px solid #000;">PPN</th>
                            <th style="border: 1px solid #000;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Awal Row -->
                        <tr>
                            <td style="border: 1px solid #000;"><?= date('d/m/Y', strtotime($startDate)) ?></td>
                            <td style="border: 1px solid #000;">Saldo Awal</td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td align="right" style="border: 1px solid #000;">
                                <?= $row['saldo_awal'] == 0 ? '0' : number_format($row['saldo_awal'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        
                        <?php 
                        $runningBalance = $row['saldo_awal'];
                        // Show Mixed History (Purchases & Payments)
                        if (isset($row['history']) && is_array($row['history'])):
                            foreach ($row['history'] as $item): 
                                // Calculate Balance
                                if ($item['type'] == 'purchase') {
                                    $runningBalance -= $item['total_harga'];
                                } else {
                                    $runningBalance += $item['jumlah_bayar'];
                                }
                        ?>
                        <tr>
                            <td style="border: 1px solid #000;"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                            
                            <!-- ITEM / KETERANGAN -->
                            <td style="border: 1px solid #000;">
                                <?php if ($item['type'] == 'purchase'): ?>
                                    <?= $item['nama_barang'] ?>
                                    <br><small style="color: #666;">(Live Stok: <?= $item['stok'] ?>)</small>
                                    <a href="#" onclick='editPembelian(<?= json_encode($item) ?>)' class="text-primary ml-1" title="Edit"><small><i class="fa fa-edit"></i></small></a>
                                    <a href="#" onclick="deletePembelian(<?= $item['id_pembelian'] ?>)" class="text-danger ml-1" title="Hapus"><small><i class="fa fa-trash"></i></small></a>
                                <?php else: ?>
                                    <em>Pelunasan</em> <?= !empty($item['keterangan']) ? '('.$item['keterangan'].')' : '' ?>
                                <?php endif; ?>
                            </td>

                            <!-- PURCHASE COLUMNS -->
                            <?php if ($item['type'] == 'purchase'): ?>
                                <td align="center" style="border: 1px solid #000;"><?= $item['qty'] ?></td>
                                <td align="right" style="border: 1px solid #000;"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                <td align="right" style="border: 1px solid #000;"><?= number_format($item['dpp'], 0, ',', '.') ?></td>
                                <td align="right" style="border: 1px solid #000;"><?= number_format($item['ppn'], 0, ',', '.') ?></td>
                                <td align="right" style="border: 1px solid #000;"><?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                <td align="right" style="border: 1px solid #000;">-</td>
                            <?php else: ?>
                                <!-- Empty Purchase Cols for Payment Row -->
                                <td style="border: 1px solid #000;"></td>
                                <td style="border: 1px solid #000;"></td>
                                <td style="border: 1px solid #000;"></td>
                                <td style="border: 1px solid #000;"></td>
                                <td style="border: 1px solid #000;"></td>
                                <!-- Payment Col -->
                                <td align="right" style="border: 1px solid #000;"><?= number_format($item['jumlah_bayar'], 0, ',', '.') ?></td>
                            <?php endif; ?>

                            <!-- SALDO -->
                            <td align="right" style="border: 1px solid #000;">
                                <?= $runningBalance == 0 ? '0' : number_format($runningBalance, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        endif;
                        ?>
                    </tbody>
                    <tfoot style="background: #eee;">
                        <!-- Column Totals -->
                        <tr>
                            <td colspan="2" align="right" style="border: 1px solid #000;"><strong>Total</strong></td>
                            <td align="center" style="border: 1px solid #000;">
                                <strong><?= number_format(array_sum(array_column($row['purchases'], 'qty')), 0, ',', '.') ?></strong>
                            </td>
                            <td style="border: 1px solid #000;"></td> <!-- Harga -->
                            <td align="right" style="border: 1px solid #000;"><strong><?= number_format($row['total_dpp'], 0, ',', '.') ?></strong></td>
                            <td align="right" style="border: 1px solid #000;"><strong><?= number_format($row['total_ppn'], 0, ',', '.') ?></strong></td>
                            <td align="right" style="border: 1px solid #000;"><strong><?= number_format($row['total_beli'], 0, ',', '.') ?></strong></td>
                            <td align="right" style="border: 1px solid #000;">
                                <strong><?= $row['total_bayar'] == 0 ? '-' : number_format($row['total_bayar'], 0, ',', '.') ?></strong>
                            </td>
                            <td align="right" style="border: 1px solid #000;"><strong>
                                <?= $row['saldo_akhir'] == 0 ? '0' : number_format($row['saldo_akhir'], 0, ',', '.') ?>
                            </strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?> <!-- End of specific supplier check -->
        
        <?php endif; ?>

    </div>

    <!-- MODAL SALDO AWAL -->
    <div id="modalSaldo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background:#fff; width:90%; max-width:450px; padding:25px; border-radius:8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <h3>Input Saldo Awal</h3>
            <form action="<?= BASE_URL ?>/laporan/setSaldo" method="POST">
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" value="<?= date('Y') ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control" required>
                        <?php foreach ($reportData as $row): ?>
                            <option value="<?= $row['supplier']['id_kode_supplier'] ?>"><?= $row['supplier']['nama_supplier'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Saldo Awal (Rp)</label>
                    <input type="number" name="saldo_awal" class="form-control" required>
                    <small class="text-danger">Gunakan tanda minus (-) jika Saldo adalah Hutang. Contoh: -1000000</small>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalSaldo').style.display='none'">Batal</button>
            </form>
        </div>
    </div>

    <!-- MODAL INPUT BELI (Manual JS with Bootstrap Style) -->
    <div class="modal fade show" id="modalBeli" tabindex="-1" role="dialog" aria-hidden="true" style="display:none; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document" style="margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Input Pembelian (Test)</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= BASE_URL ?>/laporan/debit" method="POST">
                    <div class="modal-body">
                         <!-- Hidden ID for Edit -->
                        <input type="hidden" name="id_pembelian" id="id_pembelian">
                        
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach ($allSuppliers as $s): ?>
                                    <option value="<?= $s['id_kode_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Dynamic Item Selection -->
                        <div class="form-group">
                            <label>Barang (Item)</label>
                            <select name="barang_id" id="barang_id" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= $b['id_barang'] ?>" 
                                            data-supplier-id="<?= $b['supplier_id'] ?>"
                                            data-harga="<?= $b['harga_jual'] ?>"
                                            data-stok="<?= $b['stok'] ?>">
                                        <?= $b['nama_barang'] ?> (<?= $b['kode_barang'] ?>) 
                                        <?php if ($b['nama_supplier']): ?>
                                            - [<?= $b['nama_supplier'] ?>]
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah (Unit)</label>
                                    <input type="number" name="qty" id="qty" value="1" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@Harga (DPP/Satuan)</label>
                                    <input type="number" name="harga_satuan" id="harga_satuan" class="form-control" placeholder="Contoh: 1500" required>
                                    <small class="text-muted">Harga sebelum PPN</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <small id="stok_info" style="font-weight: bold; color: #2980b9;"></small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="font-size: 0.9em; padding: 10px;">
                            <strong>Rumus:</strong><br>
                            DPP = Unit x Harga<br>
                            PPN = DPP x 11%<br>
                            Total = DPP + PPN
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
// Manual Modal Control because Bootstrap JS is missing
function showModal() {
    var modal = document.getElementById('modalBeli');
    modal.style.display = 'block';
    modal.classList.add('show');
    
    document.getElementById('modalTitle').innerText = 'Input Pembelian (Test)';
    document.getElementById('id_pembelian').value = ''; 
    document.getElementById('qty').value = '1';
    document.getElementById('harga_satuan').value = '';
    document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
    
    // Trigger supplier change to filter items
    filterItems();
}

function editPembelian(data) {
    var modal = document.getElementById('modalBeli');
    modal.style.display = 'block';
    modal.classList.add('show');
    
    document.getElementById('modalTitle').innerText = 'Edit Pembelian';
    
    document.getElementById('id_pembelian').value = data.id_pembelian;
    document.getElementById('tanggal').value = data.tanggal;
    document.getElementById('supplier_id').value = data.supplier_id;
    
    // Filter items first, then select
    filterItems();
    
    document.getElementById('barang_id').value = data.barang_id;
    document.getElementById('qty').value = data.qty;
    document.getElementById('harga_satuan').value = data.harga_satuan;
}

function closeModal() {
    var modal = document.getElementById('modalBeli');
    modal.style.display = 'none';
    modal.classList.remove('show');
}

function deletePembelian(id) {
    if(confirm('Yakin ingin menghapus item ini? Stok akan dikembalikan.')) {
        window.location.href = '<?= BASE_URL ?>/laporan/delete_pembelian/' + id;
    }
}

// Filter Items based on Supplier
document.getElementById('supplier_id').addEventListener('change', filterItems);

function filterItems() {
    var supplierId = document.getElementById('supplier_id').value;
    var itemSelect = document.getElementById('barang_id');
    var options = itemSelect.querySelectorAll('option');
    var firstVisible = '';
    
    options.forEach(function(opt) {
        if (opt.value === '') return; // Skip placeholder
        
        var itemSupplier = opt.getAttribute('data-supplier-id');
        
        // If item has no supplier (general) or matches selected supplier
        // Note: itemSupplier might be empty string/null if general
        if (!itemSupplier || itemSupplier == supplierId) {
            opt.style.display = 'block';
            if (!firstVisible) firstVisible = opt.value;
        } else {
            opt.style.display = 'none';
        }
    });

    // Reset selection if current selection is hidden
    var currentSelected = itemSelect.options[itemSelect.selectedIndex];
    if (currentSelected.style.display === 'none') {
        itemSelect.value = firstVisible;
    }
    
    // Trigger price/qty update after filtering
    updatePriceAndQty();
}

// Auto-fill Supplier, Price and Qty when Item changes
document.getElementById('barang_id').addEventListener('change', function() {
    updatePriceAndQty();
    autoSelectSupplier();
});

function updatePriceAndQty() {
    var itemSelect = document.getElementById('barang_id');
    var selectedOption = itemSelect.options[itemSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value !== '') {
        // Price
        var price = selectedOption.getAttribute('data-harga');
        if (price) {
            document.getElementById('harga_satuan').value = price;
        }
        
        // Qty (From Stock)
        var stok = selectedOption.getAttribute('data-stok');
        
        if (stok !== null && stok !== '') {
            document.getElementById('stok_info').innerText = 'Stok Saat Ini: ' + stok;
            if(document.getElementById('qty').value == '' || document.getElementById('qty').value == '0') {
                 document.getElementById('qty').value = '1';
            }
        } else {
             document.getElementById('stok_info').innerText = '';
        }
    }
}

function autoSelectSupplier() {
    var itemSelect = document.getElementById('barang_id');
    var selectedOption = itemSelect.options[itemSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value !== '') {
        var supplierId = selectedOption.getAttribute('data-supplier-id');
        var supplierSelect = document.getElementById('supplier_id');
        
        // Only change supplier if the item matches a specific supplier
        // and that supplier is different from current selection
        if (supplierId && supplierSelect.value != supplierId) {
             supplierSelect.value = supplierId;
             // We need to re-filter items to ensure consistency, 
             // BUT we must be careful not to hide the item we just selected!
             // Actually, if we switch supplier, the filterItems() will run and might hide current item 
             // if logic is strict. But here supplierId IS matching item, so it should stay visible.
             filterItems(); 
             // Re-set item value in case filterItems reset it (though logic tries to keep it)
             itemSelect.value = selectedOption.value;
        }
    }
}

// Export Functions
function exportPDF() {
    alert('Fitur Export PDF akan segera tersedia. Silakan gunakan Print to PDF dari browser untuk sementara.');
    window.print();
}

function exportExcel() {
    // Convert table to Excel format with text formatting preservation
    var tables = document.querySelectorAll('table');
    var htmlContent = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    htmlContent += '<head><meta charset="UTF-8">';
    htmlContent += '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
    
    tables.forEach(function(table, index) {
        htmlContent += '<x:ExcelWorksheet><x:Name>Sheet' + (index + 1) + '</x:Name>';
        htmlContent += '<x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet>';
    });
    
    htmlContent += '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    htmlContent += '<style>td, th { border: 1px solid black; } .text-right { text-align: right; } .text-center { text-align: center; }</style>';
    htmlContent += '</head><body>';
    
    tables.forEach(function(table) {
        // Clone table to avoid modifying the DOM
        var clone = table.cloneNode(true);
        
        // CLEANUP: Remove web-specific attributes and styles to ensure a clean Excel export
        clone.removeAttribute('class');
        clone.style = ''; // Clear table-level inline styles
        
        // Clean all rows and cells
        var allElements = clone.querySelectorAll('tr, td, th, thead, tbody, tfoot');
        allElements.forEach(function(el) {
            el.removeAttribute('class'); // Remove Bootstrap classes (table-striped, etc)
            
            // Keep text alignment if important, but remove borders/backgrounds
            // We need to preserve the text alignment logic we used for number detection if we rely on it,
            // OR we rely on the helper we just added.
            // Actually, we are about to process cells for numbers below, so let's save the alignment info first if needed,
            // or just rely on the content detection which is safer.
            
            // Reset inline style but keep mso-number-format if we set it (we haven't set it yet in this flow)
            // Ideally, we clear the style property but we need to check alignment for the number detection logic.
        });
        
        var cells = clone.querySelectorAll('td, th');
        cells.forEach(function(cell) {
             // Heuristic for alignment from web style
            var align = cell.getAttribute('align') || cell.style.textAlign;
            var text = cell.innerText.trim();
            
            // Clear inline styles now that we grabbed alignment
            cell.style.cssText = ''; 
            cell.removeAttribute('style');

            // Apply Excel-specific Styling
            // 1. Borders (will be applied via global style, but inline ensures it sticks)
            cell.style.border = '1px solid #000';
            
            // 2. Number Detection logic (Same as before)
            var isCurrency = /^-?[\d.]+(?:,[\d]+)?$/.test(text);
            
            if ((align === 'right' || cell.classList.contains('text-right')) && isCurrency) {
                var cleanNum = text.replace(/\./g, '').replace(',', '.');
                var num = parseFloat(cleanNum);
                
                if (!isNaN(num)) {
                    cell.innerText = num; 
                    cell.style.cssText += 'mso-number-format:"\\#\\,\\#\\#0"; border: 1px solid #000;';
                } else {
                    cell.style.cssText += 'mso-number-format:"\@"; border: 1px solid #000;';
                }
            } else {
                cell.style.cssText += 'mso-number-format:"\@"; border: 1px solid #000;';
            }
            
            // Restore center alignment if title keys or short text
            if (align === 'center') {
                cell.style.textAlign = 'center';
            }
        });

        // Get context from the visible card header
        // The table is inside .table-responsive, which is inside .card
        // We need to find the specific card header associated with this table
        // Since we are iterating tables, we can find the closest card
        var originalTable = table;
        var card = originalTable.closest('.card');
        var cardHeader = card ? card.querySelector('.card-header') : null;
        
        var supplierName = '-';
        var periodText = '-';

        if (cardHeader) {
            var spans = cardHeader.querySelectorAll('span');
            if (spans.length >= 2) {
                // Format: "KARTU HUTANG: SUPPLIER 1" -> we want just "SUPPLIER 1"
                var rawSupplier = spans[0].innerText; 
                supplierName = rawSupplier.replace('KARTU HUTANG:', '').trim();
                periodText = spans[1].innerText.replace('Periode:', 'periode').trim().toLowerCase();
            } else if (cardHeader.innerText.includes('REKAPITULASI')) {
                 supplierName = 'REKAPITULASI HUTANG DAGANG';
                 // Try to get period from filter if possible, or leave blank
            }
        }

        // Create new header rows
        var thead = clone.querySelector('thead');
        if (!thead) {
            thead = document.createElement('thead');
            clone.insertBefore(thead, clone.firstChild);
        }

        // Calculate colspan based on max cells in first row
        var colCount = 0;
        var firstRow = clone.querySelector('tr');
        if (firstRow) {
            var inputCells = firstRow.querySelectorAll('th, td');
            inputCells.forEach(function(c) {
                var cs = parseInt(c.getAttribute('colspan')) || 1;
                colCount += cs;
            });
        }
        // Fallback if calculation fails
        if (colCount < 5) colCount = 9; 

        // Row 1: Title (Yellow)
        var row1 = document.createElement('tr');
        row1.innerHTML = `<th colspan="${colCount}" style="background: #ffff00; font-weight: bold; text-align: center; border: 1px solid #000;">halaman kartu hutang</th>`;
        thead.insertBefore(row1, thead.firstChild);

        // Row 2: Supplier
        var row2 = document.createElement('tr');
        row2.innerHTML = `<th colspan="${colCount}" style="text-align: center; border: 1px solid #000; border-top: none;">${supplierName.toLowerCase()}</th>`;
        thead.insertBefore(row2, row1.nextSibling);

        // Row 3: Period
        var row3 = document.createElement('tr');
        row3.innerHTML = `<th colspan="${colCount}" style="text-align: center; border: 1px solid #000; border-top: none;">${periodText}</th>`;
        thead.insertBefore(row3, row2.nextSibling);
        
        htmlContent += clone.outerHTML;
        htmlContent += '<br>';
    });
    
    htmlContent += '</body></html>';
    
    var blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Laporan_Kartu_Hutang_' + new Date().toISOString().split('T')[0] + '.xls';
    a.click();
}

function exportCSV() {
    var csv = [];
    var tables = document.querySelectorAll('table');
    
    tables.forEach(function(table) {
        var rows = table.querySelectorAll('tr');
        rows.forEach(function(row) {
            var cols = row.querySelectorAll('td, th');
            var csvRow = [];
            cols.forEach(function(col) {
                csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
            });
            csv.push(csvRow.join(','));
        });
        csv.push(''); // Empty line between tables
    });
    
    var csvContent = csv.join('\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Laporan_Kartu_Hutang_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}

function exportGoogleSheets() {
    alert('Untuk export ke Google Sheets:\n1. Download file Excel terlebih dahulu\n2. Buka Google Sheets\n3. File > Import > Upload file Excel yang sudah didownload');
    exportExcel();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
