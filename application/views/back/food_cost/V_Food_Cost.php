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

  <style>
    .table thead th {
      background-color: #f8f9fa;
      color: #212529;
      font-weight: 600;
      border-bottom: 2px solid #dee2e6;
    }

    .food-cost-card {
      border-left: 4px solid #28a745;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .bahan-row {
      border-bottom: 1px solid #f1f1f1;
      padding: 10px 0;
    }

    .bahan-row:last-child {
      border-bottom: none;
    }

    .calculation-box {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-top: 15px;
    }

    .calculation-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      padding: 5px 0;
      color: #333;
    }

    .calculation-item.total {
      border-top: 2px solid #007bff;
      font-weight: bold;
      color: #007bff;
      margin-top: 10px;
      padding-top: 10px;
    }

    .btn-add-bahan {
      background: #28a745;
      border-color: #28a745;
    }

    .btn-remove-bahan {
      background: #dc3545;
      border-color: #dc3545;
    }

    .currency-input {
      position: relative;
    }

    .currency-input::before {
      content: "Rp";
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      z-index: 1;
    }

    .currency-input input {
      padding-left: 35px;
    }

    .highlight-calculation {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 10px;
      padding: 20px;
      margin: 20px 0;
    }

    .calculation-summary {
      background: white;
      border-radius: 8px;
      padding: 15px;
      margin-top: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Fix untuk warna text di calculation summary */
    .calculation-summary .calculation-item {
      color: #333 !important;
    }

    .calculation-summary .calculation-item span {
      color: #333 !important;
    }

    .calculation-summary .calculation-item.total {
      color: #007bff !important;
    }

    .calculation-summary .calculation-item.total span {
      color: #007bff !important;
    }

    /* Pastikan semua text dalam calculation summary terlihat */
    .calculation-summary * {
      color: #333 !important;
    }

    .calculation-summary .total * {
      color: #007bff !important;
    }

    /* ID specific styling */
    #calc-total-bahan,
    #calc-biaya-produksi {
      color: #333 !important;
      font-weight: 600;
    }

    #calc-food-cost {
      color: #007bff !important;
      font-weight: 700;
      font-size: 1.1em;
    }

    /* Custom Modal Size - Lebih Tinggi */
    #modal-food-cost .modal-dialog {
      max-width: 90%;
      max-height: 95vh;
      margin: 1rem auto;
    }

    #modal-food-cost .modal-content {
      height: 90vh;
      display: flex;
      flex-direction: column;
    }

    #modal-food-cost .modal-body {
      flex: 1;
      overflow-y: auto;
      max-height: calc(90vh - 120px);
      /* Kurangi tinggi header dan footer */
      padding: 1.5rem;
    }

    #modal-food-cost .modal-header {
      flex-shrink: 0;
      border-bottom: 1px solid #dee2e6;
      padding: 1rem 1.5rem;
    }

    #modal-food-cost .modal-footer {
      flex-shrink: 0;
      border-top: 1px solid #dee2e6;
      padding: 1rem 1.5rem;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
      #modal-food-cost .modal-dialog {
        max-width: 95%;
        margin: 0.5rem auto;
      }

      #modal-food-cost .modal-content {
        height: 95vh;
      }

      #modal-food-cost .modal-body {
        max-height: calc(95vh - 120px);
        padding: 1rem;
      }
    }

    /* Bahan container scrollable jika terlalu banyak */
    #bahan-container {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 15px;
      background-color: #f8f9fa;
    }

    /* Calculation box fixed di bawah */
    .highlight-calculation {
      position: relative;
      z-index: 1;
      margin-top: 20px;
    }

    /* Modal Form dengan ukuran lebih tinggi */
    .modal-food-cost-content {
      height: 85vh;
    }

    .modal-food-cost-body {
      max-height: calc(85vh - 120px);
      overflow-y: auto;
    }

    /* Custom scrollbar untuk bahan jika diperlukan */
    .bahan-scrollable {
      max-height: 300px;
      overflow-y: auto;
      padding-right: 15px;
    }

    /* Card header gradient */
    .bg-gradient-primary {
      background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
      color: white;
    }

    /* Card header khusus untuk modal */
    .modal-header {
      position: relative;
      z-index: 2;
    }

    /* Efek bayangan untuk card */
    .card {
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body data-topbar="dark" data-layout="vertical">
  <div id="layout-wrapper">

    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Food Cost Menu Regular</h4>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Menu Management</a></li>
                    <li class="breadcrumb-item active">Food Cost Regular</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="row">
            <div class="col-xl-3 col-md-6">
              <div class="card food-cost-card">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                      <p class="text-truncate font-size-14 mb-2">Total Menu</p>
                      <h4 class="mb-0" id="total-menu">0</h4>
                    </div>
                    <div class="text-success">
                      <i class="ri-restaurant-line font-size-24"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card food-cost-card">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                      <p class="text-truncate font-size-14 mb-2">Rata-rata Food Cost</p>
                      <h4 class="mb-0" id="avg-food-cost">Rp 0</h4>
                    </div>
                    <div class="text-primary">
                      <i class="ri-calculator-line font-size-24"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card food-cost-card">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                      <p class="text-truncate font-size-14 mb-2">Total Food Cost</p>
                      <h4 class="mb-0" id="total-food-cost">Rp 0</h4>
                    </div>
                    <div class="text-warning">
                      <i class="ri-money-dollar-circle-line font-size-24"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card food-cost-card">
                <div class="card-body">
                  <div class="d-flex"></div>
                  <div class="flex-1 overflow-hidden">
                    <p class="text-truncate font-size-14 mb-2">Efficiency</p>
                    <h4 class="mb-0 text-success">High</h4>
                  </div>
                  <div class="text-info">
                    <i class="ri-line-chart-line font-size-24"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Table -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <h4 class="card-title mb-0">Data Food Cost Menu Regular</h4>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                      <button type="button" class="btn btn-primary" onclick="tambah_menu()">
                        <i class="ri-add-circle-line me-1"></i> Tambah Menu
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                      <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama Menu</th>
                        <th width="25%">Deskripsi</th>
                        <th width="15%" class="text-center">Total Bahan</th>
                        <th width="15%" class="text-center">Biaya Produksi</th>
                        <th width="15%" class="text-center">Food Cost</th>
                        <th width="15%" class="text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody id="food-cost-data">
                      <!-- Data akan dimuat via AJAX -->
                    </tbody>
                  </table>
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
  <div class="modal fade" id="modal-food-cost" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
      <div class="modal-content modal-food-cost-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalLabel">
            <i class="ri-restaurant-line me-2"></i>Form Food Cost Menu Regular
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="form-food-cost">
          <div class="modal-body modal-food-cost-body">
            <input type="hidden" id="stat" name="stat" value="add">
            <input type="hidden" id="menu_id" name="id">

            <!-- Basic Info -->
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                  <i class="ri-information-line me-2"></i>Informasi Menu
                </h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-lg" id="nama_menu" name="nama_menu" placeholder="Masukkan nama menu" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="deskripsi" class="form-label">Deskripsi</label>
                      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi menu"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bahan Section -->
            <div class="card mb-4">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                  <i class="ri-shopping-basket-line me-2"></i>Daftar Bahan
                </h6>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-pilih-bahan">
                    <i class="ri-database-line me-1"></i> Pilih dari Database
                  </button>
                  <button type="button" class="btn btn-success btn-sm" onclick="addBahanRowManual()">
                    <i class="ri-add-line me-1"></i> Input Manual
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light">
                      <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Bahan <span class="text-danger">*</span></th>
                        <th style="width: 10%;">Jumlah <span class="text-danger">*</span></th>
                        <th style="width: 12%;">Satuan</th>
                        <th style="width: 15%;">Harga per Unit <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Total Harga</th>
                        <th style="width: 8%;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="bahan-container">
                      <!-- Bahan rows akan ditambahkan secara dinamis -->
                    </tbody>
                  </table>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">TOTAL KESELURUHAN:</h6>
                    <h5 class="mb-0 text-primary" id="display-total-keseluruhan">Rp 0</h5>
                  </div>
                </div>
              </div>
            </div>

            <!-- Real-time Calculation -->
            <div class="card calculation-card">
              <div class="card-header bg-gradient-primary text-white">
                <h6 class="card-title mb-0 text-white">
                  <i class="ri-calculator-line me-2"></i>Perhitungan Food Cost (Real-time)
                </h6>
              </div>
              <div class="card-body">
                <div class="calculation-summary">
                  <div class="calculation-item">
                    <span>Total Bahan Mentah (1 porsi):</span>
                    <span id="calc-total-bahan" class="fw-bold">Rp 0</span>
                  </div>
                  <div class="calculation-item">
                    <span>Biaya Produksi (20%):</span>
                    <span id="calc-biaya-produksi" class="fw-bold">Rp 0</span>
                  </div>
                  <div class="calculation-item total">
                    <span>FOOD COST:</span>
                    <span id="calc-food-cost" class="fw-bold">Rp 0</span>
                  </div>
                </div>

                <div class="text-center mt-3">
                  <small class="text-muted">
                    <i class="ri-information-line me-1"></i>
                    Formula: (Qty × Harga per Satuan) ÷ Pembagian Porsi + 20% Biaya Produksi
                  </small>
                </div>
              </div>
            </div>

          </div>

          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="ri-close-line me-1"></i> Batal
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="ri-save-line me-1"></i> Simpan Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Pilih Bahan dari Database -->
  <div class="modal fade" id="modal-pilih-bahan" tabindex="-1" aria-labelledby="modalPilihBahanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalPilihBahanLabel">
            <i class="ri-database-line me-2"></i>Pilih Bahan dari Database
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Search Bahan -->
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Cari Bahan:</label>
              <div class="input-group">
                <input type="text" class="form-control" id="search-bahan" placeholder="Ketik nama bahan..." autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" id="btn-search-bahan">
                  <i class="ri-search-line"></i>
                </button>
                <button class="btn btn-outline-info" type="button" id="btn-reload-bahan">
                  <i class="ri-refresh-line"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Daftar Bahan -->
          <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
            <table class="table table-hover table-sm">
              <thead class="table-light sticky-top">
                <tr>
                  <th width="5%">#</th>
                  <th width="40%">Nama Bahan</th>
                  <th width="15%">Satuan</th>
                  <th width="20%">Harga Awal</th>
                  <th width="20%">Harga Sekarang</th>
                </tr>
              </thead>
              <tbody id="daftar-bahan-database">
                <?php if (isset($bahan_list) && !empty($bahan_list)) : ?>
                  <?php foreach ($bahan_list as $index => $bahan) : ?>
                    <tr class="bahan-row-database cursor-pointer" data-id="<?php echo $bahan->id_bahan; ?>" data-nama="<?php echo htmlspecialchars($bahan->nama_bahan); ?>" data-satuan="<?php echo $bahan->nama_satuan; ?>" data-id-satuan="<?php echo $bahan->id_satuan; ?>" data-harga-awal="<?php echo $bahan->harga_awal; ?>" data-harga-sekarang="<?php echo $bahan->harga_sekarang; ?>" data-harga-current="<?php echo $bahan->harga_current; ?>">
                      <td><?php echo $index + 1; ?></td>
                      <td>
                        <strong><?php echo $bahan->nama_bahan; ?></strong>
                        <br>
                        <small class="text-muted">ID: <?php echo $bahan->id_bahan; ?></small>
                      </td>
                      <td>
                        <span class="badge bg-secondary"><?php echo $bahan->nama_satuan; ?></span>
                      </td>
                      <td>
                        <span class="text-muted">Rp <?php echo number_format($bahan->harga_awal, 0, ',', '.'); ?></span>
                      </td>
                      <td>
                        <?php if ($bahan->harga_sekarang > 0) : ?>
                          <strong class="text-success">Rp <?php echo number_format($bahan->harga_sekarang, 0, ',', '.'); ?></strong>
                        <?php else : ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted">
                      <i class="ri-inbox-line me-2"></i>Belum ada data bahan
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <?php $this->load->view('back/food_cost/V_Food_Cost_js'); ?>

</body>

</html>