<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Daily Menu Report</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 10mm 8mm;
      /* Top/Bottom: 12mm, Left/Right: 10mm */
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 7.5pt;
      line-height: 1.2;
    }

    .header {
      text-align: center;
      margin-bottom: 12px;
      border-bottom: 2px solid #000;
      padding-bottom: 6px;
    }

    .header h1 {
      font-size: 14pt;
      margin-bottom: 3px;
      font-weight: bold;
    }

    /* Container untuk layout horizontal */
    .customers-container {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      /* Perbesar gap dari 10px ke 15px */
      justify-content: flex-start;
      /* Ganti dari space-between ke flex-start */
    }

    .customer-section {
      flex: 0 0 calc(50% - 8px);
      /* Kurangi width sedikit untuk spacing */
      margin-bottom: 12px;
      page-break-inside: avoid;
    }

    /* Untuk customer dengan banyak kantin (>4), gunakan full width */
    .customer-section.full-width {
      flex: 0 0 100%;
    }

    .customer-title {
      background-color: #4472C4;
      color: white;
      padding: 4px 8px;
      /* Tambah padding */
      font-size: 8.5pt;
      font-weight: bold;
      margin-bottom: 4px;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
      table-layout: auto;
    }

    table th {
      background-color: #D9E1F2;
      border: 1px solid #333;
      padding: 4px 5px;
      /* Tambah padding */
      font-size: 7.5pt;
      font-weight: bold;
      text-align: center;
      word-wrap: break-word;
      vertical-align: middle;
    }

    table td {
      border: 1px solid #666;
      padding: 3px 5px;
      /* Tambah padding */
      font-size: 7.5pt;
      text-align: center;
      word-wrap: break-word;
      vertical-align: middle;
    }

    table td:first-child {
      text-align: left;
      padding-left: 6px;
    }

    table td:nth-child(2) {
      text-align: left;
      padding-left: 6px;
    }

    table tbody tr:nth-child(odd) {
      background-color: #FFFFFF;
    }

    table tbody tr:nth-child(even) {
      background-color: #F8F8F8;
      /* Lebih soft */
    }

    table tfoot tr {
      background-color: #FFE699;
      font-weight: bold;
      font-size: 8pt;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      background-color: #fff3cd;
      border: 1px solid #ffc107;
      color: #856404;
    }

    /* Fixed column widths untuk konsistensi */
    .col-menu {
      width: 28%;
      /* Perbesar dari 25% */
      min-width: 130px;
    }

    .col-kategori {
      width: 20%;
      /* Perbesar dari 18% */
      min-width: 90px;
    }

    .col-kantin {
      width: auto;
      min-width: 45px;
      /* Perbesar dari 40px */
      max-width: 65px;
    }

    .col-total {
      width: 12%;
      /* Perbesar dari 10% */
      min-width: 55px;
    }

    /* Tambahan untuk readability */
    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>
  <!-- Header -->
  <div class="header">
    <h1>Daily Menu Report</h1>
  </div>

  <!-- Data per Customer - Layout Horizontal -->
  <?php if (count($groupedByCustomer) > 0) : ?>
    <div class="customers-container">
      <?php foreach ($groupedByCustomer as $customerId => $customerData) : ?>
        <?php
        // Hitung jumlah kantin untuk menentukan layout
        $jumlahKantin = count($customerData['kantins']);

        // Jika kantin > 4, gunakan full width
        $isFullWidth = $jumlahKantin > 4;
        ?>

        <div class="customer-section <?= $isFullWidth ? 'full-width' : '' ?>">
          <!-- Customer Title -->
          <div class="customer-title" title="<?= htmlspecialchars($customerData['customer_name']) ?>">
            <?= htmlspecialchars($customerData['customer_name']) ?>
          </div>

          <table>
            <thead>
              <tr>
                <th class="col-menu">Menu Kondimen</th>
                <th class="col-kategori">Kategori</th>
                <?php foreach ($customerData['kantins'] as $kantin) : ?>
                  <th class="col-kantin" title="<?= htmlspecialchars($kantin) ?>">
                    <?php
                    // Truncate nama kantin jika terlalu panjang
                    $displayKantin = $kantin;
                    if (strlen($kantin) > 10) {
                      $displayKantin = substr($kantin, 0, 8) . '..';
                    }
                    echo htmlspecialchars($displayKantin);
                    ?>
                  </th>
                <?php endforeach; ?>
                <th class="col-total">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($customerData['menu_data'])) : ?>
                <tr>
                  <td colspan="<?= count($customerData['kantins']) + 3 ?>" style="text-align: center; padding: 12px; color: #999;">
                    Tidak ada data menu
                  </td>
                </tr>
              <?php else : ?>
                <?php foreach ($customerData['menu_data'] as $menu) : ?>
                  <tr>
                    <td title="<?= htmlspecialchars($menu['menu_kondimen']) ?>">
                      <?= htmlspecialchars($menu['menu_kondimen']) ?>
                    </td>
                    <td title="<?= htmlspecialchars($menu['kategori']) ?>">
                      <?= htmlspecialchars($menu['kategori']) ?>
                    </td>
                    <?php foreach ($customerData['kantins'] as $kantin) : ?>
                      <td>
                        <?php
                        $qty = isset($menu['qty_per_kantin'][$kantin]) ? $menu['qty_per_kantin'][$kantin] : 0;
                        echo $qty;
                        ?>
                      </td>
                    <?php endforeach; ?>
                    <td><strong><?= $menu['total'] ?></strong></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;"><strong>Total</strong></td>
                <?php
                // Total per kantin
                foreach ($customerData['kantins'] as $kantin) {
                  $totalPerKantin = 0;
                  foreach ($customerData['menu_data'] as $menu) {
                    if (isset($menu['qty_per_kantin'][$kantin])) {
                      $totalPerKantin += $menu['qty_per_kantin'][$kantin];
                    }
                  }
                  echo "<td style='text-align: center;'><strong>$totalPerKantin</strong></td>";
                }

                // Grand total
                $grandTotal = 0;
                foreach ($customerData['menu_data'] as $menu) {
                  $grandTotal += $menu['total'];
                }
                echo "<td style='text-align: center;'><strong>$grandTotal</strong></td>";
                ?>
              </tr>
            </tfoot>
          </table>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="no-data">
      <strong>⚠ Tidak Ada Data</strong><br>
      Tidak ada data menu harian yang sesuai dengan filter yang dipilih.
    </div>
  <?php endif; ?>
</body>

</html>