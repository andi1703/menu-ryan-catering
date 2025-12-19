<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>

  <!-- PASTIKAN BOOTSTRAP 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <!-- Hapus Select2 karena tidak digunakan -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->

  <style>
    .customer-header {
      background-color: #007bff !important;
      color: white;
      font-weight: bold;
      text-align: center;
    }

    .kantin-header {
      background-color: #6c757d !important;
      color: white;
      font-weight: bold;
    }

    /* Styling Label Form */
    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #495057;
      font-size: 14px;
    }

    /* Styling untuk alert data tidak ditemukan */
    .alert-warning {
      border-left: 4px solid #ffc107;
    }

    .alert-warning .alert-link {
      color: #856404;
      font-weight: bold;
      text-decoration: underline;
    }

    .alert-warning .alert-link:hover {
      color: #533f03;
    }

    /* Styling Dropdown Kantin */
    .dropdown-menu {
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu .form-check {
      margin-bottom: 8px;
      padding-left: 1.5rem;
    }

    .dropdown-menu .form-check:last-child {
      margin-bottom: 0;
    }

    .dropdown-menu .form-check-input {
      cursor: pointer;
      margin-top: 0.3rem;
    }

    .dropdown-menu .form-check-label {
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      width: 100%;
    }

    .dropdown-menu .form-check:hover {
      background-color: #f8f9fa;
      border-radius: 4px;
    }

    /* Styling button dropdown - PERBAIKAN WARNA */
    #kantin-dropdown {
      height: 38px;
      padding: 0.375rem 0.75rem;
      font-size: 14px;
      border-color: #ced4da;
      background-color: #ffffff !important;
      color: #495057 !important;
    }

    #kantin-dropdown:hover {
      background-color: #f8f9fa !important;
      border-color: #adb5bd;
    }

    #kantin-dropdown:focus,
    #kantin-dropdown:active {
      background-color: #ffffff !important;
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      color: #495057 !important;
    }

    /* Text dalam button */
    #kantin-selected-count {
      color: #6c757d;
      flex: 1;
      text-align: left;
    }

    #kantin-dropdown.has-selection #kantin-selected-count {
      color: #495057;
      font-weight: 500;
    }

    /* Icon chevron */
    #kantin-dropdown .mdi-chevron-down {
      font-size: 18px;
      color: #6c757d;
      margin-left: auto;
    }

    /* Override Bootstrap default */
    .btn-outline-secondary {
      color: #495057 !important;
      background-color: #ffffff !important;
      border-color: #ced4da !important;
    }

    .btn-outline-secondary:hover {
      color: #495057 !important;
      background-color: #f8f9fa !important;
      border-color: #adb5bd !important;
    }

    .btn-outline-secondary:focus,
    .btn-outline-secondary:active,
    .btn-outline-secondary.active,
    .btn-outline-secondary.show {
      color: #495057 !important;
      background-color: #ffffff !important;
      border-color: #80bdff !important;
    }

    /* Scrollbar custom untuk dropdown */
    .dropdown-menu::-webkit-scrollbar {
      width: 8px;
    }

    .dropdown-menu::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    /* Mencegah dropdown tertutup saat klik checkbox */
    .dropdown-menu {
      cursor: default;
    }

    #selectAllKantin,
    #deselectAllKantin {
      text-decoration: none;
      font-weight: 500;
    }

    #selectAllKantin:hover,
    #deselectAllKantin:hover {
      text-decoration: underline;
    }

    .table th,
    .table td {
      vertical-align: middle;
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

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">Daily Menu Report</h4>
                  <p class="mb-0 text-muted">Laporan menu harian per kantin</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item active">Report Daily Menu</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Form -->
          <div class="card mb-3">
            <div class="card-body">
              <form method="get" class="mb-3">
                <input type="hidden" name="apply_filter" value="1">
                <div class="row g-2">
                  <!-- Filter Tanggal -->
                  <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($this->input->get('tanggal')) ?>">
                  </div>

                  <!-- Filter Customer -->
                  <div class="col-md-3">
                    <label for="customer-select" class="form-label">Customer</label>
                    <select name="id_customer" class="form-control" id="customer-select">
                      <option value="">- Pilih Customer -</option>
                      <?php foreach ($customerList as $c) : ?>
                        <option value="<?= $c['id_customer'] ?>" <?= $this->input->get('id_customer') == $c['id_customer'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($c['nama_customer']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Filter Kantin (Dropdown Checkbox) -->
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="kantin-dropdown" class="form-label">Kantin <span class="text-danger">*</span></label>
                      <div class="dropdown w-100">
                        <!-- GANTI CLASS dari btn-outline-secondary ke btn-kantin-dropdown -->
                        <button class="btn btn-kantin-dropdown dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" id="kantin-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                          <span id="kantin-selected-count">- Pilih Kantin -</span>
                          <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu w-100 p-3" aria-labelledby="kantin-dropdown" style="max-height: 300px; overflow-y: auto;">
                          <!-- Header: Select/Deselect All -->
                          <div class="mb-2 pb-2 border-bottom">
                            <a href="#" id="selectAllKantin" class="text-primary me-2" style="font-size: 12px;">Pilih Semua</a> |
                            <a href="#" id="deselectAllKantin" class="text-danger ms-2" style="font-size: 12px;">Hapus Semua</a>
                          </div>

                          <!-- Checkbox List -->
                          <?php
                          // Ambil semua kantin untuk checkbox
                          $allKantins = $this->db->select('nama_kantin, id_kantin')
                            ->from('kantin')
                            ->order_by('nama_kantin', 'ASC')
                            ->get()
                            ->result_array();
                          $selectedKantins = isset($filter['id_kantin']) && is_array($filter['id_kantin']) ? $filter['id_kantin'] : [];
                          $selectedKantinsInt = array_map('intval', $selectedKantins);

                          if (count($allKantins) > 0) :
                            foreach ($allKantins as $k) :
                              $isChecked = in_array((int) $k['id_kantin'], $selectedKantinsInt, true) ? 'checked' : '';
                          ?>
                              <div class="form-check">
                                <input class="form-check-input kantin-checkbox" type="checkbox" name="id_kantin[]" value="<?= (int) $k['id_kantin'] ?>" id="kantin_<?= $k['id_kantin'] ?>" <?= $isChecked ?>>
                                <label class="form-check-label" for="kantin_<?= $k['id_kantin'] ?>">
                                  <?= htmlspecialchars($k['nama_kantin']) ?>
                                </label>
                              </div>
                            <?php
                            endforeach;
                          else :
                            ?>
                            <small class="text-muted">Tidak ada kantin tersedia</small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Filter Shift -->
                  <div class="col-md-3">
                    <label for="shift-select" class="form-label">Shift</label>
                    <select name="shift" id="shift-select" class="form-control">
                      <option value="">- Pilih Shift -</option>
                      <option value="lunch" <?= $this->input->get('shift') == 'lunch' ? 'selected' : '' ?>>Lunch</option>
                      <option value="dinner" <?= $this->input->get('shift') == 'dinner' ? 'selected' : '' ?>>Dinner</option>
                      <option value="supper" <?= $this->input->get('shift') == 'supper' ? 'selected' : '' ?>>Supper</option>
                    </select>
                  </div>

                  <!-- Tombol Aksi -->
                  <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                      <i class="mdi mdi-filter-outline me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('menu-harian-report') ?>" class="btn btn-secondary">
                      <i class="mdi mdi-refresh me-1"></i> Reset
                    </a>
                    <a href="<?= base_url('menu-harian-report/generate_pdf?' . http_build_query($_GET)) ?>" class="btn btn-danger" target="_blank">
                      <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                    <a href="<?= base_url('menu-harian-report/generate_excel?' . http_build_query($_GET)) ?>" class="btn btn-success" target="_blank">
                      <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Main Content - Accordion by Customer -->
          <div class="row">
            <div class="col-12">
              <?php if (!empty($hasFilter)) : ?>
                <?php if (count($groupedByCustomer) > 0) : ?>
                  <div class="card">
                    <div class="card-header">
                      <div class="row">
                        <div class="col-lg-6">
                          <h6 class="mb-0">
                            Report:
                            <?php if ($this->input->get('tanggal')) : ?>
                              <?= date('d M Y', strtotime($this->input->get('tanggal'))) ?>
                            <?php else : ?>
                              Semua Tanggal
                            <?php endif; ?>
                            <?php if ($this->input->get('shift')) : ?>
                              - Shift: <?= strtoupper($this->input->get('shift')) ?>
                            <?php endif; ?>
                            <?php if (isset($grandTotalOrderAll) && $grandTotalOrderAll > 0) : ?>
                              <span class="badge bg-warning text-dark ms-2" style="font-size: 13px;">
                                Total Order Keseluruhan: <?= number_format($grandTotalOrderAll, 0, ',', '.') ?>
                              </span>
                            <?php endif; ?>
                          </h6>
                        </div>
                        <div class="col-lg-6">
                          <div class="custom-control custom-switch float-end">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1">
                            <label class="custom-control-label" for="customSwitch1">Show Collapsed</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="accordion" id="accordionCustomer">
                        <?php
                        $no = 1;
                        foreach ($groupedByCustomer as $customerId => $customerData) :
                        ?>
                          <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="heading<?= $customerId ?>">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $customerId ?>" aria-expanded="false" aria-controls="collapse<?= $customerId ?>">
                                <strong><?= $no++ ?>. <?= htmlspecialchars($customerData['customer_name']) ?></strong>
                                <span class="badge bg-primary ms-2"><?= count($customerData['kantins']) ?> Kantin</span>
                                <?php
                                $totalOrder = isset($customerData['grand_total_order']) ? (int)$customerData['grand_total_order'] : 0;
                                ?>
                                <span class="badge bg-warning text-dark ms-2">Total Order: <?= $totalOrder ?></span>
                              </button>
                            </h2>
                            <div id="collapse<?= $customerId ?>" class="accordion-collapse collapse xcollapse" aria-labelledby="heading<?= $customerId ?>" data-bs-parent="#accordionCustomer">
                              <div class="accordion-body">
                                <div class="table-responsive">
                                  <table class="table table-bordered table-sm table-hover">
                                    <thead class="table-primary">
                                      <tr>
                                        <th class="text-center" style="width:40px;">No</th>
                                        <th>Nama Menu</th>
                                        <th>Jenis Menu</th>
                                        <th class="text-center" style="width:80px;">Shift</th>
                                        <th>Kondimen</th>
                                        <th>Kategori</th>
                                        <?php foreach ($customerData['kantins'] as $kantin) : ?>
                                          <th class="text-center"><?= htmlspecialchars($kantin) ?></th>
                                        <?php endforeach; ?>
                                        <th class="text-center" style="width:60px;">Total</th>
                                        <th class="text-center" style="background:#ffc; width:80px;">Total Order</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $menuNo = 1;
                                      foreach ($customerData['menu_data'] as $menu) :
                                        $kondimenList = isset($menu['kondimen_list']) && is_array($menu['kondimen_list']) ? $menu['kondimen_list'] : [];
                                        $kondimen_count = count($kondimenList);
                                        if ($kondimen_count === 0) {
                                          $kondimenList[] = [
                                            'nama_kondimen' => '-',
                                            'kategori' => '-',
                                            'qty_per_kantin' => []
                                          ];
                                          $kondimen_count = 1;
                                        }

                                        $first = true;
                                        $total_order = isset($menu['total_order_customer']) ? intval($menu['total_order_customer']) : 0;

                                        foreach ($kondimenList as $kIndex => $kondimen) :
                                          $counterLabel = ($kIndex + 1) . '.';
                                          $qtyPerKantin = isset($kondimen['qty_per_kantin']) && is_array($kondimen['qty_per_kantin']) ? $kondimen['qty_per_kantin'] : [];
                                      ?>
                                          <tr>
                                            <?php if ($first) : ?>
                                              <td class="text-center align-middle" rowspan="<?= $kondimen_count ?>"><?= $menuNo++ ?></td>
                                              <td class="align-middle" rowspan="<?= $kondimen_count ?>"><?= htmlspecialchars($menu['nama_menu'] ?? '-') ?></td>
                                              <td class="align-middle" rowspan="<?= $kondimen_count ?>"><?= htmlspecialchars($menu['jenis_menu'] ?? '-') ?></td>
                                              <td class="text-center align-middle" rowspan="<?= $kondimen_count ?>">
                                                <?= !empty($menu['shift']) ? strtoupper(htmlspecialchars($menu['shift'])) : '-' ?>
                                              </td>
                                            <?php endif; ?>
                                            <td>
                                              <span class="text-muted fw-semibold me-1"><?= $counterLabel ?></span>
                                              <span><?= htmlspecialchars($kondimen['nama_kondimen'] ?? '-') ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($kondimen['kategori'] ?? '-') ?></td>
                                            <?php foreach ($customerData['kantins'] as $kantin) :
                                              $qtySafe = isset($qtyPerKantin[$kantin]) ? intval($qtyPerKantin[$kantin]) : 0;
                                            ?>
                                              <td class="text-center"><?= $qtySafe ?></td>
                                            <?php endforeach; ?>
                                            <td class="text-center"><strong><?= array_sum(array_map('intval', $qtyPerKantin)) ?></strong></td>
                                            <?php if ($first) : ?>
                                              <td rowspan="<?= $kondimen_count ?>" class="text-center bg-warning">
                                                <strong><?= $total_order ?></strong>
                                              </td>
                                            <?php endif; ?>
                                          </tr>
                                          <?php $first = false; ?>
                                      <?php endforeach;
                                      endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-dark">
                                      <tr>
                                        <th colspan="<?= 7 + count($customerData['kantins']) ?>" class="text-end">Total</th>
                                        <?php
                                        // ✅ Hitung grand total dari total_order_customer
                                        $grandOrder = 0;
                                        foreach ($customerData['menu_data'] as $menu) {
                                          $grandOrder += intval($menu['total_order_customer']);
                                        }
                                        echo "<th class='text-center' style='background:#ffc; color:#222; font-weight:bold;'>$grandOrder</th>";
                                        ?>
                                      </tr>
                                    </tfoot>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                <?php else : ?>
                  <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-outline me-2"></i>
                    <strong>Data Tidak Ditemukan!</strong>
                    <br>
                    Tidak ada data menu harian yang sesuai dengan filter.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>
              <?php else : ?>
                <div class="alert alert-info" role="alert">
                  <i class="mdi mdi-information-outline me-2"></i>
                  Silakan pilih filter terlebih dahulu untuk menampilkan laporan.
                </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- JS Bootstrap & DataTables -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>

  <!-- PASTIKAN BOOTSTRAP 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

  <!-- Hapus Select2 karena tidak digunakan -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
  <?php $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report_js'); ?>
</body>

</html>