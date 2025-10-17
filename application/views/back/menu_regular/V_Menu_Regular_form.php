<?php
?>
<style>
  /* Styling untuk tabel komponen dengan pagination */
  #table-komponen-menu {
    width: 100% !important;
  }

  .komponen-pagination {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    min-height: 45px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 10px 15px;
    margin: 0;
  }

  .komponen-pagination .pagination {
    display: flex !important;
    list-style: none !important;
    border-radius: 0.25rem !important;
    margin: 0 !important;
  }

  .komponen-pagination .page-item {
    display: list-item !important;
  }

  .komponen-pagination .page-link {
    display: block !important;
    padding: 0.25rem 0.5rem !important;
    margin-left: -1px !important;
    line-height: 1.25 !important;
    color: #007bff !important;
    text-decoration: none !important;
    background-color: #fff !important;
    border: 1px solid #dee2e6 !important;
    font-size: 0.875rem;
  }

  .komponen-pagination .page-link:hover {
    z-index: 2 !important;
    color: #0056b3 !important;
    text-decoration: none !important;
    background-color: #e9ecef !important;
    border-color: #dee2e6 !important;
  }

  .komponen-pagination .page-item.active .page-link {
    z-index: 1 !important;
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
  }

  .komponen-pagination .page-item.disabled .page-link {
    color: #6c757d !important;
    pointer-events: none !important;
    cursor: auto !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
  }

  /* Styling untuk tabel keranjang */
  #table-keranjang-menu {
    margin-top: 0;
    border: none;
  }

  #table-keranjang-menu th,
  #table-keranjang-menu td {
    padding: 0.5rem 0.75rem;
    vertical-align: middle;
  }

  #table-keranjang-menu thead th {
    border-top: none;
  }

  .btn-hapus-item {
    padding: 0.2rem 0.4rem;
    line-height: 1;
    border-radius: 3px;
  }

  /* Badge styling untuk kategori */
  .badge-kategori {
    padding: 0.3em 0.6em;
    font-size: 85%;
    font-weight: normal;
    border-radius: 3px;
    white-space: nowrap;
  }

  /* Animasi untuk menambah komponen */
  @keyframes highlight-row {
    from {
      background-color: #fffbcc;
    }

    to {
      background-color: transparent;
    }
  }

  .highlight-new {
    animation: highlight-row 1.5s;
  }

  /* Memperbaiki empty state */
  #keranjang-empty td {
    height: 120px;
  }

  /* Perbaikan spacing tabel keranjang */
  .card-body {
    padding: 1rem !important;
  }

  #table-keranjang-menu {
    margin-bottom: 0 !important;
  }

  /* Perbaiki border tabel agar tidak dobel dengan card */
  .table-bordered {
    border: 1px solid #dee2e6;
  }

  /* Perbaiki padding sel tabel */
  #table-keranjang-menu th,
  #table-keranjang-menu td {
    padding: 0.5rem 0.75rem;
  }

  /* Tambahkan border-radius pada tabel agar lebih rapi */
  #table-keranjang-menu {
    border-radius: 0.25rem;
    overflow: hidden;
  }

  /* Buat footer tabel lebih menonjol */
  #table-keranjang-menu tfoot tr {
    background-color: #f8f9fa;
    font-weight: bold;
  }

  /* Perbaiki tampilan badge kategori */
  .badge-kategori {
    display: inline-block;
    padding: 0.25em 0.6em;
    font-size: 80%;
    font-weight: normal;
    border-radius: 0.25rem;
  }

  /* Perbaiki ukuran & spacing card header */
  .card-header {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0, 0, 0, .03);
  }

  /* TAMBAHAN: Styling untuk pagination keranjang */
  .keranjang-pagination {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    min-height: 45px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 10px 15px;
    margin: 0;
  }

  .keranjang-pagination .pagination {
    display: flex !important;
    list-style: none !important;
    border-radius: 0.25rem !important;
    margin: 0 !important;
  }

  .keranjang-pagination .page-item {
    display: list-item !important;
  }

  .keranjang-pagination .page-link {
    display: block !important;
    padding: 0.25rem 0.5rem !important;
    margin-left: -1px !important;
    line-height: 1.25 !important;
    color: #007bff !important;
    text-decoration: none !important;
    background-color: #fff !important;
    border: 1px solid #dee2e6 !important;
    font-size: 0.875rem;
  }

  .keranjang-pagination .page-link:hover {
    z-index: 2 !important;
    color: #0056b3 !important;
    text-decoration: none !important;
    background-color: #e9ecef !important;
    border-color: #dee2e6 !important;
  }

  .keranjang-pagination .page-item.active .page-link {
    z-index: 1 !important;
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
  }

  .keranjang-pagination .page-item.disabled .page-link {
    color: #6c757d !important;
    pointer-events: none !important;
    cursor: auto !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
  }

  /* TAMBAHAN: Hide rows untuk pagination */
  .komponen-row {
    display: table-row;
  }

  .komponen-row.hidden {
    display: none !important;
  }
</style>

