<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>MENU HARIAN REPORT</title>
  <style>
    /* 1. SETUP HALAMAN HEMAT KERTAS */
    @page {
      size: A4 landscape;
      margin: 5mm 5mm 5mm 5mm;
      /* Margin tepi sangat tipis (5mm) */
    }

    body {
      font-family: 'Helvetica', Arial, sans-serif;
      font-size: 7pt;
      /* Font dasar diperkecil */
      color: #000;
      line-height: 1.1;
      /* Jarak antar baris teks dirapatkan */
    }

    /* HEADER */
    .header {
      text-align: center;
      margin-bottom: 5px;
      /* Jarak ke konten diperkecil */
      border-bottom: 2px solid #000;
      padding-bottom: 2px;
    }

    .header h1 {
      font-size: 12pt;
      /* Judul tidak perlu terlalu besar */
      font-weight: bold;
      margin: 0;
      text-transform: uppercase;
    }

    .header .sub {
      font-size: 8pt;
      margin-top: 2px;
    }

    /* === LAYOUT 2 KOLOM (FLOAT LEFT SEMUA) === */
    /* Menggunakan float:left untuk kedua kolom agar sejajar di atas */
    .main-column {
      width: 49%;
      float: left;
    }

    /* Memberi jarak sedikit untuk kolom kiri agar tidak nempel dengan kolom kanan */
    .col-spacer {
      width: 2%;
      float: left;
      height: 1px;
    }

    /* WRAPPER CUSTOMER */
    .customer-wrapper {
      width: 100%;
      margin-bottom: 10px;
      /* Jarak antar customer diperkecil */
      border: 1px solid #000;
      /* Kotak pembungkus customer */
      page-break-inside: avoid;
      /* Usahakan satu blok customer tidak terpotong */
    }

    .customer-title {
      background-color: #007bff;
      color: #fff;
      font-weight: bold;
      font-size: 8.5pt;
      padding: 3px 5px;
      /* Padding diperkecil */
      text-transform: uppercase;
      border-bottom: 1px solid #000;
    }

    .customer-body {
      padding: 2px;
      background-color: #fff;
    }

    /* MENU ITEM */
    .menu-item-box {
      width: 100%;
      margin-bottom: 5px;
      /* Jarak antar menu sangat rapat */
      border: 1px solid #999;
    }

    /* Menghilangkan margin bawah untuk menu terakhir */
    .menu-item-box:last-child {
      margin-bottom: 0;
    }

    .menu-header {
      background-color: #e9f2fb;
      border-bottom: 1px solid #999;
      padding: 2px;
      text-align: center;
      font-weight: bold;
      font-size: 7.5pt;
      color: #000;
      text-transform: uppercase;
    }

    /* TABEL */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 6.5pt;
      /* Font tabel sangat kecil agar muat */
    }

    th,
    td {
      border: 1px solid #999;
      padding: 1px 2px;
      /* Padding sel sangat tipis */
      text-align: center;
      vertical-align: middle;
    }

    th {
      background-color: #333;
      color: #fff;
      font-weight: bold;
      font-size: 7pt;
    }

    /* Angka Qty */
    .val-qty {
      font-weight: normal;
      color: #000;
    }

    /* Kolom Total Order Merged */
    .col-total-merged {
      background-color: #ffe699;
      color: #000;
      font-weight: bold;
      font-size: 8pt;
    }

    .text-left {
      text-align: left;
      padding-left: 3px;
    }

    .no-data {
      text-align: center;
      padding: 10px;
      border: 1px dashed #ccc;
      clear: both;
    }

    /* Helper Clearfix */
    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>MENU HARIAN REPORT PT RYAN CATERING</h1>
    <div class="sub">
      Shift: <strong><?= !empty($filter['shift']) ? strtoupper($filter['shift']) : 'SEMUA' ?></strong>
      &nbsp;|&nbsp;
      Tanggal: <strong><?= !empty($filter['tanggal']) ? date('d/m/Y', strtotime($filter['tanggal'])) : date('d/m/Y') ?></strong>
    </div>
  </div>

  <?php if (!empty($groupedByCustomer)) : ?>

    <?php
    // 1. SPLIT DATA (GANJIL GENAP) UNTUK MASONRY
    $leftColumnData = [];
    $rightColumnData = [];
    $counter = 0;

    foreach ($groupedByCustomer as $c) {
      if ($counter % 2 == 0) {
        $leftColumnData[] = $c;
      } else {
        $rightColumnData[] = $c;
      }
      $counter++;
    }
    ?>

    <div class="clearfix">

      <!-- KOLOM KIRI (49%) -->
      <div class="main-column">
        <?php foreach ($leftColumnData as $customerData) : ?>
          <?php renderCustomerBlockCompact($customerData); ?>
        <?php endforeach; ?>
      </div>

      <!-- SPACER (2%) -->
      <div class="col-spacer"></div>

      <!-- KOLOM KANAN (49%) -->
      <div class="main-column">
        <?php foreach ($rightColumnData as $customerData) : ?>
          <?php renderCustomerBlockCompact($customerData); ?>
        <?php endforeach; ?>
      </div>

    </div>

  <?php else : ?>
    <div class="no-data">
      <strong>⚠ Tidak Ada Data</strong>
    </div>
  <?php endif; ?>

