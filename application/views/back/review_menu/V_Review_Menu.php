<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

  <style>
    /* =============================================
       GRID CARD STYLING
       ============================================= */
    .menu-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      height: 100%;
      cursor: pointer;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .menu-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .menu-card-img-wrapper {
      height: 240px;
      overflow: hidden;
      position: relative;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .menu-card-img {
      height: 100%;
      width: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .menu-card:hover .menu-card-img {
      transform: scale(1.1);
    }

    .menu-card-img-placeholder {
      height: 240px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 5rem;
    }

    .menu-card-body {
      padding: 1.25rem;
    }

    .menu-card-title {
      font-size: 1.15rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 0.75rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      min-height: 2.8rem;
    }

    .menu-info-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.75rem;
      padding: 0.35rem 0.6rem;
      border-radius: 4px;
      font-weight: 600;
    }

    .kondimen-list-container {
      max-height: 200px;
      overflow-y: auto;
      margin-top: 0.75rem;
      padding-right: 0.25rem;
    }

    .kondimen-list-container::-webkit-scrollbar {
      width: 6px;
    }

    .kondimen-list-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .kondimen-list-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .kondimen-list-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    .kondimen-item {
      font-size: 0.875rem;
      padding: 0.5rem 0;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .kondimen-item:last-child {
      border-bottom: none;
    }

    .kondimen-name {
      flex: 1;
      font-weight: 500;
      color: #495057;
    }

    .kondimen-more {
      text-align: center;
      padding: 0.5rem;
      background: #f8f9fa;
      border-radius: 4px;
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: #6c757d;
      font-weight: 600;
    }

    .card-footer-custom {
      background: #f8f9fa;
      border-top: 1px solid #e9ecef;
      padding: 0.75rem 1.25rem;
    }

    /* =============================================
       BADGE KATEGORI - SAME AS MENU HARIAN
       ============================================= */
    .badge-kategori {
      font-size: 0.7rem !important;
      padding: 3px 6px !important;
      font-weight: 600 !important;
      border-radius: 3px !important;
      white-space: nowrap !important;
    }

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

    /* =============================================
       PAGINATION
       ============================================= */
    .pagination-wrapper {
      margin-top: 2rem;
      padding: 1.5rem;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .pagination-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: #6c757d;
    }

    .pagination {
      margin-bottom: 0;
    }

    .page-link {
      color: #667eea;
      border-color: #dee2e6;
    }

    .page-link:hover {
      color: #764ba2;
      background-color: #f8f9fa;
      border-color: #dee2e6;
    }

    .page-item.active .page-link {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-color: #667eea;
    }

    #per-page-select {
      font-weight: 600;
      border: 1px solid #dee2e6;
      text-align: center;
      padding: 0.25rem 0.5rem;
    }

    #per-page-select:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* =============================================
       FILTER SECTION
       ============================================= */
    .filter-card {
      border: none;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 2rem;
    }

    .filter-card .card-header {
      background: #2a3042;
      color: white;
      font-weight: 600;
      border: none;
    }

    /* =============================================
       LOADING & EMPTY STATE
       ============================================= */
    .loading-state,
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
    }

    .loading-state i,
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    /* Circle Loader */
    .circle-loader {
      width: 60px;
      height: 60px;
      display: inline-block;
      position: relative;
      margin: 0 auto;
    }

    .circle-loader .dot {
      width: 12px;
      height: 12px;
      background: #667eea;
      border-radius: 50%;
      position: absolute;
      animation: circle-loader 1.2s linear infinite;
    }

    .circle-loader .dot:nth-child(1) {
      top: 0;
      left: 50%;
      margin-left: -6px;
      animation-delay: -0.9s;
    }

    .circle-loader .dot:nth-child(2) {
      top: 6px;
      right: 6px;
      animation-delay: -0.8s;
    }

    .circle-loader .dot:nth-child(3) {
      top: 50%;
      right: 0;
      margin-top: -6px;
      animation-delay: -0.7s;
    }

    .circle-loader .dot:nth-child(4) {
      bottom: 6px;
      right: 6px;
      animation-delay: -0.6s;
    }

    .circle-loader .dot:nth-child(5) {
      bottom: 0;
      left: 50%;
      margin-left: -6px;
      animation-delay: -0.5s;
    }

    .circle-loader .dot:nth-child(6) {
      bottom: 6px;
      left: 6px;
      animation-delay: -0.4s;
    }

    .circle-loader .dot:nth-child(7) {
      top: 50%;
      left: 0;
      margin-top: -6px;
      animation-delay: -0.3s;
    }

    .circle-loader .dot:nth-child(8) {
      top: 6px;
      left: 6px;
      animation-delay: -0.2s;
    }

    @keyframes circle-loader {

      0%,
      20%,
      80%,
      100% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.5);
        opacity: 0.5;
      }
    }

    /* =============================================
       MODAL DETAIL
       ============================================= */
    .modal-header-custom {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
    }

    .modal-img-preview {
      max-width: 400px;
      width: 100%;
      height: auto;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .detail-table {
      font-size: 0.9rem;
    }

    .detail-table th {
      width: 35%;
      font-weight: 600;
      color: #495057;
    }

    .kondimen-table-detail {
      font-size: 0.85rem;
    }

    .kondimen-table-detail thead {
      background: #343a40;
      color: white;
    }

    /* =============================================
       RESPONSIVE
       ============================================= */
    @media (max-width: 768px) {

      .menu-card-img-wrapper,
      .menu-card-img-placeholder {
        height: 180px;
      }

      .menu-card-title {
        font-size: 1rem;
      }

      .filter-card .row {
        row-gap: 0.75rem;
      }
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <input type="hidden" id="ajax_url" value="<?= site_url('back_review_menu') ?>">

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
              <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
                <div>
                  <h4 class="mb-1 font-size-18">
                    Review Menu
                  </h4>
                  <p class="mb-0 text-muted">Katalog menu harian yang telah dibuat</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item active">Review Menu</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Section -->
          <div class="row">
            <div class="col-12">
              <div class="card filter-card">
                <div class="card-header">
                  Filter Menu
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-3">
                      <label class="form-label fw-semibold">Customer</label>
                      <select id="filter-customer" class="form-select select2-dropdown">
                        <option value="">-- Pilih Customer --</option>
                        <?php foreach ($customers as $customer) : ?>
                          <option value="<?= $customer['id_customer'] ?>"><?= $customer['nama_customer'] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-semibold">Jenis Menu</label>
                      <select id="filter-jenis" class="form-select select2-dropdown">
                        <option value="">-- Pilih Jenis Menu --</option>
                        <option value="regular">Regular</option>
                        <option value="paket">Paket</option>
                        <option value="sehat">Sehat</option>
                        <option value="staff">Staff</option>
                      </select>
                    </div>
                    <div class="col-md-5">
                      <label class="form-label fw-semibold">Cari Menu</label>
                      <input type="text" id="filter-search" class="form-control" placeholder="Ketik nama menu...">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                      <button id="btn-reset-filter" class="btn btn-secondary w-100" title="Reset Filter">
                        <i class="fas fa-redo"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Grid Cards Container -->
          <div class="row" id="menu-grid-container">
            <!-- Cards will be loaded here by JavaScript -->
            <div class="col-12 loading-state">
              <div class="circle-loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
              </div>
              <p class="mt-3 text-muted fs-5">Memuat data menu...</p>
            </div>
          </div>

          <!-- Pagination -->
          <div class="row" id="pagination-wrapper" style="display: none;">
            <div class="col-12">
              <div class="pagination-wrapper">
                <div class="pagination-info">
                  <div>
                    <span id="pagination-info-text">Menampilkan 0 dari 0 menu</span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Tampilkan</span>
                    <select id="per-page-select" class="form-select form-select-sm" style="width: 70px;">
                      <option value="8">8</option>
                      <option value="12" selected>12</option>
                      <option value="16">16</option>
                      <option value="20">20</option>
                      <option value="24">24</option>
                      <option value="50">50</option>
                    </select>
                    <span class="text-muted">data per halaman</span>
                  </div>
                </div>
                <nav>
                  <ul class="pagination justify-content-center mb-0" id="pagination-controls">
                    <!-- Pagination buttons will be generated here -->
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- Detail Modal -->
  <div class="modal fade" id="modal-detail-menu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header modal-header-custom">
          <h5 class="modal-title">
            <!-- <i class="fas fa-utensils me-2"></i> -->
            <span id="modal-title-text" style="color: white ;">Detail Menu</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modal-detail-content">
          <!-- Content loaded by JavaScript -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/node-waves@0.7.6/dist/waves.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
</body>

</html>