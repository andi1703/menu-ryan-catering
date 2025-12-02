<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Daily Menu Report</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 10mm 8mm;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 7pt;
    }

    .header {
      text-align: center;
      margin-bottom: 10px;
    }

    .header h1 {
      font-size: 11pt;
      font-weight: bold;
      margin-bottom: 2px;
    }

    .header .sub {
      font-size: 8pt;
      margin-bottom: 6px;
    }

    .customers-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .customer-block {
      flex: 0 0 48%;
      margin-bottom: 10px;
      page-break-inside: avoid;
    }

    .customer-title {
      background: #4472C4;
      color: #fff;
      font-weight: bold;
      padding: 4px 8px;
      font-size: 8pt;
      border-radius: 3px 3px 0 0;
    }

    .menu-title {
      font-size: 8pt;
      margin-bottom: 2px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 0;
      font-size: 7pt;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 2px 4px;
      text-align: center;
    }

    th {
      background: #D9E1F2;
      font-weight: bold;
      color: #222;
    }

    .total-row {
      background: #FFE699;
      font-weight: bold;
      color: #222;
    }

    .col-no {
      width: 18px;
    }

    .col-menu {
      text-align: left;
    }

    .col-nama-menu {
      text-align: left;
    }

    .col-jenis-menu {
      text-align: left;
    }

    .no-data {
      text-align: center;
      padding: 10px;
      background: #fff3cd;
      border: 1px solid #ffc107;
      color: #856404;
      margin-bottom: 10px;
    }

    /* Responsive print */
    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .customer-block {
        page-break-inside: avoid;
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>MENU HARIAN REPORT</h1>
    <div class="sub">
      Shift: <?= isset($shift) ? htmlspecialchars($shift) : '-' ?> &nbsp; | &nbsp;
      Tanggal: <?= isset($tanggal) ? htmlspecialchars($tanggal) : date('d/m/Y') ?>
    </div>
  </div>

  <?php if (!empty($groupedByCustomer)) : ?>
    <div class="customers-container">
      <?php foreach ($groupedByCustomer as $customerId => $customerData) : ?>
        <div class="customer-block">
          <div class="customer-title">
            <?= htmlspecialchars($customerData['customer_name']) ?>
          </div>
          <table>
            <thead>
              <tr>
                <th class="col-no">#</th>
                <th class="col-nama-menu">Nama Menu</th>
                <th class="col-jenis-menu">Jenis Menu</th>
                <th class="col-menu">Kondimen</th>
                <?php foreach ($customerData['kantins'] as $kantin) : ?>
                  <th><?= htmlspecialchars($kantin) ?></th>
                <?php endforeach; ?>
                <th>Total</th>
              </tr>
            </thead>
            <?php
            // Hitung rowspan untuk setiap kombinasi nama_menu + jenis_menu
            $rowspanMap = [];
            foreach ($customerData['menu_data'] as $menu) {
              $key = $menu['nama_menu'] . '|' . $menu['jenis_menu'];
              if (!isset($rowspanMap[$key])) $rowspanMap[$key] = 0;
              $rowspanMap[$key]++;
            }
            ?>

            <tbody>
              <?php
              $no = 1;
              $printed = [];
              foreach ($customerData['menu_data'] as $menu) :
                $key = $menu['nama_menu'] . '|' . $menu['jenis_menu'];
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                // Merge cell untuk nama_menu dan jenis_menu
                if (empty($printed[$key])) {
                  echo '<td rowspan="' . $rowspanMap[$key] . '">' . htmlspecialchars($menu['nama_menu']) . '</td>';
                  echo '<td rowspan="' . $rowspanMap[$key] . '">' . htmlspecialchars($menu['jenis_menu']) . '</td>';
                  $printed[$key] = true;
                }
                // Kondimen dan qty
                echo '<td>' . htmlspecialchars($menu['menu_kondimen']) . '</td>';
                foreach ($customerData['kantins'] as $kantin) {
                  echo '<td>' . (isset($menu['qty_per_kantin'][$kantin]) ? $menu['qty_per_kantin'][$kantin] : 0) . '</td>';
                }
                echo '<td><strong>' . $menu['total'] . '</strong></td>';
                echo '</tr>';
              endforeach;
              ?>
            </tbody>
            <tfoot>
              <tr class="total-row">
                <td colspan="4">Total</td>
                <?php
                foreach ($customerData['kantins'] as $kantin) {
                  $totalPerKantin = 0;
                  foreach ($customerData['menu_data'] as $menu) {
                    if (isset($menu['qty_per_kantin'][$kantin])) {
                      $totalPerKantin += $menu['qty_per_kantin'][$kantin];
                    }
                  }
                  echo "<td>$totalPerKantin</td>";
                }
                $grandTotal = 0;
                foreach ($customerData['menu_data'] as $menu) {
                  $grandTotal += $menu['total'];
                }
                echo "<td>$grandTotal</td>";
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