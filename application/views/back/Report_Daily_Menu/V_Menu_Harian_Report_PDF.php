<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>MENU HARIAN REPORT</title>
  <style>
    /* 1. SETUP KERTAS & RESET TOTAL */
    @page {
      size: A4 landscape;
      margin: 5mm;
      /* Margin kertas tipis 5mm */
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Helvetica', Arial, sans-serif;
      font-size: 7pt;
      color: #000;
      line-height: 1.1;
    }

    .page-container {
      padding: 4mm 6mm 0;
    }

    /* HEADER LAPORAN */
    .header {
      text-align: center;
      margin-bottom: 5px;
      /* Jarak ke tabel */
      border-bottom: 2px solid #000;
      padding-bottom: 2px;
      width: 100%;
    }

    .header h1 {
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      line-height: 1;
      margin-bottom: 2px;
    }

    .header .sub {
      font-size: 8pt;
      margin: 0;
    }

    /* === LAYOUT UTAMA: 3 KOLOM === */
    .layout-grid {
      width: 100%;
      margin-top: 0;
      font-size: 0;
      display: block;
    }

    .layout-grid .grid-col {
      display: inline-block;
      vertical-align: top;
      padding: 0;
      font-size: 7pt;
      box-sizing: border-box;
    }

    /* Pengaturan Lebar Kolom yang Aman */
    .layout-grid .col-content {
      width: 49%;
    }

    .layout-grid .col-spacer {
      width: 2%;
    }

    /* WRAPPER ITEM (Customer / Menu) */
    .item-wrapper {
      width: 100%;
      margin-bottom: 4px;
      /* Jarak vertikal antar customer */
      border: 1px solid #000;
      background-color: #fff;
      page-break-inside: auto;
      break-inside: auto;
    }

    /* HEADER CUSTOMER */
    .customer-header-block {
      background-color: #007bff;
      color: #fff;
      font-weight: bold;
      font-size: 9pt;
      padding: 3px 5px;
      text-transform: uppercase;
      border-bottom: 1px solid #000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .customer-header-block .total-order-badge {
      background-color: #ffc107;
      color: #000;
      padding: 2px 8px;
      border-radius: 3px;
      font-size: 8pt;
      font-weight: bold;
    }

    /* CONTAINER MENU DALAM CUSTOMER */
    .customer-body {
      padding: 0;
      background-color: #fff;
    }

    /* KOTAK MENU INDIVIDUAL */
    .menu-box {
      width: 100%;
      margin: 0;
      border-bottom: 1px solid #000;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .menu-box:last-child {
      border-bottom: none;
    }

    /* JUDUL MENU */
    .menu-title {
      background-color: #e9f2fb;
      border-bottom: 1px solid #999;
      padding: 2px 4px;
      font-weight: bold;
      font-size: 7.5pt;
      color: #000;
      text-transform: uppercase;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
      text-align: center;
    }

    .menu-title .menu-meta-line {
      font-weight: normal;
      font-size: 6.5pt;
      color: #000;
      text-transform: none;
    }

    .menu-title .menu-meta-line strong {
      font-size: 7.5pt;
    }

    /* TABEL DATA KONDIMEN */
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
      table-layout: fixed;
      border: none;
      margin: 0;
    }

    table.data-table th,
    table.data-table td {
      border: 1px solid #999;
      padding: 2px 3px;
      text-align: center;
      vertical-align: middle;
      word-wrap: break-word;
    }

    /* Hapus border duplikat agar rapi */
    table.data-table th:first-child,
    table.data-table td:first-child {
      border-left: none;
    }

    table.data-table th:last-child,
    table.data-table td:last-child {
      border-right: none;
    }

    table.data-table th {
      border-top: none;
    }

    table.data-table th {
      text-align: center;
      background-color: #333;
      color: #fff;
      font-weight: bold;
    }

    .col-total-merged {
      background-color: #ffe699;
      color: #000;
      font-weight: bold;
      font-size: 8pt;
    }

    table.data-table thead .col-total-merged {
      background-color: #333;
      color: #fff;
    }

    .text-left {
      text-align: left;
      padding-left: 4px;
    }

    .col-kondimen,
    .col-kondimen-cell {
      width: 38%;
      min-width: 38%;
      max-width: 38%;
    }

    .col-kondimen-cell {
      text-align: left !important;
    }

    .kondimen-number {
      display: inline-block;
      min-width: 12px;
      font-weight: bold;
    }

    .kondimen-name {
      display: inline-block;
      margin-left: 2px;
    }

    .text-qty {
      font-weight: normal;
      color: #000;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      border: 1px dashed #ccc;
    }

    .badge-shift {
      display: inline-block;
      padding: 1px 7px;
      border-radius: 999px;
      font-size: 6.5pt;
      font-weight: 700;
      color: #fff;
      text-transform: capitalize;
      letter-spacing: 0.3px;
    }

    .special-summary-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
    }

    .special-summary-table th,
    .special-summary-table td {
      border: 1px solid #999;
      padding: 3px 4px;
      vertical-align: middle;
    }

    .special-summary-table thead th {
      background-color: #444;
      color: #fff;
      text-align: center;
      font-weight: bold;
    }

    .special-summary-table .col-summary {
      text-align: left;
      line-height: 1.35;
    }

    .special-summary-table tbody .col-total {
      background-color: #ffe699;
      font-weight: bold;
      text-align: center;
      width: 45px;
    }

    .special-summary-table thead .col-total {
      background-color: #444;
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="page-container">
    <!-- HEADER -->
    <div class="header">
      <h1>MENU HARIAN REPORT PT RYAN CATERING</h1>
      <div class="sub">
        Shift: <strong><?= !empty($filter['shift']) ? strtoupper($filter['shift']) : 'SEMUA' ?></strong>
        &nbsp;|&nbsp;
        Tanggal: <strong><?= !empty($filter['tanggal']) ? date('d/m/Y', strtotime($filter['tanggal'])) : date('d/m/Y') ?></strong>
        <?php if (isset($grandTotalOrderAll) && $grandTotalOrderAll > 0) : ?>
          &nbsp;|&nbsp;
          <span style="background:#ffc107; color:#000; padding:2px 6px; border-radius:3px; font-weight:bold;">
            Grand Total Order: <?= number_format($grandTotalOrderAll, 0, ',', '.') ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($groupedByCustomer)) : ?>

      <?php
      // === LOGIKA SPLIT DATA ===
      // Membagi data menjadi array Kiri dan Kanan
      $isSingleCustomer = (count($groupedByCustomer) == 1);

      $leftContent = [];
      $rightContent = [];

      if ($isSingleCustomer) {
        // MODE 1 CUSTOMER: SPLIT MENU DIA SENDIRI
        $cust = reset($groupedByCustomer);
        $menus = normalizeMenus($cust['menu_data']);

        // Header Customer Full Width
        echo '<div style="margin-top:0; margin-bottom:2px; background:#007bff; color:#fff; padding:3px 5px; font-weight:bold; font-size:9pt; text-transform:uppercase; border:1px solid #000;">' . htmlspecialchars($cust['customer_name']) . '</div>';

        $counter = 0;
        foreach ($menus as $m) {
          $item = [
            'type' => 'menu_only',
            'menu_data' => $m,
            'kantins' => $cust['kantins'],
            'customer_name' => $cust['customer_name']
          ];

          if ($counter % 2 == 0) $leftContent[] = $item;
          else $rightContent[] = $item;
          $counter++;
        }
      } else {
        // MODE BANYAK CUSTOMER: SPLIT PER CUSTOMER
        $counter = 0;
        foreach ($groupedByCustomer as $c) {
          $item = ['type' => 'customer_full', 'data' => $c];

          if ($counter % 2 == 0) $leftContent[] = $item;
          else $rightContent[] = $item;
          $counter++;
        }
      }
      ?>

      <!-- STRUKTUR LAYOUT UTAMA 3 KOLOM -->
      <div class="layout-grid">
        <!-- KOLOM KIRI (49%) -->
        <div class="grid-col col-content">
          <?php foreach ($leftContent as $item) {
            renderItem($item);
          } ?>
        </div>

        <!-- KOLOM TENGAH / SPACER (2%) -->
        <div class="grid-col col-spacer"></div>

        <!-- KOLOM KANAN (49%) -->
        <div class="grid-col col-content">
          <?php foreach ($rightContent as $item) {
            renderItem($item);
          } ?>
        </div>
      </div>

    <?php else : ?>
      <div class="no-data"><strong>⚠ Tidak Ada Data</strong></div>
    <?php endif; ?>
  </div>

