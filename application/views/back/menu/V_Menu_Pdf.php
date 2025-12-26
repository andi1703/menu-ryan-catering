<?php

/** @var array $menu */
/** @var string $base_url */
$namaMenu = isset($menu['menu_nama']) ? $menu['menu_nama'] : '';
$gambar   = isset($menu['menu_gambar']) ? $menu['menu_gambar'] : '';
$bahan    = isset($menu['bahan_utama']) && is_array($menu['bahan_utama']) ? $menu['bahan_utama'] : [];
$desk     = isset($menu['menu_deskripsi']) ? $menu['menu_deskripsi'] : '';

// Build image URL
$imageUrl = '';
if (!empty($gambar)) {
  $imageUrl = rtrim($base_url, '/') . '/file/products/menu/' . $gambar;
}
// Logo URL (letterhead)
$logoUrl = rtrim($base_url, '/') . '/file/logo/logo.png';

// Escape text for safe HTML
function h($s)
{
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
$deskHtml = nl2br(h($desk));
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title><?= h($namaMenu) ?> - PDF</title>
  <style>
    @page {
      margin: 10mm;
      margin-top: 6mm;
    }

    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 12px;
      color: #222;
    }

    .letterhead {
      display: table;
      width: 100%;
      margin-bottom: 8px;
    }

    .lh-left {
      display: table-cell;
      vertical-align: middle;
      width: 70%;
    }

    .lh-right {
      display: table-cell;
      vertical-align: middle;
      width: 30%;
      text-align: right;
    }

    .lh-right img {
      max-height: 72px;
    }

    .lh-divider {
      height: 2px;
      background: #495057;
      margin: 6px 0 12px;
    }

    .title {
      font-size: 20px;
      font-weight: 700;
      margin: 8px 0;
    }

    .print-meta {
      font-size: 10px;
      color: #888;
      margin-top: 2px;
    }

    .image-box {
      text-align: center;
      margin: 8px 0 14px;
    }

    .image-box img {
      max-height: 220px;
      max-width: 100%;
      object-fit: cover;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .section {
      margin-bottom: 10px;
    }

    .section-title {
      font-weight: 600;
      font-size: 13px;
      margin-bottom: 6px;
      color: #555;
    }

    .bahan-list {
      margin: 0;
      padding-left: 18px;
    }

    .desc-box {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 6px;
      padding: 10px;
      white-space: pre-wrap;
    }

    .muted {
      color: #777;
      font-style: italic;
    }

    .footer {
      position: fixed;
      bottom: 10mm;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 10px;
      color: #888;
    }
  </style>
</head>

<body>
  <div class="letterhead">
    <div class="lh-left">
      <div class="title">Resep Menu PT Ryan Catering</div>
      <div class="print-meta">Dicetak dari sistem Menu PT Ryan Catering — <?= date('d/m/Y H:i') ?></div>
    </div>
    <div class="lh-right">
      <img src="<?= h($logoUrl) ?>" alt="Logo">
    </div>
  </div>
  <div class="lh-divider"></div>

  <div class="section">
    <div class="section-title">Nama Menu:</div>
    <div><strong><?= h($namaMenu) ?></strong></div>
  </div>

  <div class="image-box">
    <?php if ($imageUrl) : ?>
      <img src="<?= h($imageUrl) ?>" alt="Gambar <?= h($namaMenu) ?>">
    <?php else : ?>
      <div class="muted">Tidak ada gambar</div>
    <?php endif; ?>
  </div>

  <div class="section">
    <div class="section-title">Bahan Utama:</div>
    <?php if (!empty($bahan)) : ?>
      <ol class="bahan-list">
        <?php foreach ($bahan as $i => $nama) : ?>
          <li><?= h($nama) ?></li>
        <?php endforeach; ?>
      </ol>
    <?php else : ?>
      <div class="muted">Tidak ada bahan utama</div>
    <?php endif; ?>
  </div>

  <div class="section">
    <div class="section-title">Deskripsi / Resep / Cara Membuat:</div>
    <div class="desc-box">
      <?php if (trim($desk) !== '') : ?>
        <?= $deskHtml ?>
      <?php else : ?>
        <span class="muted">Tidak ada deskripsi / resep untuk menu ini.</span>
      <?php endif; ?>
    </div>
  </div>


</body>

</html>