<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Daily Menu Report PT Ryan Catering - Excel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      color: #0b1f33;
      background: #edf2fb;
      padding: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #13448a;
    }

    .meta {
      margin: 0 auto 20px;
      padding: 10px 14px;
      max-width: 95%;
      background: linear-gradient(90deg, #b9d7ff 0%, #d9e8ff 100%);
      border: 1px solid #8fb3e2;
      border-radius: 6px;
      color: #0b1f33;
    }

    .meta span {
      display: inline-block;
      margin-right: 18px;
      font-weight: bold;
    }

    .customer-block {
      margin: 0 auto 28px;
      max-width: 95%;
      background: #ffffff;
      border: 1px solid #7f8ca5;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(17, 34, 68, 0.08);
    }

    .customer-title {
      padding: 10px 14px;
      background: #d9d9d9;
      color: #101820;
      font-weight: bold;
      font-size: 15px;
      border-radius: 6px 6px 0 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      background: #ffffff;
    }

    th,
    td {
      border: 1px solid #4c5f7a;
      padding: 6px 8px;
      vertical-align: middle;
    }

    thead th {
      background: #0f274d;
      color: #ffffff;
      text-align: center;
      font-weight: bold;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .total-order-cell {
      background: #ffe8a3;
      font-weight: bold;
      text-align: center;
      color: #5c4200;
    }

    .total-label-row th {
      background: #0f274d;
      color: #fff;
      text-align: right;
      font-weight: bold;
    }

    .kantin-header {
      background: #2563eb;
      color: #fff;
    }
  </style>
</head>

<body>
  <?php
  $dateLabel = !empty($filter['tanggal']) ? date('d M Y', strtotime($filter['tanggal'])) : 'Semua Tanggal';
  $shiftLabel = !empty($filter['shift']) ? strtoupper($filter['shift']) : 'Semua Shift';
  ?>
  <h2>Menu Harian Report</h2>
  <div class="meta">
    <span>Tanggal Laporan: <?= htmlspecialchars($dateLabel); ?></span>
    <span>Shift: <?= htmlspecialchars($shiftLabel); ?></span>
    <?php if (isset($grandTotalOrderAll) && $grandTotalOrderAll > 0) : ?>
      <span style="background:#ffc107; color:#000; padding:4px 10px; border-radius:4px; font-weight:bold;">
        Total Order Keseluruhan: <?= number_format($grandTotalOrderAll, 0, ',', '.') ?>
      </span>
    <?php endif; ?>
  </div>

  <?php if (empty($groupedByCustomer)) : ?>
    <p>Tidak ada data untuk filter yang dipilih.</p>
  <?php else : ?>
    <?php foreach ($groupedByCustomer as $customer) : ?>
      <div class="customer-block">
        <div class="customer-title">
          <?= htmlspecialchars($customer['customer_name']); ?> (<?= count($customer['kantins']); ?> Kantin)
          <span style="float:right; background:#ffc107; color:#000; padding:4px 10px; border-radius:4px; font-size:13px;">
            Total Order: <?= isset($customer['grand_total_order']) ? (int)$customer['grand_total_order'] : 0 ?>
          </span>
        </div>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Menu</th>
              <th>Jenis Menu</th>
              <th>Shift</th>
              <th>Kondimen</th>
              <th>Kategori</th>
              <?php foreach ($customer['kantins'] as $kantinName) : ?>
                <th><?= htmlspecialchars($kantinName); ?></th>
              <?php endforeach; ?>
              <th>Total</th>
              <th>Total Order</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $menuIndex = 1;
            foreach ($customer['menu_data'] as $menu) :
              $kondimenList = $menu['kondimen_list'];
              if (empty($kondimenList)) {
                $kondimenList = [[
                  'nama_kondimen' => '-',
                  'kategori' => '-',
                  'qty_per_kantin' => [],
                  'total' => 0
                ]];
              }
              $rowspan = count($kondimenList);
              foreach ($kondimenList as $idx => $kondimen) :
                $rowNumber = $menuIndex;
                $qtyPerKantin = isset($kondimen['qty_per_kantin']) && is_array($kondimen['qty_per_kantin'])
                  ? $kondimen['qty_per_kantin']
                  : [];
                $counterLabel = ($idx + 1) . '. ';
            ?>
                <tr>
                  <?php if ($idx === 0) : ?>
                    <td class="text-center" rowspan="<?= $rowspan; ?>"><?= $rowNumber; ?></td>
                    <td rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($menu['nama_menu'] ?? '-'); ?></td>
                    <td rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($menu['jenis_menu'] ?? '-'); ?></td>
                    <td class="text-center" rowspan="<?= $rowspan; ?>"><?= !empty($menu['shift']) ? strtoupper(htmlspecialchars($menu['shift'])) : '-'; ?></td>
                  <?php endif; ?>
                  <td><?= $counterLabel ?><?= htmlspecialchars($kondimen['nama_kondimen'] ?? '-'); ?></td>
                  <td><?= htmlspecialchars($kondimen['kategori'] ?? '-'); ?></td>
                  <?php foreach ($customer['kantins'] as $kantinName) :
                    $qty = isset($qtyPerKantin[$kantinName]) ? (int) $qtyPerKantin[$kantinName] : 0;
                  ?>
                    <td class="text-center"><?= $qty; ?></td>
                  <?php endforeach; ?>
                  <td class="text-center"><?= !empty($qtyPerKantin) ? array_sum(array_map('intval', $qtyPerKantin)) : (isset($kondimen['total']) ? (int) $kondimen['total'] : 0); ?></td>
                  <?php if ($idx === 0) : ?>
                    <td class="total-order-cell" rowspan="<?= $rowspan; ?>"><?= isset($menu['total_order_customer']) ? (int) $menu['total_order_customer'] : 0; ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
              <?php $menuIndex++; ?>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="total-label-row">
              <th colspan="<?= 7 + count($customer['kantins']); ?>">Total</th>
              <th class="total-order-cell"><?= isset($customer['grand_total_order']) ? (int) $customer['grand_total_order'] : 0; ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>

</html>