</body>

</html>

<?php
// === HELPER FUNCTIONS (PHP) ===

function normalizeMenus($menuData)
{
  $menus = [];
  $firstItem = reset($menuData);
  if (isset($firstItem['kondimen_list'])) {
    return $menuData;
  }
  foreach ($menuData as $row) {
    $shiftValue = isset($row['shift']) ? $row['shift'] : '';
    $menuKey = $row['nama_menu'] . '_' . $row['jenis_menu'] . '_' . $shiftValue;
    if (!isset($menus[$menuKey])) {
      $menus[$menuKey] = [
        'nama_menu' => $row['nama_menu'],
        'jenis_menu' => $row['jenis_menu'],
        'shift' => $shiftValue,
        'total_order_customer' => isset($row['total_order_customer']) ? $row['total_order_customer'] : (isset($row['total_orderan']) ? $row['total_orderan'] : 0),
        'total_orderan' => isset($row['total_orderan']) ? $row['total_orderan'] : 0,
        'kondimen_list' => []
      ];
    }
    $row['menu_kondimen'] = isset($row['menu_kondimen']) ? $row['menu_kondimen'] : (isset($row['nama_kondimen']) ? $row['nama_kondimen'] : '-');
    $row['shift'] = $shiftValue;
    $menus[$menuKey]['kondimen_list'][] = $row;
  }
  return array_values($menus);
}

