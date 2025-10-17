<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Laporan Kebutuhan Bahan Baku</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      margin: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      border-bottom: 2px solid #333;
      padding-bottom: 20px;
    }

    .header h1 {
      margin: 0;
      font-size: 18px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .header h2 {
      margin: 5px 0;
      font-size: 14px;
      font-weight: normal;
    }

    .info-table {
      width: 100%;
      margin-bottom: 20px;
    }

    .info-table td {
      padding: 5px;
      vertical-align: top;
    }

    .info-table .label {
      font-weight: bold;
      width: 150px;
    }

    .main-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .main-table th,
    .main-table td {
      border: 1px solid #333;
      padding: 8px;
      text-align: left;
    }

    .main-table th {
      background-color: #f5f5f5;
      font-weight: bold;
      text-align: center;
    }

    .main-table .number {
      text-align: right;
    }

    .main-table .center {
      text-align: center;
    }

    .footer-total {
      font-weight: bold;
      background-color: #f0f0f0;
    }

    .footer {
      margin-top: 30px;
      text-align: right;
      font-size: 10px;
    }

    .summary-box {
      border: 1px solid #333;
      padding: 15px;
      margin-bottom: 20px;
      background-color: #f9f9f9;
    }

    .summary-box h3 {
      margin: 0 0 10px 0;
      font-size: 14px;
    }

    .summary-item {
      display: inline-block;
      margin-right: 30px;
      margin-bottom: 5px;
    }

    .summary-item .label {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>Laporan Kebutuhan Bahan Baku</h1>
    <h2>Ryan Catering</h2>
  </div>

  <table class="info-table">
    <tr>
      <td class="label">Periode Laporan:</td>
      <td><?= $periode ?></td>
    </tr>
    <tr>
      <td class="label">Total Porsi:</td>
      <td><?= number_format($porsi_total, 0, ',', '.') ?> porsi</td>
    </tr>
    <tr>
      <td class="label">Tanggal Cetak:</td>
      <td><?= $tanggal_cetak ?></td>
    </tr>
  </table>

  <?php
  $total_keseluruhan = 0;
  $total_bahan = count($laporan_data);
  foreach ($laporan_data as $item) {
    $total_keseluruhan += $item['total_biaya'];
  }
  ?>

  <div class="summary-box">
    <h3>Ringkasan Laporan</h3>
    <div class="summary-item">
      <span class="label">Total Jenis Bahan:</span> <?= $total_bahan ?> item
    </div>
    <div class="summary-item">
      <span class="label">Total Biaya Keseluruhan:</span> Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?>
    </div>
    <div class="summary-item">
      <span class="label">Rata-rata Biaya per Bahan:</span> Rp <?= $total_bahan > 0 ? number_format($total_keseluruhan / $total_bahan, 0, ',', '.') : 0 ?>
    </div>
  </div>

  <table class="main-table">
    <thead>
      <tr>
        <th width="5%">No</th>
        <th width="35%">Nama Bahan</th>
        <th width="12%">Satuan</th>
        <th width="15%">Kebutuhan Total</th>
        <th width="15%">Harga Satuan</th>
        <th width="18%">Total Biaya</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($laporan_data)) : ?>
        <tr>
          <td colspan="6" class="center">Tidak ada data untuk periode yang dipilih</td>
        </tr>
      <?php else : ?>
        <?php $no = 1; ?>
        <?php foreach ($laporan_data as $item) : ?>
          <tr>
            <td class="center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($item['nama_bahan']) ?></td>
            <td class="center"><?= htmlspecialchars($item['nama_satuan']) ?></td>
            <td class="number"><?= number_format($item['total_kebutuhan'], 2, ',', '.') ?></td>
            <td class="number">Rp <?= number_format($item['harga_sekarang'], 0, ',', '.') ?></td>
            <td class="number">Rp <?= number_format($item['total_biaya'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
    <?php if (!empty($laporan_data)) : ?>
      <tfoot>
        <tr class="footer-total">
          <td colspan="5" class="center"><strong>TOTAL KESELURUHAN</strong></td>
          <td class="number"><strong>Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?></strong></td>
        </tr>
      </tfoot>
    <?php endif; ?>
  </table>

  <?php if (!empty($laporan_data)) : ?>
    <div style="margin-top: 30px; page-break-inside: avoid;">
      <h3 style="margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 5px;">
        Catatan Laporan
      </h3>
      <ul style="margin: 0; padding-left: 20px; font-size: 11px;">
        <li>Laporan ini menunjukkan kebutuhan bahan baku berdasarkan menu yang telah dibuat dalam sistem food cost.</li>
        <li>Harga yang digunakan adalah harga terkini (harga_sekarang) dari database bahan.</li>
        <li>Kebutuhan total sudah dikalikan dengan jumlah porsi yang diminta (<?= $porsi_total ?> porsi).</li>
        <li>Total biaya belum termasuk biaya overhead produksi dan margin keuntungan.</li>
        <li>Data ini dapat digunakan untuk perencanaan pembelian dan pengelolaan inventory.</li>
      </ul>
    </div>
  <?php endif; ?>

  <div class="footer">
    <p>
      Laporan digenerate otomatis oleh sistem Ryan Catering<br>
      Tanggal: <?= $tanggal_cetak ?>
    </p>
  </div>
</body>

</html>