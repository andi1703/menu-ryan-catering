<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>MENU HARIAN REPORT</title>
  <style>
    /* 1. SETUP KERTAS */
    @page {
      size: A4 landscape;
      /* Margin: Atas/Bawah 10mm, Kiri/Kanan 20mm agar konten tidak terlalu mepet tepi kertas */
      margin: 10mm 15mm;
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
      padding: 0 5mm;
    }

    .page-container {
      width: 100%;
    }

    /* HEADER LAPORAN */
    .header {
      text-align: center;
      margin-bottom: 5px;
      border-bottom: 2px solid #000;
      padding-bottom: 2px;
      width: 100%;
    }

    .header h1 {
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 2px;
    }

    .header .sub {
      font-size: 8pt;
      margin: 0;
    }

    /* === LAYOUT UTAMA: TABLE FIXED === */
    .page-wrapper {
      width: 100%;
      page-break-after: always;
      /* Ganti halaman setelah setiap wrapper selesai */
    }

    .page-wrapper:last-child {
      page-break-after: auto;
    }

    table.layout-grid {
      width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
      border: none;
      margin-top: 5px;
    }

    table.layout-grid td {
      vertical-align: top;
      padding: 0;
    }

    /* Kolom Kiri */
    td.grid-col-left {
      width: 49%;
      padding-right: 5px;
      /* Padding internal antar kolom */
    }

    /* Spacer Tengah */
    td.grid-col-spacer {
      width: 2%;
    }

    /* Kolom Kanan */
    td.grid-col-right {
      width: 49%;
      padding-left: 5px;
      /* Padding internal antar kolom */
    }

    /* WRAPPER ITEM */
    .item-wrapper {
      width: 100%;
      margin-bottom: 5px;
      border: 1px solid #000;
      background-color: #fff;
      page-break-inside: avoid;
      break-inside: avoid;
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

    /* CONTAINER MENU */
    .customer-body {
      padding: 0;
      background-color: #fff;
    }

    /* MENU BOX */
    .menu-box {
      width: 100%;
      border-bottom: 1px solid #000;
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
      text-align: center;
    }

    .menu-title .menu-meta-line {
      font-weight: normal;
      font-size: 6.5pt;
      color: #000;
    }

    /* TABEL DATA */
    table.data-table,
    table.special-summary-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
      table-layout: fixed;
      border: none;
      margin: 0;
    }

    table.data-table th,
    table.data-table td,
    table.special-summary-table th,
    table.special-summary-table td {
      border: 1px solid #999;
      padding: 2px 3px;
      vertical-align: middle;
      word-wrap: break-word;
    }

    /* Clean Borders */
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

    table.special-summary-table th:first-child,
    table.special-summary-table td:first-child {
      border-left: none;
    }

    table.special-summary-table th:last-child,
    table.special-summary-table td:last-child {
      border-right: none;
    }

    table.special-summary-table thead th {
      border-top: none;
    }

    /* Styling Header Tabel */
    table.data-table th,
    .special-summary-table thead th {
      text-align: center;
      background-color: #333;
      color: #fff;
      font-weight: bold;
    }

    .col-total-merged,
    .special-summary-table tbody .col-total {
      background-color: #ffe699;
      color: #000;
      font-weight: bold;
      text-align: center;
    }

    table.data-table thead .col-total-merged,
    .special-summary-table thead .col-total {
      background-color: #333;
      color: #fff;
    }

    .text-left {
      text-align: left;
      padding-left: 4px;
    }

    .text-center {
      text-align: center;
    }

    /* Kolom Kondimen */
    .col-kondimen {
      width: 38%;
    }

    .col-kondimen-cell {
      text-align: left !important;
    }

    .kondimen-number {
      font-weight: bold;
      margin-right: 3px;
    }

    .kondimen-name {
      display: inline;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      border: 1px dashed #ccc;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="page-container">

    <?php if (!empty($groupedByCustomer)) : ?>
      <?php
      // === PRE-PROCESS DATA ===
      $allItems = [];
      foreach ($groupedByCustomer as $c) {
        $allItems[] = [
          'type' => 'customer_header',
          'customer_name' => isset($c['customer_name']) ? $c['customer_name'] : '',
          'grand_total_order' => isset($c['grand_total_order']) ? (int)$c['grand_total_order'] : 0
        ];

        $menus = normalizeMenus(isset($c['menu_data']) ? $c['menu_data'] : []);
        foreach ($menus as $m) {
          $allItems[] = [
            'type' => 'menu_only',
            'menu_data' => $m,
            'kantins' => isset($c['kantins']) ? $c['kantins'] : [],
            'customer_name' => isset($c['customer_name']) ? $c['customer_name'] : ''
          ];
        }
      }

      // === LOGIKA FILL PAGE ===
      // Limit 65 untuk A4 Landscape
      $maxPageHeight = 65;
      $pages = buildPageGrids($allItems, $maxPageHeight);
      ?>

      <?php foreach ($pages as $pageIndex => $pg) : ?>
        <div class="page-wrapper">
          <!-- HEADER DI SETIAP HALAMAN -->
          <div class="header">
            <h1>MENU HARIAN REPORT PT RYAN CATERING</h1>
            <div class="sub">
              Shift: <strong><?= !empty($filter['shift']) ? strtoupper($filter['shift']) : 'SEMUA' ?></strong>
              &nbsp;|&nbsp;
              Tanggal: <strong><?= !empty($filter['tanggal']) ? date('d/m/Y', strtotime($filter['tanggal'])) : date('d/m/Y') ?></strong>
              <?php if (isset($grandTotalOrderAll) && $grandTotalOrderAll > 0) : ?>
                &nbsp;|&nbsp;
                <span style="background:#ffc107; color:#000; padding:1px 5px; border-radius:3px; font-weight:bold;">
                  Grand Total: <?= number_format($grandTotalOrderAll, 0, ',', '.') ?> porsi
                </span>
              <?php endif; ?>
              &nbsp;|&nbsp;
              Halaman: <strong><?= $pageIndex + 1 ?></strong>
            </div>
          </div>

          <!-- KONTEN GRID -->
          <table class="layout-grid">
            <tr>
              <!-- KOLOM KIRI -->
              <td class="grid-col-left">
                <?php foreach ($pg['left'] as $item) {
                  renderItem($item);
                } ?>
              </td>

              <!-- SPACER -->
              <td class="grid-col-spacer">&nbsp;</td>

              <!-- KOLOM KANAN -->
              <td class="grid-col-right">
                <?php
                if (!empty($pg['right'])) {
                  foreach ($pg['right'] as $item) {
                    renderItem($item);
                  }
                } else {
                  echo '&nbsp;';
                }
                ?>
              </td>
            </tr>
          </table>
        </div>
      <?php endforeach; ?>

    <?php else : ?>
      <!-- TAMPILAN JIKA TIDAK ADA DATA -->
      <div class="page-wrapper">
        <div class="header">
          <h1>MENU HARIAN REPORT PT RYAN CATERING</h1>
          <div class="sub">
            Shift: <strong><?= !empty($filter['shift']) ? strtoupper($filter['shift']) : 'SEMUA' ?></strong>
            &nbsp;|&nbsp;
            Tanggal: <strong><?= !empty($filter['tanggal']) ? date('d/m/Y', strtotime($filter['tanggal'])) : date('d/m/Y') ?></strong>
          </div>
        </div>
        <div class="no-data"><strong>⚠ Tidak Ada Data</strong></div>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>

<?php
// ==========================================
// FUNGSI BANTUAN PHP
// ==========================================

function normalizeMenus($menuData)
{
  $menus = [];
  if (empty($menuData)) return [];

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
    $menus[$menuKey]['kondimen_list'][] = $row;
  }
  return array_values($menus);
}