function renderItem($item)
{
  if ($item['type'] === 'customer_full') {
    $c = $item['data'];
    $menus = normalizeMenus(isset($c['menu_data']) ? $c['menu_data'] : []);
?>
    <div class="item-wrapper">
      <div class="customer-header-block">
        <span><?= htmlspecialchars($c['customer_name']) ?></span>
        <span class="total-order-badge">Total Order: <?= isset($c['grand_total_order']) ? (int)$c['grand_total_order'] : 0 ?></span>
      </div>
      <div class="customer-body">
        <?php foreach ($menus as $m) {
          renderMenuTable($m, $c['kantins'], $c['customer_name']);
        } ?>
      </div>
    </div>
  <?php
  } elseif ($item['type'] === 'menu_only') {
  ?>
    <div class="item-wrapper">
      <div class="customer-body">
        <?php renderMenuTable($item['menu_data'], $item['kantins'], isset($item['customer_name']) ? $item['customer_name'] : ''); ?>
      </div>
    </div>
  <?php
  }
}

function renderMenuTable($menu, $kantins, $customerName = '')
{
  $kondimenList = isset($menu['kondimen_list']) ? $menu['kondimen_list'] : [];
  $totalOrderMenu = 0;
  if (isset($menu['total_order_customer'])) {
    $totalOrderMenu = intval($menu['total_order_customer']);
  } elseif (isset($menu['total_orderan'])) {
    $totalOrderMenu = intval($menu['total_orderan']);
  }

  $hasSingleKantin = is_array($kantins) && count($kantins) === 1;
  $isSpecialSummary = $hasSingleKantin || isSpecialSummaryCustomer($customerName);

  ?>
  <div class="menu-box">
    <div class="menu-title">
      <?php
      $metaSegments = [];
      $menuNameSafe = htmlspecialchars($menu['nama_menu'] ?? '-');
      $metaSegments[] = '<strong>' . $menuNameSafe . '</strong>';
      if (!empty($menu['jenis_menu'])) {
        $metaSegments[] = htmlspecialchars(strtoupper($menu['jenis_menu']));
      }
      if (!empty($menu['shift'])) {
        $metaSegments[] = '<strong>' . htmlspecialchars(ucwords(strtolower($menu['shift']))) . '</strong>';
      }
      if (!empty($customerName)) {
        $metaSegments[] = htmlspecialchars($customerName);
      }
      ?>
      <span class="menu-meta-line"><?= implode(' | ', $metaSegments) ?></span>
    </div>

    <?php if ($isSpecialSummary) :
      $summaryParts = [];
      foreach ($kondimenList as $kondimen) {
        $namaKondimen = isset($kondimen['menu_kondimen']) ? $kondimen['menu_kondimen'] : (isset($kondimen['nama_kondimen']) ? $kondimen['nama_kondimen'] : '-');
        $qtyValue = 0;
        if (isset($kondimen['total']) && $kondimen['total'] !== '') {
          $qtyValue = intval($kondimen['total']);
        } elseif (isset($kondimen['qty_per_kantin']) && is_array($kondimen['qty_per_kantin'])) {
          $qtyValue = array_sum(array_map('intval', $kondimen['qty_per_kantin']));
        }
        $qtyDisplay = $qtyValue > 0 ? $qtyValue : 0;
        $summaryParts[] = htmlspecialchars($namaKondimen) . ' (<strong>' . $qtyDisplay . '</strong>)';
      }
      $summaryText = !empty($summaryParts) ? implode(', ', $summaryParts) : '-';
    ?>
      <table class="special-summary-table">
        <thead>
          <tr>
            <!-- <th style="width:25px;">No.</th> -->
            <th class="col-summary">Kondimen Menu</th>
            <th class="col-total">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <!-- <td style="text-align:center;">1</td> -->
            <td class="col-summary"><?= $summaryText ?></td>
            <td class="col-total"><?= $totalOrderMenu ?></td>
          </tr>
        </tbody>
      </table>
    <?php else : ?>
      <table class="data-table">
        <thead>
          <tr>
            <th class="text-left col-kondimen">Kondimen Menu</th>
            <?php foreach ($kantins as $kantin) : ?>
              <th>Qty <?= htmlspecialchars($kantin) ?></th>
            <?php endforeach; ?>
            <th style="width:20px;">JML</th>
            <th class="col-total-merged" style="width:25px;">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $rowNo = 1;
          $count = count($kondimenList);
          if ($count > 0) :
            foreach ($kondimenList as $idx => $kondimen) :
              $namaKondimen = isset($kondimen['menu_kondimen']) ? $kondimen['menu_kondimen'] : (isset($kondimen['nama_kondimen']) ? $kondimen['nama_kondimen'] : '-');
          ?>
              <tr>
                <td class="text-left col-kondimen-cell">
                  <span class="kondimen-number"><?= $rowNo++ ?>.</span>
                  <span class="kondimen-name"><?= htmlspecialchars($namaKondimen) ?></span>
                </td>
                <?php foreach ($kantins as $kantin) :
                  $qtySafe = 0;
                  if (isset($kondimen['qty_per_kantin']) && is_array($kondimen['qty_per_kantin'])) {
                    $qtySafe = isset($kondimen['qty_per_kantin'][$kantin]) ? intval($kondimen['qty_per_kantin'][$kantin]) : 0;
                  }
                ?>
                  <td class="text-qty"><?= ($qtySafe > 0) ? $qtySafe : '' ?></td>
                <?php endforeach; ?>

                <td style="background-color:#f2f2f2; font-weight:bold;">
                  <?= isset($kondimen['total']) && $kondimen['total'] > 0 ? $kondimen['total'] : '' ?>
                </td>

                <?php if ($idx === 0) : ?>
                  <td class="col-total-merged" rowspan="<?= $count ?>">
                    <?= $totalOrderMenu ?>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach;
          else : ?>
            <tr>
              <td colspan="<?= 3 + count($kantins) ?>">Empty</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php
}

function isSpecialSummaryCustomer($customerName)
{
  if (empty($customerName)) {
    return false;
  }
  $normalized = strtolower($customerName);
  return (strpos($normalized, 'astra') !== false) &&
    (strpos($normalized, 'daihatsu') !== false || strpos($normalized, 'dihatsu') !== false);
}
?>