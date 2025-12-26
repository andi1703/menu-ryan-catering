<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <style>
    /* =============================================
       MAIN TABLE STYLING
       ============================================= */
    #menu-harian-table {
      font-size: 0.85rem;
      /* ✅ DIKECILKAN */
      border-collapse: collapse;
    }

    #menu-harian-table thead th {
      background-color: #343a40;
      color: white;
      font-weight: 600;
      vertical-align: middle;
      text-align: center;
      border: 1px solid #23282d;
      padding: 10px 6px;
      /* ✅ PADDING DIKECILKAN */
      position: sticky;
      top: 0;
      z-index: 10;
      /* text-transform: uppercase; */
      letter-spacing: 0.3px;
      font-size: 0.8rem;
      /* ✅ DIKECILKAN */
    }

    #menu-harian-table tbody td {
      vertical-align: middle;
      border: 1px solid #dee2e6;
      padding: 8px 6px;
      /* ✅ PADDING DIKECILKAN */
    }

    #menu-harian-table tbody tr:hover {
      background-color: #f8f9fa;
      transition: background-color 0.2s ease;
    }

    /* =============================================
       NESTED TABLE KONDIMEN - COMPACT VERSION
       ============================================= */
    .nested-kondimen-wrapper {
      background-color: #f8f9fa;
      border-radius: 4px;
      /* ✅ DIKECILKAN */
      padding: 4px;
      /* ✅ DIKECILKAN */
      border: 1px solid #e1e4e8;
      max-width: 100%;
      overflow-x: auto;
      /* ✅ SCROLL HORIZONTAL JIKA PERLU */
    }

    .nested-kondimen-table {
      width: 100%;
      margin: 0;
      font-size: 0.7rem;
      border-collapse: separate;
      border-spacing: 0;
      background-color: white;
      border-radius: 3px;
      overflow: hidden;
      box-shadow: none;
      min-width: 600px;
      /* ✅ DIPERBESAR DARI 450px UNTUK ACCOMMODATE NAMA LENGKAP */
      table-layout: auto;
      /* ✅ AUTO LAYOUT UNTUK FLEXIBLE WIDTH */
    }

    /* ✅ HEADER NESTED TABLE - IMPROVED FOR LONG KANTIN NAMES */
    .nested-kondimen-table thead th {
      padding: 4px 2px;
      /* ✅ PADDING MINIMAL */
      font-weight: 700;
      font-size: 0.55rem;
      /* ✅ FONT DIPERKECIL LAGI */
      text-align: center;
      color: white !important;
      border: 1px solid #5a6268;
      letter-spacing: 0.2px;
      white-space: nowrap;
      line-height: 1.1;
      height: auto;
      /* ✅ AUTO HEIGHT */
      vertical-align: middle;
      /* ✅ VERTICAL ALIGN */
      word-break: break-word;
      /* ✅ BREAK WORD JIKA PERLU */
    }

    /* ✅ KOLOM QTY HEADERS - UNTUK NAMA KANTIN LENGKAP */
    .nested-kondimen-table thead th.col-qty-kantin {
      min-width: 60px;
      /* ✅ DIPERBESAR UNTUK NAMA LENGKAP */
      max-width: 100px;
      /* ✅ MAX WIDTH LEBIH BESAR */
      font-size: 0.55rem;
      /* ✅ FONT KECIL TAPI READABLE */
      padding: 3px 2px;
      /* ✅ PADDING MINIMAL */
      line-height: 1.1;
      white-space: normal;
      /* ✅ ALLOW WRAP UNTUK NAMA PANJANG */
      word-wrap: break-word;
      /* ✅ BREAK LONG WORDS */
      overflow-wrap: break-word;
      /* ✅ MODERN SYNTAX */
      text-align: center;
      vertical-align: middle;
      hyphens: auto;
      /* ✅ AUTO HYPHENATION */
    }

    /* ✅ KOLOM QTY BODY - SESUAIKAN */
    .nested-kondimen-table tbody td.col-qty {
      min-width: 60px;
      /* ✅ SESUAIKAN DENGAN HEADER */
      max-width: 100px;
      text-align: center;
      font-weight: 700;
      color: #28a745;
      font-size: 0.65rem;
      background-color: #f0fff4;
      padding: 3px 2px;
    }

    /* ✅ KOLOM # - FIXED WIDTH */
    .nested-kondimen-table .col-number {
      width: 30px;
      min-width: 30px;
      max-width: 30px;
      background-color: #e9ecef;
      font-weight: 700;
      color: #495057;
      font-size: 0.6rem;
    }

    /* ✅ KOLOM NAMA KONDIMEN - SESUAIKAN */
    .nested-kondimen-table .col-nama {
      width: 25%;
      /* ✅ DIKURANGI KARENA QTY COLUMN LEBIH LEBAR */
      text-align: left;
      padding-left: 6px;
      font-weight: 600;
      color: #2c3e50;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* ✅ KOLOM KATEGORI - SESUAIKAN */
    .nested-kondimen-table .col-kategori {
      width: 20%;
      /* ✅ DIKURANGI SEDIKIT */
      min-width: 80px;
      max-width: 120px;
      text-align: center;
    }

    /* ✅ BODY ROWS - COMPACT */
    .nested-kondimen-table tbody tr:nth-child(odd) {
      background-color: #f8f9fa;
    }

    .nested-kondimen-table tbody tr:nth-child(even) {
      background-color: #ffffff;
    }

    .nested-kondimen-table tbody tr:hover {
      background-color: #e7f3ff !important;
    }

    .nested-kondimen-table tbody td {
      padding: 4px 3px;
      /* ✅ PADDING SANGAT KECIL */
      border: 1px solid #e1e4e8;
      font-size: 0.7rem;
      /* ✅ FONT KECIL */
      line-height: 1.3;
    }

    /* ✅ KOLOM NAMA KONDIMEN - COMPACT */
    .nested-kondimen-table .col-nama {
      text-align: left;
      padding-left: 6px;
      font-weight: 600;
      color: #2c3e50;
      max-width: 120px;
      /* ✅ BATAS LEBAR */
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* ✅ KOLOM QTY - COMPACT */
    .nested-kondimen-table .col-qty {
      text-align: center;
      font-weight: 700;
      color: #28a745;
      font-size: 0.7rem;
      background-color: #f0fff4;
      width: 50px;
      /* ✅ LEBAR TETAP KECIL */
    }

    /* ✅ BADGE KATEGORI - MINI VERSION */
    .nested-kondimen-table .badge {
      font-size: 0.6rem !important;
      padding: 2px 4px !important;
      font-weight: 600 !important;
      border-radius: 3px !important;
      display: inline-block !important;
      line-height: 1.2 !important;
      white-space: nowrap !important;
      min-width: 20px !important;
      text-align: center !important;
    }

    /* ✅ BADGE COLORS - DENGAN !IMPORTANT */
    .badge-nasi {
      background-color: #ffc107 !important;
      color: #000 !important;
    }

    .badge-lauk-utama {
      background-color: #dc3545 !important;
      color: #fff !important;
    }

    .badge-pendamping-basah {
      background-color: #28a745 !important;
      color: #fff !important;
    }

    .badge-pendamping-kering {
      background-color: #17a2b8 !important;
      color: #fff !important;
    }

    .badge-tumisan {
      background-color: #6f42c1 !important;
      color: #fff !important;
    }

    .badge-sayuran-berkuah {
      background-color: #20c997 !important;
      color: #fff !important;
    }

    .badge-sambal {
      background-color: #fd7e14 !important;
      color: #fff !important;
    }

    .badge-buah {
      background-color: #e83e8c !important;
      color: #fff !important;
    }

    .badge-secondary {
      background-color: #6c757d !important;
      color: #fff !important;
    }

    /* =============================================
       MAIN TABLE COLUMN WIDTHS - ADJUSTED
       ============================================= */
    #menu-harian-table th:nth-child(1) {
      width: 3%;
    }

    /* No */
    #menu-harian-table th:nth-child(2) {
      width: 8%;
    }

    /* Tanggal */
    #menu-harian-table th:nth-child(3) {
      width: 6%;
    }

    /* Shift */
    #menu-harian-table th:nth-child(4) {
      width: 10%;
    }

    /* Customer */
    #menu-harian-table th:nth-child(5) {
      width: 8%;
    }

    /* Kantin */
    #menu-harian-table th:nth-child(6) {
      width: 7%;
    }

    /* Jenis Menu */
    #menu-harian-table th:nth-child(7) {
      width: 10%;
    }

    /* Nama Menu */
    #menu-harian-table th:nth-child(8) {
      width: 40%;
    }

    /* Kondimen Menu ✅ */
    #menu-harian-table th:nth-child(9) {
      width: 6%;
    }

    /* Total Order */
    #menu-harian-table th:nth-child(10) {
      width: 4%;
    }

    /* Aksi */
    #menu-harian-table th:nth-child(11) {
      width: 10%;
    }

    /* =============================================
       KANTIN & SHIFT BADGES - SMALLER
       ============================================= */
    .kantin-list {
      display: flex;
      flex-wrap: wrap;
      gap: 3px;
      /* ✅ GAP KECIL */
    }

    .kantin-badge {
      background-color: #007bff;
      color: white;
      padding: 3px 6px;
      /* ✅ PADDING KECIL */
      border-radius: 3px;
      font-size: 0.65rem;
      /* ✅ FONT KECIL */
      font-weight: 600;
      white-space: nowrap;
    }

    .shift-badge {
      display: inline-block;
      padding: 4px 8px;
      /* ✅ PADDING KECIL */
      border-radius: 4px;
      font-weight: 700;
      font-size: 0.65rem;
      /* ✅ FONT KECIL */
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .shift-lunch {
      background-color: #28a745;
      color: white;
    }

    .shift-dinner {
      background-color: #ffc107;
      color: #000;
    }

    .shift-supper {
      background-color: #6c757d;
      color: white;
    }

    /* =============================================
       TOTAL ORDER - COMPACT
       ============================================= */
    .total-order-wrapper {
      text-align: center;
    }

    .total-order-main {
      font-weight: 800;
      font-size: 1.1rem;
      /* ✅ DIKECILKAN */
      color: #007bff;
      display: block;
      margin-bottom: 4px;
      /* ✅ MARGIN KECIL */
    }

    .total-order-breakdown {
      font-size: 0.6rem;
      /* ✅ FONT SANGAT KECIL */
      color: #6c757d;
      line-height: 1.2;
    }

    /* =============================================
       ACTION BUTTONS - SMALLER
       ============================================= */
    .btn-action {
      padding: 4px 6px;
      /* ✅ PADDING KECIL */
      font-size: 0.7rem;
      /* ✅ FONT KECIL */
      margin: 0 1px;
      /* ✅ MARGIN KECIL */
      border-radius: 3px;
    }

    .btn-action i {
      font-size: 0.8rem;
    }

    /* =============================================
       RESPONSIVE - EXTRA SMALL
       ============================================= */
    @media (max-width: 768px) {
      #menu-harian-table {
        font-size: 0.7rem;
      }

      .nested-kondimen-table {
        font-size: 0.65rem;
        min-width: 500px;
        /* ✅ TETAP BESAR UNTUK MOBILE */
      }

      .nested-kondimen-table thead th.col-qty-kantin {
        font-size: 0.5rem;
        /* ✅ LEBIH KECIL DI MOBILE */
        padding: 2px 1px;
        min-width: 50px;
        /* ✅ SEDIKIT DIKECILKAN DI MOBILE */
      }

      .nested-kondimen-table tbody td.col-qty {
        font-size: 0.6rem;
        min-width: 50px;
      }

      .badge {
        font-size: 0.5rem;
        padding: 1px 3px;
      }
    }

    /* =============================================
       SCROLL HINT FOR NESTED TABLE
       ============================================= */
    .nested-kondimen-wrapper::after {
      content: "";
      display: block;
      height: 2px;
      background: linear-gradient(90deg, transparent 0%, #007bff 50%, transparent 100%);
      margin-top: 2px;
      opacity: 0.3;
    }

    /* =============================================
       SPECIAL HANDLING FOR VERY LONG KANTIN NAMES
       ============================================= */
    .nested-kondimen-table thead th.col-qty-kantin {
      /* Jika nama kantin sangat panjang, akan wrap ke baris baru */
      height: auto;
      max-height: 50px;
      /* ✅ BATAS TINGGI MAKSIMAL */
    }

    /* Agar teks remark wrap dan tidak overflow */
    #menu-harian-table th.remark-cell,
    #menu-harian-table td.remark-cell {
      white-space: pre-line;
      word-break: break-word;
      min-width: 120px;
      width: 20vw;
      max-width: 0vw;
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <input type="hidden" id="ajax_url" value="<?= site_url('back_menu_harian') ?>">

  <div id="layout-wrapper">
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    $this->load->view('back_partial/head-css');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between mb-3">
                <div>
                  <h4 class="mb-1 font-size-18">Menu Harian</h4>
                  <p class="mb-2 text-muted">Kelola menu untuk setiap kantin dan shift</p>
                  <?php
                  $today = new DateTime();
                  $dayOfWeek = $today->format('N');
                  $monday = clone $today;
                  $monday->modify('-' . ($dayOfWeek - 1) . ' days');
                  $sunday = clone $monday;
                  $sunday->modify('+6 days');
                  ?>
                  <span class="badge bg-warning text-black" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                    <i class="fas fa-calendar-week"></i>
                    Periode Menu Minggu ini: <?= $monday->format('d M Y') ?> - <?= $sunday->format('d M Y') ?>
                  </span>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item active">Menu Harian</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">
                    Daftar Menu Harian
                  </h5>
                  <button type="button" class="btn btn-primary" onclick="tambah_menu_harian()">
                    <i class="fas fa-plus me-1"></i>Tambah Menu Harian
                  </button>
                </div>

                <div class="card-body">
                  <div id="menu-harian-table-container">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover align-middle w-100" id="menu-harian-table" style="display: none;">
                        <thead class="table-dark">
                          <tr>
                            <th class="text-center" width="3%">No</th>
                            <th width="8%">Tanggal</th>
                            <th class="text-center" width="6%">Shift</th>
                            <th width="10%">Customer</th>
                            <th width="8%">Kantin</th>
                            <th class="text-center" width="7%">Jenis Menu</th>
                            <th width="10%">Nama Menu</th>
                            <th width="18%">Kondimen Menu</th>
                            <th class="text-center" width="7%">Total Order</th>
                            <th class="text-center remark-cell" width="22%">Remark</th>
                            <th class="text-center" width="4%">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Data dari JS -->
                          <tr>
                            <td class="text-center">1</td>
                            <td>
                              <div class="fw-semibold">01-01-2023</div>
                              <div class="text-muted small">Jam Input: 08:00</div>
                            </td>
                            <td class="text-center">Pagi</td>
                            <td>Customer A</td>
                            <td>Kantin 1</td>
                            <td class="text-center">Utama</td>
                            <td>Menu Spesial</td>
                            <td>
                              <div class="kantin-list">
                                <span class="badge badge-nasi">Nasi</span>
                                <span class="badge badge-lauk-utama">Ayam Penyet</span>
                                <span class="badge badge-pendamping-basah">Sop</span>
                                <span class="badge badge-pendamping-kering">Kerupuk</span>
                                <span class="badge badge-tumisan">Sayur Tumis</span>
                                <span class="badge badge-sayuran-berkuah">Sayur Kuah</span>
                                <span class="badge badge-sambal">Sambal</span>
                                <span class="badge badge-buah">Buah Segar</span>
                              </div>
                            </td>
                            <td class="text-center">50</td>
                            <td class="text-center remark-cell">
                              <div class="remark-content" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Siang hari, jangan lupa bawa payung!
                              </div>
                            </td>
                            <td class="text-center">
                              <button class="btn btn-sm btn-info" onclick="edit_menu_harian(this)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_menu_harian(this)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">2</td>
                            <td>
                              <div class="fw-semibold">02-01-2023</div>
                              <div class="text-muted small">Jam Input: 09:15</div>
                            </td>
                            <td class="text-center">Siang</td>
                            <td>Customer B</td>
                            <td>Kantin 2</td>
                            <td class="text-center">Pendamping</td>
                            <td>Menu Biasa</td>
                            <td>
                              <div class="kantin-list">
                                <span class="badge badge-nasi">Nasi</span>
                                <span class="badge badge-pendamping-kering">Kerupuk</span>
                                <span class="badge badge-sambal">Sambal</span>
                              </div>
                            </td>
                            <td class="text-center">30</td>
                            <td class="text-center remark-cell">
                              <div class="remark-content" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Perlu tambahan sambal pedas.
                              </div>
                            </td>
                            <td class="text-center">
                              <button class="btn btn-sm btn-info" onclick="edit_menu_harian(this)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_menu_harian(this)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">3</td>
                            <td>
                              <div class="fw-semibold">03-01-2023</div>
                              <div class="text-muted small">Jam Input: 11:20</div>
                            </td>
                            <td class="text-center">Malam</td>
                            <td>Customer C</td>
                            <td>Kantin 3</td>
                            <td class="text-center">Utama</td>
                            <td>Menu Spesial</td>
                            <td>
                              <div class="kantin-list">
                                <span class="badge badge-nasi">Nasi</span>
                                <span class="badge badge-lauk-utama">Ikan Bakar</span>
                                <span class="badge badge-pendamping-basah">Sayur Asem</span>
                                <span class="badge badge-pendamping-kering">Kerupuk</span>
                                <span class="badge badge-tumisan">Tumis Kangkung</span>
                                <span class="badge badge-sayuran-berkuah">Sayur Kuah</span>
                                <span class="badge badge-sambal">Sambal</span>
                                <span class="badge badge-buah">Buah Segar</span>
                              </div>
                            </td>
                            <td class="text-center">40</td>
                            <td class="text-center remark-cell">
                              <div class="remark-content" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Perlu ekstra kerupuk.
                              </div>
                            </td>
                            <td class="text-center">
                              <button class="btn btn-sm btn-info" onclick="edit_menu_harian(this)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_menu_harian(this)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">4</td>
                            <td>
                              <div class="fw-semibold">04-01-2023</div>
                              <div class="text-muted small">Jam Input: 07:45</div>
                            </td>
                            <td class="text-center">Pagi</td>
                            <td>Customer D</td>
                            <td>Kantin 1</td>
                            <td class="text-center">Pendamping</td>
                            <td>Menu Biasa</td>
                            <td>
                              <div class="kantin-list">
                                <span class="badge badge-nasi">Nasi</span>
                                <span class="badge badge-pendamping-kering">Kerupuk</span>
                                <span class="badge badge-sambal">Sambal</span>
                              </div>
                            </td>
                            <td class="text-center">20</td>
                            <td class="text-center remark-cell">
                              <div class="remark-content" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Tidak ada sambal.
                              </div>
                            </td>
                            <td class="text-center">
                              <button class="btn btn-sm btn-info" onclick="edit_menu_harian(this)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_menu_harian(this)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">5</td>
                            <td>
                              <div class="fw-semibold">05-01-2023</div>
                              <div class="text-muted small">Jam Input: 10:30</div>
                            </td>
                            <td class="text-center">Siang</td>
                            <td>Customer E</td>
                            <td>Kantin 2</td>
                            <td class="text-center">Utama</td>
                            <td>Menu Spesial</td>
                            <td>
                              <div class="kantin-list">
                                <span class="badge badge-nasi">Nasi</span>
                                <span class="badge badge-lauk-utama">Ayam Penyet</span>
                                <span class="badge badge-pendamping-basah">Sop</span>
                                <span class="badge badge-pendamping-kering">Kerupuk</span>
                                <span class="badge badge-tumisan">Sayur Tumis</span>
                                <span class="badge badge-sayuran-berkuah">Sayur Kuah</span>
                                <span class="badge badge-sambal">Sambal</span>
                                <span class="badge badge-buah">Buah Segar</span>
                              </div>
                            </td>
                            <td class="text-center">60</td>
                            <td class="text-center remark-cell">
                              <div class="remark-content" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                Siang hari, jangan lupa bawa payung!
                              </div>
                            </td>
                            <td class="text-center">
                              <button class="btn btn-sm btn-info" onclick="edit_menu_harian(this)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_menu_harian(this)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- Modal Form -->
  <?php $this->load->view('back/menu_harian/V_Menu_Harian_form'); ?>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>

  <!-- ✅ WAVES.JS HARUS SEBELUM APP.JS -->
  <script src="https://cdn.jsdelivr.net/npm/node-waves@0.7.6/dist/waves.min.js"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <?php $this->load->view('back/menu_harian/V_Menu_Harian_js'); ?>
</body>

</html>