<div class="modal fade" id="modal-regular-menu" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modalMenuRegularLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form id="form-regular-menu" method="post">
        <input type="hidden" name="stat" value="add">
        <input type="hidden" name="id" id="id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMenuRegularLabel">Tambah Menu Regular</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="-1">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nama_menu_reg">Nama Menu Regular <span class="text-danger">*</span></label>
            <input type="text" required name="nama_menu_reg" id="nama_menu_reg" class="form-control" placeholder="Contoh: Paket Nasi Ayam">
          </div>
          <div class="form-group">
            <label for="harga">Harga <span class="text-danger">*</span></label>
            <input type="number" required name="harga" id="harga" class="form-control" placeholder="Harga menu regular" readonly>
          </div>
          <div class="form-group">
            <label>Komponen Menu <span class="text-danger">*</span></label>
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="ri-search-line"></i></span>
              </div>
              <input type="text" id="search-komponen" class="form-control" placeholder="Cari komponen menu...">
            </div>

            <!-- Table komponen dengan pagination -->
            <div class="border rounded">
              <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="table-komponen-menu">
                  <thead>
                    <tr>
                      <th style="width: 50px">Pilih</th>
                      <th>Nama Komponen</th>
                      <th>Kategori</th>
                      <th>Harga</th>
                    </tr>
                  </thead>
                  <tbody id="komponen-menu-body">
                    <?php if (!empty($menu_list)) : ?>
                      <?php foreach ($menu_list as $m) : ?>
                        <tr class="komponen-row" data-komponen-id="<?= $m->id_komponen ?>">
                          <td class="text-center">
                            <input type="checkbox" class="komponen-checkbox" name="komponen_menu[]" value="<?= $m->id_komponen ?>" data-harga="<?= $m->menu_harga ?>">
                          </td>
                          <td><?= htmlspecialchars($m->menu_nama) ?></td>
                          <td><?= htmlspecialchars($m->kategori_nama) ?></td>
                          <td>Rp <?= number_format($m->menu_harga, 0, ',', '.') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Tidak ada data komponen menu</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination untuk table komponen - PERBAIKAN: Selalu ditampilkan -->
              <div id="komponen-pagination-container" class="komponen-pagination" style="display: block;">
                <div class="row align-items-center">
                  <div class="col-sm-6">
                    <div class="dataTables_info text-muted" style="font-size: 0.875rem;">
                      Menampilkan <strong id="komponen-start">1</strong> sampai <strong id="komponen-end">10</strong> dari <strong id="komponen-total"><?= !empty($menu_list) ? count($menu_list) : 0 ?></strong> komponen
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <nav aria-label="Komponen Pagination">
                      <ul class="pagination pagination-sm justify-content-end mb-0" id="komponen-pagination-list">
                        <!-- Pagination buttons akan di-generate oleh JavaScript -->
                        <li class="page-item disabled">
                          <span class="page-link"><i class="mdi mdi-chevron-left"></i></span>
                        </li>
                        <li class="page-item active">
                          <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                          <a class="page-link komponen-page-btn" href="#" data-page="2">2</a>
                        </li>
                        <li class="page-item">
                          <a class="page-link komponen-page-btn" href="#" data-page="2">
                            <i class="mdi mdi-chevron-right"></i>
                          </a>
                        </li>
                      </ul>
                    </nav>
                  </div>
                </div>
              </div>
            </div>

            <small class="form-text text-muted">Centang satu atau lebih komponen menu.</small>
          </div>

          <!-- Tabel Komponen Menu Terpilih (Keranjang) dengan Pagination -->
          <div class="card mt-4 border">
            <div class="card-header bg-light">
              <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                  <i class="ri-shopping-cart-2-line mr-1"></i> Komponen Menu Terpilih
                </h6>
                <span class="badge badge-primary" id="jumlah-komponen">0</span>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="table-keranjang-menu">
                  <thead class="thead-light">
                    <tr>
                      <th width="40px" class="text-center">No</th>
                      <th>Nama Komponen</th>
                      <th width="100px">Kategori</th>
                      <th width="120px" class="text-right">Harga</th>
                      <th width="50px" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="keranjang-menu-body">
                    <tr id="keranjang-empty">
                      <td colspan="5" class="text-center text-muted py-4">
                        <div>
                          <i class="ri-shopping-cart-line fa-2x mb-2"></i>
                          <p class="mb-0">Belum ada komponen menu dipilih</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="bg-light font-weight-bold">
                      <td colspan="3" class="text-right">Total Harga:</td>
                      <td class="text-right" id="total-harga-display">Rp 0</td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <!-- TAMBAHAN: Pagination untuk keranjang -->
              <div id="keranjang-pagination-container" class="keranjang-pagination" style="display: none;">
                <div class="row align-items-center">
                  <div class="col-sm-6">
                    <div class="dataTables_info text-muted" style="font-size: 0.875rem;">
                      Menampilkan <strong id="keranjang-start">1</strong> sampai <strong id="keranjang-end">5</strong> dari <strong id="keranjang-total">0</strong> komponen
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <nav aria-label="Keranjang Pagination">
                      <ul class="pagination pagination-sm justify-content-end mb-0" id="keranjang-pagination-list">
                        <!-- Pagination will be generated by JavaScript -->
                      </ul>
                    </nav>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Hidden container untuk menyimpan komponen terpilih -->
          <div id="selected-components-container" style="display:none;"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" tabindex="-1">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>