</body>

</html>

<?php
function renderCustomerBlockCompact($customerData)
{
?>
  <div class="customer-wrapper">
    <div class="customer-title">
      <?= htmlspecialchars($customerData['customer_name']) ?>
    </div>

    <div class="customer-body">
      <?php
      $menus = [];
      if (isset($customerData['menu_data']) && is_array($customerData['menu_data'])) {
        // Cek struktur data (Nested vs Flat)
        $firstItem = reset($customerData['menu_data']);
        if (isset($firstItem['kondimen_list'])) {
          $menus = $customerData['menu_data'];
        } else {
          // Fallback Grouping
          foreach ($customerData['menu_data'] as $row) {
            $menuKey = $row['nama_menu'] . '_' . $row['jenis_menu'];
            if (!isset($menus[$menuKey])) {
              $menus[$menuKey] = [
                'nama_menu' => $row['nama_menu'],
                'jenis_menu' => $row['jenis_menu'],
                'kondimen_list' => []
              ];
            }
            $row['menu_kondimen'] = isset($row['menu_kondimen']) ? $row['menu_kondimen'] : (isset($row['nama_kondimen']) ? $row['nama_kondimen'] : '-');
            $menus[$menuKey]['kondimen_list'][] = $row;
          }
          $menus = array_values($menus);
        }
      }
      ?>

      <?php foreach ($menus as $menu) :
        $kondimenList = isset($menu['kondimen_list']) ? $menu['kondimen_list'] : [];

        // Hitung Total Order Lauk Utama
        $totalOrderMenu = 0;
        $foundLaukUtama = false;
        foreach ($kondimenList as $k) {
          $kQty = isset($k['total']) ? intval($k['total']) : 0;
          $kat = isset($k['kategori']) ? $k['kategori'] : '';
          if (stripos($kat, 'lauk utama') !== false) {
            $totalOrderMenu += $kQty;
            $foundLaukUtama = true;
          }
        }
        if (!$foundLaukUtama && !empty($kondimenList)) {
          foreach ($kondimenList as $k) {
            $kQty = isset($k['total']) ? intval($k['total']) : 0;
            if ($kQty > $totalOrderMenu) $totalOrderMenu = $kQty;
          }
        }
      ?>
        <!-- KOTAK MENU -->
        <div class="menu-item-box">
          <div class="menu-header">
            <?= htmlspecialchars($menu['nama_menu'] ?? '-') ?>
            <?php if (!empty($menu['jenis_menu'])) : ?>
              <span style="font-weight:normal; font-size: 6.5pt;">(<?= strtoupper($menu['jenis_menu']) ?>)</span>
            <?php endif; ?>
          </div>

          <table>
            <thead>
              <tr>
                <th style="width:12px;">#</th>
                <th class="text-left">Kondimen</th>
                <?php foreach ($customerData['kantins'] as $kantin) : ?>
                  <th><?= htmlspecialchars($kantin) ?></th>
                <?php endforeach; ?>
                <th style="width:20px;">JML</th>
                <th class="col-total-merged" style="width:25px;">ORD</th>
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
                    <td><?= $rowNo++ ?></td>
                    <td class="text-left"><?= htmlspecialchars($namaKondimen) ?></td>
                    <?php foreach ($customerData['kantins'] as $kantin) :
                      $qtySafe = 0;
                      if (isset($kondimen['qty_per_kantin']) && is_array($kondimen['qty_per_kantin'])) {
                        $qtySafe = isset($kondimen['qty_per_kantin'][$kantin]) ? intval($kondimen['qty_per_kantin'][$kantin]) : 0;
                      }
                    ?>
                      <td><?= ($qtySafe > 0) ? $qtySafe : '' ?></td>
                    <?php endforeach; ?>

                    <td style="font-weight:bold; background-color:#f2f2f2;">
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
                  <td colspan="<?= 4 + count($customerData['kantins']) ?>">Empty</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
}
?>