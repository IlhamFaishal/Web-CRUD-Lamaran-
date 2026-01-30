<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi - <?= $transaksi['no_transaksi'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .items { width: 100%; border-collapse: collapse; }
        .items td { padding: 5px 0; }
        .total-section { border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8rem; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h3>POS BESI & KAYU</h3>
        <p>Jl. Raya Besi No. 123<br>Telp: 08123456789</p>
        <p><?= $transaksi['tanggal'] ?><br>No: <?= $transaksi['no_transaksi'] ?></p>
        <p>Kasir: <?= currentUser()['username'] ?></p>
    </div>

    <table class="items">
        <?php foreach ($details as $item): ?>
        <tr>
            <td colspan="2"><?= $item['nama_barang'] ?></td>
        </tr>
        <tr>
            <td><?= $item['qty'] ?> x <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
            <td class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="total-section">
        <table width="100%">
            <tr>
                <td>Total</td>
                <td class="text-right"><strong><?= number_format($transaksi['total_harga'], 0, ',', '.') ?></strong></td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="text-right"><?= number_format($transaksi['bayar'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right"><?= number_format($transaksi['kembalian'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima Kasih atas Kunjungan Anda</p>
        <p>Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <a href="<?= BASE_URL ?>/pos" style="text-decoration: none; background: #333; color: #fff; padding: 10px 20px;">Kembali ke POS</a>
    </div>

</body>
</html>
