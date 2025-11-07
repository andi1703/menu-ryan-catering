<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
  <style>
    /* ... styling responsif dan badge, bisa copy dari bahan ... */
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
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">Menu Harian</h4>
                  <p class="mb-0 text-muted">Kelola menu harian untuk setiap kantin dan shift</p>
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
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <h4 class="card-title mb-0">Daftar Menu Harian</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" onclick="tambah_menu_harian()">
                        <i class="fas fa-plus me-1"></i>Tambah Menu Harian
                      </button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div id="menu-harian-table-container">
                    <div class="table-responsive">
                      <!-- ID tabel sudah benar, JS akan mengisinya -->
                      <table class="table table-striped table-hover align-middle w-100" id="menu-harian-table" style="display: none;">
                        <thead class="table-dark">
                          <tr>
                            <th class="text-center">No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Customer</th>
                            <th>Kantin</th>
                            <th>Jenis Menu</th>
                            <th>Nama Menu</th>
                            <th>Kondimen Menu</th>
                            <th>Total Order/Kantin</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Data akan diisi oleh V_Menu_Harian_js.php -->
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
  <!-- jQuery harus di-load PALING ATAS sebelum JS lain -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <!-- Bootstrap JS jika dipakai -->
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <!-- Select2 JS jika dipakai -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Custom JS Anda (static asset) -->
  <script src="<?php echo base_url('assets_back/js/menu_harian.js'); ?>"></script>

</body>

</html>