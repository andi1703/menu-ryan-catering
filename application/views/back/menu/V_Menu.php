<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->helper('url');
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>

  <!-- DataTables -->
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

  <!-- CUSTOM CSS -->
  <style>
    .dataTables_wrapper {
      display: block !important;
      visibility: visible !important;
      width: 100% !important;
    }

    .dataTables_wrapper .dataTables_paginate,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 0.375rem;
    }

    #datatable {
      width: 100% !important;
      min-width: 980px;
      display: none;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .badge {
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 0.375rem;
      padding: 0.35em 0.65em;
      color: #fff !important;
    }

    .badge.bg-info {
      background-color: #0dcaf0 !important;
    }

    .badge.bg-warning {
      background-color: #ffc107 !important;
      color: #000 !important;
    }

    .badge.bg-secondary {
      background-color: #6c757d !important;
    }

    .table thead th {
      vertical-align: middle;
    }

    .table tbody td {
      vertical-align: middle;
    }

    .no-image {
      width: 50px;
      height: 50px;
      background: #f8f9fa;
      border: 1px dashed #dee2e6;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      font-size: 10px;
      color: #6c757d;
    }

    .btn-group-sm .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
    }

    /* Custom Multi-Select Container */
    .multi-select-wrapper {
      position: relative;
      user-select: none;
    }

    /* The main input box */
    .multi-select-box {
      background-color: #ffffff;
      border: 1px solid #ced4da;
      border-radius: 6px;
      min-height: 45px;
      padding: 5px 10px;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      position: relative;
    }

    .multi-select-box.active {
      border-color: #86b7fe;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    /* The tags */
    .tag-badge {
      background-color: #e9ecef;
      color: #495057;
      padding: 4px 10px;
      border-radius: 4px;
      margin: 3px 6px 3px 0;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #dee2e6;
      font-weight: 500;
    }

    .tag-close {
      cursor: pointer;
      opacity: 0.6;
      font-size: 1.1em;
      color: #6c757d;
      transition: color 0.2s;
    }

    .tag-close:hover {
      opacity: 1;
      color: #dc3545;
    }

    /* Controls on the right */
    .controls {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #adb5bd;
    }

    .clear-all {
      cursor: pointer;
      font-size: 1.2rem;
      transition: color 0.2s;
    }

    .clear-all:hover {
      color: #495057;
    }

    .control-divider {
      width: 1px;
      height: 20px;
      background: #dee2e6;
      margin: 0 2px;
    }

    /* The Dropdown Menu */
    .options-menu {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background-color: #ffffff;
      border: 1px solid #ced4da;
      border-top: none;
      border-radius: 0 0 6px 6px;
      z-index: 1000;
      margin-top: 4px;
      display: none;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .options-menu.show {
      display: block;
    }

    /* Search input inside dropdown */
    .search-wrapper {
      padding: 10px;
      background-color: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }

    .custom-search-input {
      background-color: #ffffff;
      border: 1px solid #ced4da;
      color: #212529;
      width: 100%;
      padding: 8px 12px;
      border-radius: 4px;
      outline: none;
      transition: border-color 0.15s ease-in-out;
    }

    .custom-search-input:focus {
      border-color: #86b7fe;
    }

    /* List Items */
    .options-list {
      list-style: none;
      padding: 0;
      margin: 0;
      max-height: 250px;
      overflow-y: auto;
    }

    .option-item {
      padding: 10px 15px;
      cursor: pointer;
      color: #212529;
      transition: background 0.2s;
    }

    .option-item:hover {
      background-color: #f1f3f5;
    }

    .option-item.selected {
      background-color: #e9ecef;
      font-weight: 500;
    }

    .option-item.highlighted {
      background-color: #e7f1ff;
      color: #0d6efd;
      border-left: 3px solid #0d6efd;
    }

    /* Scrollbar styling */
    .options-list::-webkit-scrollbar {
      width: 8px;
    }

    .options-list::-webkit-scrollbar-track {
      background: #f8f9fa;
    }

    .options-list::-webkit-scrollbar-thumb {
      background: #ced4da;
      border-radius: 4px;
    }

    .options-list::-webkit-scrollbar-thumb:hover {
      background: #adb5bd;
    }

    .placeholder-text {
      color: #6c757d;
    }

    @media (max-width: 992px) {

      #datatable th:nth-child(7),
      #datatable td:nth-child(7) {
        display: none;
      }
    }

    @media (max-width: 768px) {
      #datatable {
        min-width: 680px;
        font-size: 0.875rem;
      }

      #datatable th:nth-child(5),
      #datatable td:nth-child(5) {
        display: none;
      }

      #datatable th:nth-child(6),
      #datatable td:nth-child(6) {
        display: none;
      }

      #datatable th:nth-child(2),
      #datatable td:nth-child(2) {
        display: none;
      }
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <input type="hidden" id="base_url" value="<?= base_url() ?>">
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
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">Menu Kondimen Management</h4>
                  <p class="mb-0 text-muted">Kelola kondimen menu catering</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                    <li class="breadcrumb-item active">Menu Kondimen</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <h4 class="card-title mb-0">Daftar Menu Kondimen</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" onclick="tambah_data()">
                        <i class="ri-add-circle-line me-2"></i>Tambah Menu Kondimen
                      </button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="table-responsive" id="menu-table-container">
                    <table id="datatable" class="table table-striped table-hover align-middle w-100">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-center">No</th>
                          <th class="text-center">Gambar</th>
                          <th>Nama Kondimen</th>
                          <th>Kategori</th>
                          <th>Thematik</th>
                          <th>Bahan Utama</th>
                          <th>Deskripsi Resep</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_menu">
                        <!-- Data dari AJAX -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Include Modal Form -->
          <?php $this->load->view('back/menu/V_Menu_form'); ?>

          <!-- previewImage -->
          <?php $this->load->view('back/menu/preview_image'); ?>

          <!-- Modal Preview Deskripsi dipisah ke file khusus -->
          <?php $this->load->view('back/menu/PreviewDeskMenu'); ?>

          <!-- JAVASCRIPT -->
          <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net-buttons/js/dataTables.buttons.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
          <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

          <?php $this->load->view('back/menu/V_Menu_js'); ?>

</body>

</html>