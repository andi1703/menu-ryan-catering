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

  <!-- Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

  <!-- CUSTOM CSS -->
  <style>
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 0.375rem;
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

    .badge.bg-success {
      background-color: #28a745 !important;
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
      background-color: #343a40;
      color: white;
      font-weight: 600;
      border: 1px solid #23282d;
      font-size: 0.85rem;
    }

    .table tbody td {
      vertical-align: middle;
      font-size: 0.85rem;
    }

    .info-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 10px;
    }

    .info-card .info-item {
      padding: 10px 15px;
      border-right: 1px solid rgba(255, 255, 255, 0.2);
    }

    .info-card .info-item:last-child {
      border-right: none;
    }

    .info-card .info-label {
      font-size: 0.75rem;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .info-card .info-value {
      font-size: 1.1rem;
      font-weight: 700;
      margin-top: 5px;
    }

    .shift-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      font-weight: 700;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .shift-lunch {
      background-color: #20c997;
      color: white;
    }

    .shift-dinner {
      background-color: #6f42c1;
      color: white;
    }

    .shift-supper {
      background-color: #e83e8c;
      color: white;
    }

    .nested-table {
      background-color: #f8f9fa;
      border-radius: 6px;
      overflow: hidden;
    }

    .nested-table thead th {
      background-color: #e9ecef !important;
      color: #495057 !important;
      font-size: 0.8rem;
      font-weight: 600;
      border: 1px solid #dee2e6;
    }

    .nested-table tbody td {
      background-color: white;
      border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
      .table {
        font-size: 0.8rem;
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
                  <h4 class="mb-0 font-size-18">DETAIL PENGHITUNGAN SAYUR</h4>
                  <p class="mb-0 text-muted">Sesi ID: <?php echo (int)($session_id ?? 0); ?></p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo base_url('vegetable-calculator'); ?>">Vegetable Calculator</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Card -->
          <div class="row mb-3">
            <div class="col-12">
              <div class="card info-card">
                <div class="card-body py-3">
                  <div class="row" id="session_header">
                    <div class="col-md-12 text-center">
                      <i class="ri-loader-4-line ri-spin"></i> Memuat data sesi...
                    </div>
                  </div>
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
                      <h4 class="card-title mb-0">Detail Menu & Bahan</h4>
                    </div>
                    <div class="col-auto">
                      <a href="<?php echo base_url('vegetable-calculator'); ?>" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                      </a>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-center" width="5%">#</th>
                          <th width="25%">Menu Kondimen</th>
                          <th class="text-center" width="10%">Qty Kondimen</th>
                          <th>Detail Bahan</th>
                        </tr>
                      </thead>
                      <tbody id="detail_body">
                        <tr>
                          <td colspan="4" class="text-center py-4">
                            <i class="ri-loader-4-line ri-spin ri-2x"></i>
                            <p class="mb-0 mt-2 text-muted">Memuat data...</p>
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
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <script>
    window.BASE_URL = '<?php echo base_url(); ?>';
    const SESSION_ID = <?php echo (int)($session_id ?? 0); ?>;
  </script>
  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <script>
    $(function() {
      const BASE_URL = window.BASE_URL || '<?php echo base_url(); ?>';

      function number(n) {
        return Intl.NumberFormat().format(n || 0);
      }

      $.getJSON(`${BASE_URL}vegetable-calculator/session-detail`, {
        id: SESSION_ID
      }).done(res => {
        console.log('Session Detail Response:', res);
        const data = (res && res.data) ? res.data : null;
        if (!data) {
          $('#session_header').html(`
            <div class="col-md-12 text-center">
              <i class="ri-alert-line ri-2x"></i>
              <p class="mb-0 mt-2">Data sesi tidak ditemukan</p>
            </div>
          `);
          return;
        }

        const s = data.session || {};
        console.log('Session Data:', s);
        console.log('menu_harian_id:', s.menu_harian_id);
        console.log('nama_menu:', s.nama_menu);
        const shiftLower = (s.shift || '').toLowerCase();
        const shiftClass = `shift-${shiftLower}`;

        $('#session_header').html(`
          <div class="col-md-2 info-item">
            <div class="info-label">Tanggal</div>
            <div class="info-value"><i class="ri-calendar-line me-1"></i>${s.tanggal || '-'}</div>
          </div>
          <div class="col-md-2 info-item">
            <div class="info-label">Shift</div>
            <div class="info-value"><span class="shift-badge ${shiftClass}">${s.shift || '-'}</span></div>
          </div>
          <div class="col-md-1 info-item">
            <div class="info-label">ID Menu</div>
            <div class="info-value">${s.menu_harian_id || '-'}</div>
          </div>
          <div class="col-md-2 info-item">
            <div class="info-label">Customer</div>
            <div class="info-value">${s.nama_customer || s.customer_id || '-'}</div>
          </div>
          <div class="col-md-2 info-item">
            <div class="info-label">Nama Menu</div>
            <div class="info-value" style="font-size: 0.9rem;">${s.nama_menu || '-'}</div>
          </div>
          <div class="col-md-1 info-item text-center">
            <div class="info-label">Jml Menu</div>
            <div class="info-value">${number(s.total_menu || 0)}</div>
          </div>
          <div class="col-md-2 info-item text-center">
            <div class="info-label">Jml Bahan</div>
            <div class="info-value">${number(s.total_bahan || 0)}</div>
          </div>
        `);

        const $body = $('#detail_body');
        $body.empty();

        if (!data.items || data.items.length === 0) {
          $body.append(`
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                <i class="ri-file-list-line ri-2x"></i>
                <p class="mb-0 mt-2">Tidak ada data menu</p>
              </td>
            </tr>
          `);
          return;
        }

        (data.items || []).forEach((it, idx) => {
          const bahanRows = (it.bahan || []).map(b => `
            <tr>
              <td>${(b.bahan_nama || '-').toString()}</td>
              <td class="text-end">${number(b.qty || 0)}</td>
              <td class="text-center">${(b.satuan || '-').toString()}</td>
            </tr>
          `).join('');

          const nested = `
            <div class="nested-table">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr>
                    <th width="50%">Bahan Utama</th>
                    <th class="text-end" width="25%">Jumlah Qty</th>
                    <th class="text-center" width="25%">Satuan</th>
                  </tr>
                </thead>
                <tbody>${bahanRows || '<tr><td colspan="3" class="text-center text-muted py-2">Tidak ada bahan</td></tr>'}</tbody>
              </table>
            </div>
          `;

          const tr = `
            <tr>
              <td class="text-center"><strong>${idx + 1}</strong></td>
              <td><strong>${it.nama_kondimen || '-'}</strong></td>
              <td class="text-center"><span class="badge bg-info">${number(it.qty_kondimen || 0)}</span></td>
              <td>${nested}</td>
            </tr>
          `;
          $body.append(tr);
        });
      }).fail((xhr, status, error) => {
        $('#session_header').html(`
          <div class="col-md-12 text-center">
            <i class="ri-error-warning-line ri-2x text-danger"></i>
            <p class="mb-0 mt-2">Gagal memuat data: ${error}</p>
          </div>
        `);
        $('#detail_body').html(`
          <tr>
            <td colspan="4" class="text-center py-4 text-danger">
              <i class="ri-alert-line ri-2x"></i>
              <p class="mb-0 mt-2">Gagal memuat data detail</p>
            </td>
          </tr>
        `);
        console.error('Failed to load session detail:', {
          xhr,
          status,
          error
        });
      });
    });
  </script>
</body>

</html>