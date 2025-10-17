<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    /* Custom style agar select2 lebih besar */
    .select2-container--default .select2-selection--multiple {
      min-height: 48px;
      font-size: 1rem;
      padding: 8px;
      border-radius: 6px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
      font-size: 1rem;
      padding: 4px 8px;
      margin-top: 4px;
    }

    .table thead th {
      background-color: #f8f9fa;
      color: #212529;
      font-weight: 600;
    }

    /* Pagination styling - hanya tambahan ini */
    .pagination-container {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      min-height: 50px;
      background: #f8f9fa;
      border-top: 1px solid #dee2e6;
      padding: 15px;
      margin-top: 0;
    }

    .pagination {
      display: flex !important;
      list-style: none !important;
      border-radius: 0.25rem !important;
      margin: 0 !important;
    }

    .page-item {
      display: list-item !important;
    }

    .page-link {
      display: block !important;
      padding: 0.375rem 0.75rem !important;
      margin-left: -1px !important;
      line-height: 1.25 !important;
      color: #007bff !important;
      text-decoration: none !important;
      background-color: #fff !important;
      border: 1px solid #dee2e6 !important;
    }

    .page-link:hover {
      z-index: 2 !important;
      color: #0056b3 !important;
      text-decoration: none !important;
      background-color: #e9ecef !important;
      border-color: #dee2e6 !important;
    }

    .page-item.active .page-link {
      z-index: 1 !important;
      color: #fff !important;
      background-color: #007bff !important;
      border-color: #007bff !important;
    }

    .page-item.disabled .page-link {
      color: #6c757d !important;
      pointer-events: none !important;
      cursor: auto !important;
      background-color: #fff !important;
      border-color: #dee2e6 !important;
    }

    .badge {
      font-size: 10px;
      padding: 3px 6px;
      margin-left: 5px;
      border-radius: 3px;
      font-weight: 500;
    }

    .badge-primary {
      background-color: #007bff !important;
      color: white !important;
    }

    .badge-success {
      background-color: #28a745 !important;
      color: white !important;
    }

    .badge-info {
      background-color: #17a2b8 !important;
      color: white !important;
    }

    .badge-warning {
      background-color: #ffc107 !important;
      color: #212529 !important;
    }

    .badge-secondary {
      background-color: #6c757d !important;
      color: white !important;
    }

    .badge-danger {
      background-color: #dc3545 !important;
      color: white !important;
    }

    .badge-dark {
      background-color: #343a40 !important;
      color: white !important;
    }

    .badge-light {
      background-color: #f8f9fa !important;
      color: #212529 !important;
      border: 1px solid #dee2e6;
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
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Master Menu Regular</h4>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Viona</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                    <li class="breadcrumb-item active">Menu Regular</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <h4 class="card-title">Daftar Menu Regular</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary waves-effect waves-light" onclick="tambah_menu_regular()">
                        <i class="mdi mdi-plus me-2"></i>Tambah Menu Regular
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Menu Regular</th>
                          <th>Komponen Menu</th>
                          <th>Harga</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_menu_regular">
                        <?php
                        // TAMBAHAN PAGINATION LOGIC
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $itemsPerPage = 10;
                        $offset = ($page - 1) * $itemsPerPage;
                        $totalItems = isset($show_data) ? count($show_data) : 0;
                        $totalPages = ceil($totalItems / $itemsPerPage);
                        $currentPageData = isset($show_data) ? array_slice($show_data, $offset, $itemsPerPage) : [];

                        // GUNAKAN DATA YANG SUDAH DI-SLICE
                        if (!empty($currentPageData)) :
                          $no = $offset + 1; // Mulai dari nomor yang sesuai
                          foreach ($currentPageData as $m) :
                        ?>
                            <tr>
                              <td><?= $no++ ?></td>
                              <td><?= htmlspecialchars($m->nama_menu_reg) ?></td>
                              <td>
                                <ol style="padding-left: 18px; margin-bottom: 0;">
                                  <?php
                                  $badge_map = [
                                    'Lauk Utama' => 'primary',
                                    'Nasiasbjdasbd238974' => 'success',
                                    'Sambal' => 'danger',
                                    'Pendamping Basah' => 'info',
                                    'Pendamping Kering' => 'warning',
                                    'Sayuran Berkuah' => 'secondary',
                                    'Buah' => 'dark',
                                    'Tumisan' => 'light'
                                  ];
                                  if (!empty($m->komponen)) {
                                    foreach ($m->komponen as $k) {
                                      $badge = isset($badge_map[$k->kategori_nama]) ? $badge_map[$k->kategori_nama] : 'secondary';
                                      echo '<li>' . htmlspecialchars($k->menu_nama) . ' <span class="badge badge-' . $badge . '">' . htmlspecialchars($k->kategori_nama) . '</span></li>';
                                    }
                                  }
                                  ?>
                                </ol>
                              </td>
                              <td>Rp <?= number_format($m->harga, 0, ',', '.') ?></td>
                              <td>
                                <button class="btn btn-warning btn-sm btn-edit" data-id="<?= $m->id ?>" type="button">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $m->id ?>" type="button">
                                  <i class="fas fa-trash"></i> Hapus
                                </button>
                              </td>
                            </tr>
                          <?php
                          endforeach;
                        else :
                          ?>
                          <tr>
                            <td colspan="5" class="text-center">Tidak ada data menu regular</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>

                  <!-- TAMBAHAN PAGINATION - Hanya ini yang ditambah -->
                  <?php if ($totalItems > $itemsPerPage) : ?>
                    <div class="pagination-container">
                      <div class="row align-items-center">
                        <div class="col-sm-6">
                          <div class="dataTables_info text-muted">
                            Menampilkan <strong><?= min($offset + 1, $totalItems) ?></strong> sampai <strong><?= min($offset + $itemsPerPage, $totalItems) ?></strong> dari <strong><?= $totalItems ?></strong> data
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <nav aria-label="Menu Regular Pagination">
                            <ul class="pagination pagination-sm justify-content-end mb-0">

                              <!-- Previous Button -->
                              <?php if ($page > 1) : ?>
                                <li class="page-item">
                                  <a class="page-link" href="?page=<?= $page - 1 ?>" title="Halaman Sebelumnya">
                                    <i class="ri-arrow-left-line"></i>
                                  </a>
                                </li>
                              <?php else : ?>
                                <li class="page-item disabled">
                                  <span class="page-link">
                                    <i class="ri-arrow-left-line"></i>
                                  </span>
                                </li>
                              <?php endif; ?>

                              <!-- Page Numbers -->
                              <?php
                              $startPage = max(1, $page - 2);
                              $endPage = min($totalPages, $page + 2);

                              // First page
                              if ($startPage > 1) : ?>
                                <li class="page-item">
                                  <a class="page-link" href="?page=1">1</a>
                                </li>
                                <?php if ($startPage > 2) : ?>
                                  <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                  </li>
                                <?php endif;
                              endif;

                              // Current range
                              for ($i = $startPage; $i <= $endPage; $i++) :
                                if ($i == $page) : ?>
                                  <li class="page-item active">
                                    <span class="page-link"><?= $i ?></span>
                                  </li>
                                <?php else : ?>
                                  <li class="page-item">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                  </li>
                                <?php endif;
                              endfor;

                              // Last page
                              if ($endPage < $totalPages) :
                                if ($endPage < $totalPages - 1) : ?>
                                  <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                  </li>
                                <?php endif; ?>
                                <li class="page-item">
                                  <a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                                </li>
                              <?php endif; ?>

                              <!-- Next Button -->
                              <?php if ($page < $totalPages) : ?>
                                <li class="page-item">
                                  <a class="page-link" href="?page=<?= $page + 1 ?>" title="Halaman Selanjutnya">
                                    <i class="ri-arrow-right-line"></i>
                                  </a>
                                </li>
                              <?php else : ?>
                                <li class="page-item disabled">
                                  <span class="page-link">
                                    <i class="ri-arrow-right-line"></i>
                                  </span>
                                </li>
                              <?php endif; ?>
                            </ul>
                          </nav>
                        </div>
                      </div>
                    </div>
                  <?php else : ?>
                    <div id="manual-pagination-container">
                      <div class="text-center py-2 text-muted">
                        Semua data ditampilkan (<?= $totalItems ?> item)
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            </div>
          </div>

          <!-- Modal Form Menu Regular -->
          <?php $this->load->view('back/menu_regular/V_Menu_Regular_form', ['menu_list' => $menu_list]); ?>
        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Load JavaScript terakhir -->
  <?php $this->load->view('back/menu_regular/V_Menu_Regular_js'); ?>
</body>

</html>