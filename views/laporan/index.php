<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <h1>Laporan Penjualan</h1>

        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
            <form method="GET" action="<?= BASE_URL ?>/laporan">
                <div class="report-filter">
                    <div>
                        <label>Tipe Laporan</label>
                        <select name="type" class="form-control" onchange="this.form.submit()">
                            <option value="harian" <?= $filterType == 'harian' ? 'selected' : '' ?>>Harian</option>
                            <option value="bulanan" <?= $filterType == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="tahunan" <?= $filterType == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                    <div>
                        <label>Filter</label>
                        <?php if ($filterType == 'harian'): ?>
                            <input type="date" name="value" class="form-control" value="<?= $filterValue ?>">
                        <?php elseif ($filterType == 'bulanan'): ?>
                            <input type="month" name="value" class="form-control" value="<?= $filterValue ?>">
                        <?php elseif ($filterType == 'tahunan'): ?> <!-- Note: Helper for Year picker usually number input -->
                            <input type="number" name="value" class="form-control" value="<?= $filterValue ?>" min="2000" max="2099">
                        <?php endif; ?>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="report-container">
            <div class="report-summary">
                <h3>Ringkasan</h3>
                <p>Total Transaksi: <strong><?= $totalTransaksi ?></strong></p>
                <p>Total Omzet: <strong><?= formatRupiah($totalOmzet) ?></strong></p>
                
                <h4 style="margin-top: 20px;">Export</h4>
                <div style="display: flex; gap: 5px;">
                    <a href="<?= BASE_URL ?>/export/download?type=<?= $filterType ?>&value=<?= $filterValue ?>&export_type=csv" target="_blank" class="btn btn-success" style="font-size: 0.8rem;">CSV</a>
                    <a href="<?= BASE_URL ?>/export/download?type=<?= $filterType ?>&value=<?= $filterValue ?>&export_type=excel" target="_blank" class="btn btn-success" style="font-size: 0.8rem;">Excel</a>
                    <a href="<?= BASE_URL ?>/export/download?type=<?= $filterType ?>&value=<?= $filterValue ?>&export_type=pdf" target="_blank" class="btn btn-danger" style="font-size: 0.8rem;">PDF</a>
                </div>
            </div>
            <div class="report-chart">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Transaksi</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporan)): ?>
                    <tr><td colspan="5" align="center">Tidak ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach ($laporan as $row): ?>
                        <tr>
                            <td><?= $row['no_transaksi'] ?></td>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= formatRupiah($row['total_harga']) ?></td>
                            <td><?= formatRupiah($row['bayar']) ?></td>
                            <td><?= formatRupiah($row['kembalian']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                label: 'Omzet Penjualan',
                data: <?= $values ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.5)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
