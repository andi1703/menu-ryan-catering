<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>

  <!-- DataTables & CSS -->
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <style>
    /* DataTables Visibility Fix */
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

    /* Table Responsiveness */
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    #bahan-table {
      width: 100% !important;
      min-width: 800px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      #bahan-table {
        min-width: 600px;
        font-size: 0.875rem;
      }

      .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }

      .badge {
        font-size: 0.65rem;
        padding: 0.25em 0.5em;
      }
    }

    /* Hide columns on smaller screens */
    @media (max-width: 992px) {

      .table th:nth-child(4),
      .table td:nth-child(4) {
        display: none;
        /* Hide Harga Awal */
      }
    }

    @media (max-width: 768px) {

      .table th:nth-child(3),
      .table td:nth-child(3),
      .table th:nth-child(6),
      .table td:nth-child(6) {
        display: none;
        /* Hide Satuan and Perubahan */
      }
    }

    /* Badge Styling */
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

    .badge.bg-success {
      background-color: #198754 !important;
    }

    .badge.bg-danger {
      background-color: #dc3545 !important;
    }

    .badge.bg-secondary {
      background-color: #6c757d !important;
    }

    .badge.bg-warning {
      background-color: #ffc107 !important;
      color: #000 !important;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table-responsive {
      border-radius: 0.375rem;
    }

    .badge {
      font-size: 0.75em;
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <!-- Hidden input for base URL -->
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
                  <h4 class="mb-0 font-size-18">Data Master Bahan Baku</h4>
                  <p class="mb-0 text-muted">Kelola dan pantau semua bahan baku untuk produksi menu</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Data Master</a></li>
                    <li class="breadcrumb-item active">Bahan Baku</li>
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
                      <h4 class="card-title mb-0">Daftar Bahan Baku</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" onclick="tambah_bahan()">
                        <i class="fas fa-plus me-1"></i>Tambah Bahan Baru
                      </button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div id="bahan-table-container">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover align-middle w-100" id="bahan-table" style="display: none;">
                        <!-- Table tetap sama, tapi hidden dulu -->
                        <thead class="table-dark">
                          <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Nama Bahan</th>
                            <th scope="col" class="text-center">Satuan</th>
                            <th scope="col" class="text-end">Harga Awal</th>
                            <th scope="col" class="text-end">Harga Sekarang</th>
                            <th scope="col" class="text-center">Perubahan</th>
                            <th scope="col" class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($bahan_list)) : ?>
                            <?php foreach ($bahan_list as $index => $bahan) : ?>
                              <tr>
                                <td class="text-center fw-bold"></td>
                                <td class="fw-semibold"><?= htmlspecialchars($bahan['nama_bahan']) ?></td>
                                <td class="text-center">
                                  <span class="badge bg-info"><?= htmlspecialchars($bahan['nama_satuan']) ?></span>
                                </td>
                                <td class="text-end">Rp <?= number_format($bahan['harga_awal'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($bahan['harga_sekarang'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                  <?php
                                  $persen_perubahan = $bahan['harga_awal'] > 0 ?
                                    (($bahan['harga_sekarang'] - $bahan['harga_awal']) / $bahan['harga_awal']) * 100 : 0;
                                  ?>
                                  <?php if ($persen_perubahan > 0) : ?>
                                    <span class="badge bg-danger">
                                      <i class="fas fa-arrow-up"></i> +<?= number_format($persen_perubahan, 1) ?>%
                                    </span>
                                  <?php elseif ($persen_perubahan < 0) : ?>
                                    <span class="badge bg-success">
                                      <i class="fas fa-arrow-down"></i> <?= number_format($persen_perubahan, 1) ?>%
                                    </span>
                                  <?php else : ?>
                                    <span class="badge bg-secondary">
                                      <i class="fas fa-minus"></i> 0%
                                    </span>
                                  <?php endif; ?>
                                </td>
                                <td class="text-center">
                                  <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-warning" onclick="edit_bahan(<?= $bahan['id_bahan'] ?>)" title="Edit">
                                      <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="hapus_bahan(<?= $bahan['id_bahan'] ?>)" title="Hapus">
                                      <i class="fas fa-trash"></i>
                                    </button>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- container-fluid -->
      </div>
      <!-- End Page-content -->

      <?php $this->load->view('back_partial/footer'); ?>
    </div>
    <!-- end main content-->

  </div>
  <!-- END layout-wrapper -->

  <!-- Modal Form -->
  <?php $this->load->view('back/bahan/V_Bahan_form'); ?>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>

  <!-- DataTables JS -->
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>

  <!-- SweetAlert2 -->
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

  <!-- App js -->
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <!-- Custom JS -->
  <?php $this->load->view('back/bahan/V_Bahan_js'); ?>

</body>

</html>