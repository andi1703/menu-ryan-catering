<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet" />
  <style>
    .condimen-row {
      background: #fffbe6;
      font-weight: 600;
    }

    td,
    th {
      vertical-align: middle;
    }

    .w-120 {
      width: 120px;
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <div id="layout-wrapper">
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
              <h4 class="mb-0">Vegetable Calculator</h4>
              <small class="text-muted">Excel-like table: Kondimen + Bahan</small>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-1">Sesi Penghitungan Sayur</h5>
                <small class="text-muted">Daftar perhitungan yang sudah disimpan</small>
              </div>
              <div>
                <button class="btn btn-primary" id="btn_open_modal"><i class="ri-add-line me-1"></i> Tambahkan Penghitungan</button>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered align-middle" id="session_table">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center" style="width:60px;">No</th>
                      <th>Tanggal</th>
                      <th>Shift</th>
                      <th>Customer</th>
                      <th class="text-end">Jumlah Menu</th>
                      <th class="text-end">Jumlah Bahan</th>
                      <th class="text-center" style="width:160px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="session_body"></tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <script>
    window.BASE_URL = '<?php echo base_url(); ?>';
  </script>
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <?php $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator_Form'); ?>
  <?php $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator_js'); ?>
</body>

</html>