// Fungsi Estimasi Tinggi Item
function estimateItemHeight($item)
{
  $height = 0;

  if ($item['type'] === 'customer_header') {
    return 4;
  }

  if ($item['type'] === 'menu_only') {
    $height += 4;

    $kondimenList = isset($item['menu_data']['kondimen_list']) ? $item['menu_data']['kondimen_list'] : [];
    if (empty($kondimenList)) {
      $height += 2;
    } else {
      foreach ($kondimenList as $k) {
        $hRow = 1.2;
        $nama = isset($k['menu_kondimen']) ? $k['menu_kondimen'] : '';
        if (strlen($nama) > 35) {
          $hRow += 1.0;
        }
        $height += $hRow;
      }
    }
    return $height;
  }

  return 3;
}

// Logika Distribusi Halaman
function buildPageGrids($items, $pageLimit)
{
  $pages = [];
  $left = [];
  $right = [];
  $hL = 0;
  $hR = 0;

  foreach ($items as $it) {
    $h = estimateItemHeight($it);

    // KONDISI 1: Halaman baru (kosong) ATAU Muat di KIRI
    $isPageEmpty = (empty($left) && empty($right));

    if (($hL + $h <= $pageLimit) || ($isPageEmpty && $hL == 0)) {
      $left[] = $it;
      $hL += $h;
    }
    // KONDISI 2: Kiri Penuh, Muat di KANAN?
    elseif ($hR + $h <= $pageLimit) {
      $right[] = $it;
      $hR += $h;
    }
    // KONDISI 3: Semua Penuh -> Bikin Halaman Baru
    else {
      $pages[] = ['left' => $left, 'right' => $right];

      // Item ini masuk ke Kiri halaman baru
      $left = [$it];
      $right = [];
      $hL = $h;
      $hR = 0;
    }
  }

  // Sisa item
  if (!empty($left) || !empty($right)) {
    $pages[] = ['left' => $left, 'right' => $right];
  }

  return $pages;
}

