<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="form-modal-menu-harian" tabindex="-1" aria-labelledby="modalMenuHarianLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="form-menu-harian" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMenuHarianLabel">
            <i class="fas fa-utensils me-2"></i>Tambah/Edit Menu Harian
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="id_menu_harian" name="id_menu_harian" />

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" id="tanggal" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="shift">Shift <span class="text-danger">*</span></label>
                <select class="form-control" name="shift" id="shift" required>
                  <option value="">-- Pilih Shift --</option>
                  <option value="lunch">Lunch</option>
                  <option value="dinner">Dinner</option>
                  <option value="supper">Supper</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="jenis_menu">Jenis Menu <span class="text-danger">*</span></label>
                <select class="form-control" name="jenis_menu" id="jenis_menu" required>
                  <option value="">-- Pilih Jenis Menu --</option>
                  <option value="regular">regular</option>
                  <option value="paket">paket</option>
                  <option value="sehat">sehat</option>
                  <option value="staff">staff</option>
                </select>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="id_customer">Customer <span class="text-danger">*</span></label>
                <select class="form-control" name="id_customer" id="id_customer" required>
                  <option value="">-- Pilih Customer --</option>
                  <!-- Option customer diisi dari JS -->
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="kantin-dropdown">Kantin <span class="text-danger">*</span></label>
                <div class="dropdown w-100">
                  <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" id="kantin-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="height: calc(2.25rem + 2px);">
                    <span id="kantin-selected-count">- Pilih Kantin -</span>
                    <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu w-100 p-3" aria-labelledby="kantin-dropdown" style="max-height: 300px; overflow-y: auto;">
                    <div class="mb-2 pb-2 border-bottom">
                      <a href="#" id="selectAllKantin" class="text-primary me-2" style="font-size: 12px;">Pilih Semua</a> |
                      <a href="#" id="deselectAllKantin" class="text-danger ms-2" style="font-size: 12px;">Hapus Semua</a>
                    </div>
                    <div id="kantin-checkbox-group">
                      <!-- Akan diisi oleh JavaScript -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="nama_menu">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_menu" id="nama_menu" required placeholder="Masukkan nama menu utama">
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group mb-3">
                <label for="remark" class="form-label">Catatan / Remark</label>
                <textarea id="remark" name="remark" class="form-control" placeholder="Catatan menu (opsional)"></textarea>
              </div>
            </div>
          </div>

          <hr class="my-3">

          <!-- ✅ TABLE KONDIMEN -->
          <div class="form-group mb-3">
            <label>Menu Kondimen <span class="text-danger">*</span></label>

            <!-- ✅ WRAPPER UNTUK SCROLL HORIZONTAL -->
            <div class="table-kondimen-wrapper">
              <table class="table table-bordered table-hover mb-0" id="table-kondimen-menu">
                <thead></thead>
                <tbody></tbody>
              </table>
            </div>

            <button type="button" class="btn btn-success btn-sm mt-2" id="btn-tambah-kondimen">
              <i class="fas fa-plus me-1"></i>Tambah Kondimen
            </button>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" id="btn-close-modal-menu-harian" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Close
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .form-label,
  .form-group label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.95rem;
  }

  .text-danger {
    color: #dc3545 !important;
    font-weight: 700;
  }

  .form-control,
  .form-select {
    height: calc(2.25rem + 2px);
    min-height: calc(2.25rem + 2px);
  }

  .table-bordered th,
  .table-bordered td {
    vertical-align: middle;
  }

  .btn-sm {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
  }

  /* ✅ DROPDOWN KANTIN */
  .dropdown-menu {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none;
  }

  .dropdown-menu.show {
    display: block;
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

  #kantin-dropdown {
    background-color: #ffffff !important;
    color: #495057 !important;
    border-color: #ced4da;
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

  #kantin-selected-count {
    color: #6c757d;
    flex: 1;
    text-align: left;
  }

  #kantin-dropdown.has-selection #kantin-selected-count {
    color: #495057;
    font-weight: 500;
  }

  #kantin-dropdown .mdi-chevron-down {
    font-size: 18px;
    color: #6c757d;
    margin-left: auto;
  }

  /* ✅ TABLE KONDIMEN */
  #table-kondimen-menu {
    font-size: 0.9rem;
  }

  #table-kondimen-menu thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    border: 1px solid #dee2e6;
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
  }

  #table-kondimen-menu tbody td {
    padding: 0.5rem;
    vertical-align: middle;
  }

  #table-kondimen-menu .kondimen-nama,
  #table-kondimen-menu .kondimen-kategori,
  #table-kondimen-menu .kondimen-qty {
    font-size: 0.875rem;
    height: calc(1.5em + 0.75rem + 2px);
  }

  #table-kondimen-menu .kondimen-qty {
    text-align: center;
    font-weight: 500;
  }

  #table-kondimen-menu .kondimen-qty:focus {
    border-color: #4dabf7;
    box-shadow: 0 0 0 0.2rem rgba(77, 171, 247, 0.25);
  }

  .table-kondimen-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table-kondimen-wrapper::-webkit-scrollbar {
    height: 8px;
  }

  .table-kondimen-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  .table-kondimen-wrapper::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
  }

  .table-kondimen-wrapper::-webkit-scrollbar-thumb:hover {
    background: #555;
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

  /* Select2 di modal */
  .select2-dropdown {
    z-index: 2100 !important;
  }

  .select2-container--bootstrap-5 .select2-selection {
    min-height: calc(1.5em + 0.75rem + 2px);
  }
</style>