<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="content">
        <!-- Header with Title and Export Buttons -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <h1 style="margin: 0;">Riwayat Transaksi</h1>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button class="btn btn-danger" onclick="exportPDF()" title="Export PDF" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fa fa-file-pdf"></i> PDF
                </button>
                <button class="btn btn-success" onclick="exportExcel()" title="Export Excel" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fa fa-file-excel"></i> Excel
                </button>
                <button class="btn btn-primary" onclick="exportCSV()" title="Export CSV" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fa fa-file-csv"></i> CSV
                </button>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div style="margin-bottom: 25px; display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/pos/history?filter=today" 
               class="btn <?= ($currentFilter == 'today') ? 'btn-primary' : 'btn-default' ?>" 
               style="padding: 8px 16px; font-size: 14px; border: 1px solid #ccc; text-decoration: none;">
               Hari Ini
            </a>
            <a href="<?= BASE_URL ?>/pos/history?filter=7days" 
               class="btn <?= ($currentFilter == '7days') ? 'btn-primary' : 'btn-default' ?>" 
               style="padding: 8px 16px; font-size: 14px; border: 1px solid #ccc; text-decoration: none;">
               7 Hari Terakhir
            </a>
            <a href="<?= BASE_URL ?>/pos/history?filter=30days" 
               class="btn <?= ($currentFilter == '30days') ? 'btn-primary' : 'btn-default' ?>" 
               style="padding: 8px 16px; font-size: 14px; border: 1px solid #ccc; text-decoration: none;">
               30 Hari Terakhir
            </a>
            <a href="<?= BASE_URL ?>/pos/history?filter=all" 
               class="btn <?= ($currentFilter == 'all') ? 'btn-primary' : 'btn-default' ?>" 
               style="padding: 8px 16px; font-size: 14px; border: 1px solid #ccc; text-decoration: none;">
               Semua Data
            </a>
        </div>

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 18px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 500;">Total Penjualan</div>
                <div style="font-size: 24px; font-weight: bold;"><?= formatRupiah($stats['total_sales']) ?></div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 18px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 500;">Jumlah Transaksi</div>
                <div style="font-size: 24px; font-weight: bold;"><?= number_format($stats['total_transactions']) ?></div>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 18px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 500;">Rata-rata per Transaksi</div>
                <div style="font-size: 24px; font-weight: bold;"><?= formatRupiah($stats['average_sale']) ?></div>
            </div>
        </div>

        <!-- Charts Section -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <!-- Sales Trend Chart -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Tren Penjualan</h3>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
            
            <!-- Category Sales Chart -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">Penjualan per Kategori</h3>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="categorySalesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Transaction Table -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px;">
            <h3 style="margin-top: 0;">Detail Transaksi</h3>
            <div class="table-responsive">
                <table class="table" id="transactionTable">
                    <thead>
                        <tr>
                            <th>No Transaksi</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                        <tr><td colspan="6" align="center">Belum ada transaksi.</td></tr>
                        <?php else: ?>
                            <?php foreach ($history as $row): ?>
                            <tr>
                                <td><?= $row['no_transaksi'] ?></td>
                                <td><?= $row['tanggal'] ?></td>
                                <td><?= formatRupiah($row['total_harga']) ?></td>
                                <td><?= formatRupiah($row['bayar']) ?></td>
                                <td><?= formatRupiah($row['kembalian']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/pos/struk/<?= $row['id_transaksi'] ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;" target="_blank">
                                        <i class="fas fa-print"></i> Cetak Struk
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Sales Trend Chart
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: <?= json_encode($chartData['data']) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Category Sales Chart
    const categorySalesCtx = document.getElementById('categorySalesChart').getContext('2d');
    new Chart(categorySalesCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($categorySales, 'nama_kategori')) ?>,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: <?= json_encode(array_column($categorySales, 'total_sales')) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Export Functions
    function exportPDF() {
        window.print();
    }

    function exportExcel() {
        // Clone table to avoid modifying DOM
        const originalTable = document.getElementById('transactionTable');
        const table = originalTable.cloneNode(true);
        
        // Remove 'Aksi' column (Last header and last cell in every row)
        // Header
        const theadRow = table.querySelector('thead tr');
        if (theadRow && theadRow.lastElementChild) {
            theadRow.removeChild(theadRow.lastElementChild);
        }
        // Body rows
        table.querySelectorAll('tbody tr').forEach(row => {
            if (row.lastElementChild) {
                row.removeChild(row.lastElementChild);
            }
        });

        // Add Styling for Excel
        const cells = table.querySelectorAll('td, th');
        cells.forEach(cell => {
            // Apply Borders
            cell.style.border = '1px solid #000';
            
            // Text Cleaning
            let text = cell.innerText.trim();
            cell.innerText = text; // Removes HTML tags like buttons insides
            
            // Format Numbers/Currency
            // Detect "Rp 10.000" or similar
            if (text.startsWith('Rp') || /^-?[\d.]+(?:,[\d]+)?$/.test(text)) {
                 // Remove "Rp " and dots, replace comma with dot for JS float
                 let cleanNum = text.replace('Rp ', '').replace(/\./g, '').replace(',', '.');
                 let num = parseFloat(cleanNum);
                 
                 if (!isNaN(num)) {
                     cell.innerText = num;
                     cell.style.cssText += 'mso-number-format:"\\#\\,\\#\\#0"; border: 1px solid #000;';
                 } else {
                     cell.style.cssText += 'mso-number-format:"\@"; border: 1px solid #000;';
                 }
            } else {
                 // Text
                 cell.style.cssText += 'mso-number-format:"\@"; border: 1px solid #000;';
            }
        });

        // Add Title Header Rows
        const thead = table.querySelector('thead');
        if (thead) {
            // Title Row
            const row1 = document.createElement('tr');
            row1.innerHTML = `<th colspan="${theadRow ? theadRow.children.length : 5}" style="background: #f1c40f; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000;">RIWAYAT PENJUALAN</th>`;
            thead.insertBefore(row1, thead.firstChild);

            // Summary Info Row
            const row2 = document.createElement('tr');
            row2.innerHTML = `<th colspan="${theadRow ? theadRow.children.length : 5}" style="text-align: left; border: 1px solid #000;">Total Penjualan: <?= formatRupiah($stats["total_sales"]) ?> | Jumlah Transaksi: <?= $stats["total_transactions"] ?></th>`;
            thead.insertBefore(row2, row1.nextSibling);
        }

        let htmlContent = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        htmlContent += '<head><meta charset="UTF-8">';
        htmlContent += '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Riwayat Transaksi</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
         htmlContent += '<style>td, th { border: 1px solid black; } .text-right { text-align: right; } .text-center { text-align: center; }</style>';
        htmlContent += '</head><body>';
        htmlContent += table.outerHTML;
        htmlContent += '</body></html>';
        
        const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Riwayat_Transaksi_' + new Date().toISOString().split('T')[0] + '.xls';
        a.click();
    }

    function exportCSV() {
        const table = document.getElementById('transactionTable');
        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(function(row) {
            const cols = row.querySelectorAll('td, th');
            const csvRow = [];
            cols.forEach(function(col, index) {
                // Skip action column
                if (index < cols.length - 1) {
                    csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
                }
            });
            if (csvRow.length > 0) {
                csv.push(csvRow.join(','));
            }
        });
        
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Riwayat_Transaksi_' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