function renderItem($item)
{
  if ($item['type'] === 'customer_header') {
    $grandTotal = isset($item['grand_total_order']) ? (int)$item['grand_total_order'] : 0;
?>
    <div class="item-wrapper" style="border:none; border-bottom:1px solid #000; margin-bottom:0;">
      <div class="customer-header-block">
        <span><?= htmlspecialchars($item['customer_name']) ?></span>
        <span class="total-order-badge">Total: <?= $grandTotal ?> porsi</span>
      </div>
    </div>
  <?php
  } elseif ($item['type'] === 'menu_only') {
    $cName = isset($item['customer_name']) ? $item['customer_name'] : '';
  ?>
    <div class="item-wrapper">
      <div class="customer-body">
        <?php renderMenuTable($item['menu_data'], $item['kantins'], $cName); ?>
      </div>
    </div>
  <?php
  }
}

function renderMenuTable($menu, $kantins, $customerName = '')
{
  $kondimenList = isset($menu['kondimen_list']) ? $menu['kondimen_list'] : [];

  $totalOrderMenu = 0;
  if (isset($menu['total_order_customer'])) $totalOrderMenu = intval($menu['total_order_customer']);
  elseif (isset($menu['total_orderan'])) $totalOrderMenu = intval($menu['total_orderan']);

  $hasSingleKantin = is_array($kantins) && count($kantins) === 1;
  $isSpecialSummary = $hasSingleKantin || isSpecialSummaryCustomer($customerName);
  ?>
  <div class="menu-box">
    <div class="menu-title">
      <?php
      $menuNameSafe = htmlspecialchars(isset($menu['nama_menu']) ? $menu['nama_menu'] : '-');
      $parts = ['<strong>' . $menuNameSafe . '</strong>'];

      if (!empty($menu['jenis_menu'])) {
        $parts[] = htmlspecialchars(strtoupper($menu['jenis_menu']));
      }
      if (!empty($menu['shift'])) {
        $parts[] = '<strong>' . htmlspecialchars(ucwords(strtolower($menu['shift']))) . '</strong>';
      }

      if (!empty($customerName)) {
        $parts[] = htmlspecialchars($customerName);
      }
      ?>
      <span class="menu-meta-line"><?= implode(' | ', $parts) ?></span>
    </div>

    <?php if ($isSpecialSummary) :
      $summaryParts = [];
      foreach ($kondimenList as $k) {
        $nama = isset($k['menu_kondimen']) ? $k['menu_kondimen'] : (isset($k['nama_kondimen']) ? $k['nama_kondimen'] : '-');
        $qty = 0;
        if (isset($k['total']) && $k['total'] !== '') $qty = intval($k['total']);
        elseif (isset($k['qty_per_kantin']) && is_array($k['qty_per_kantin'])) {
          $qty = array_sum(array_map('intval', $k['qty_per_kantin']));
        }

        if ($qty > 0) {
          $summaryParts[] = htmlspecialchars($nama) . ' (<strong>' . $qty . '</strong>)';
        }
      }
      $summaryText = !empty($summaryParts) ? implode(', ', $summaryParts) : '-';
    ?>
      <table class="special-summary-table">
        <thead>
          <tr>
            <th style="text-align:left; padding-left:5px;">Detail Kondimen</th>
            <th class="col-total" style="width:40px;">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="text-align:left; line-height:1.3; padding:4px;"><?= $summaryText ?></td>
            <td class="col-total" style="font-size:8pt;"><?= $totalOrderMenu ?></td>
          </tr>
        </tbody>
      </table>
    <?php else : ?>
      <table class="data-table">
        <thead>
          <tr>
            <th class="text-left col-kondimen">Kondimen</th>
            <?php foreach ($kantins as $k) : ?>
              <th><?= htmlspecialchars(substr($k, 0, 3)) ?></th>
            <?php endforeach; ?>
            <th style="width:25px;">JML</th>
            <th class="col-total-merged" style="width:30px;">ALL</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $rowNo = 1;
          $count = count($kondimenList);
          if ($count > 0) :
            foreach ($kondimenList as $idx => $kondimen) :
              $nama = isset($kondimen['menu_kondimen']) ? $kondimen['menu_kondimen'] : (isset($kondimen['nama_kondimen']) ? $kondimen['nama_kondimen'] : '-');
          ?>
              <tr>
                <td class="text-left col-kondimen-cell">
                  <span class="kondimen-number"><?= $rowNo++ ?>.</span>
                  <span class="kondimen-name"><?= htmlspecialchars($nama) ?></span>
                </td>
                <?php foreach ($kantins as $k) :
                  $q = (isset($kondimen['qty_per_kantin']) && isset($kondimen['qty_per_kantin'][$k])) ? intval($kondimen['qty_per_kantin'][$k]) : 0;
                ?>
                  <td class="text-center"><?= ($q > 0) ? $q : '' ?></td>
                <?php endforeach; ?>
                <td style="background:#f2f2f2; font-weight:bold; text-align:center;">
                  <?= (isset($kondimen['total']) && $kondimen['total'] > 0) ? $kondimen['total'] : '' ?>
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
              <td colspan="<?= 3 + count($kantins) ?>">No Data</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php
}

function isSpecialSummaryCustomer($name)
{
  if (empty($name)) return false;
  $n = strtolower($name);
  return (strpos($n, 'astra') !== false) && (strpos($n, 'daihatsu') !== false || strpos($n, 'dihatsu') !== false);
}
?>