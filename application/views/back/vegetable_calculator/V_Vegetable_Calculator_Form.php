<div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #2a3042; color: white;">
        <h5 class="modal-title" id="calcModalLabel" style="color: white;">Tambahkan Penghitungan Sayur</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="background-color: #f8f9fa;">
        <!-- Filter Section -->
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date" id="f_tanggal" class="form-control" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">Customer</label>
                <select id="f_customer" class="form-control">
                  <option value="">Pilih Customer</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Shift</label>
                <select id="f_shift" class="form-control">
                  <option value="">Pilih Shift</option>
                  <option value="lunch">Lunch</option>
                  <option value="dinner">Dinner</option>
                  <option value="supper">Supper</option>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="btn_filter_load">
                  Muat Menu
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="alert alert-info mb-3">
          <strong>Petunjuk:</strong> Isi ketiga filter untuk memuat menu harian customer pada tanggal dan shift tersebut.
          Tabel di bawah berisi kondimen beserta detail bahan otomatis. Klik "Simpan Penghitungan" untuk menyimpan.
        </div>

        <!-- Menu Info Display -->
        <div id="menu_info_display" style="display: none;" class="mb-3">
          <div class="card shadow-sm border-0">
            <div class="card-body py-2" style="background-color: #2a3042;">
              <div class="row align-items-center">
                <div class="col">
                  <div class="row">
                    <div class="col-md-6">
                      <small class="text-white-50 d-block mb-1">NAMA MENU</small>
                      <h6 class="mb-0 text-white fw-bold" id="display_nama_menu">-</h6>
                    </div>
                    <div class="col-md-6">
                      <small class="text-white-50 d-block mb-1">JENIS MENU</small>
                      <span id="display_jenis_menu" class="badge bg-light text-dark px-3 py-2" style="font-size: 0.85rem;">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle table-sm" id="calc_excel_table">
            <thead style="background-color: #2a3042; color: white;">
              <tr>
                <th style="min-width: 150px;">Menu Kondimen</th>
                <th class="text-center" style="width: 140px;">Jumlah Order</th>
                <th class="text-center" style="width: 140px;">Pembagian/Porsi</th>
                <th class="text-center" style="width: 140px;">Hasil Pembagian</th>
                <th class="text-center" style="width: 150px;">Aksi</th>
              </tr>
            </thead>
            <tbody id="calc_excel_body"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Tutup
        </button>
        <button type="button" class="btn btn-success" id="btn_save_session">
          Simpan Penghitungan
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* Styling untuk Modal Perhitungan Sayur */
  #calcModal .modal-xl {
    max-width: 1200px;
  }

  #calc_excel_table {
    background-color: white;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  #calc_excel_table thead th {
    font-weight: 600;
    font-size: 0.8rem;
    border: none;
    padding: 8px 8px;
    letter-spacing: 0.3px;
  }

  #calc_excel_table tbody tr.condimen-row {
    background-color: #ffffff;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s;
  }

  #calc_excel_table tbody tr.condimen-row:hover {
    background-color: #f8f9fa;
  }

  #calc_excel_table tbody tr.condimen-row td {
    padding: 8px 8px;
    vertical-align: middle;
    font-size: 0.85rem;
  }

  #calc_excel_table tbody tr.condimen-row td:first-child {
    font-weight: 600;
    color: #2c3e50;
  }

  /* Detail Bahan Container */
  #calc_excel_table tbody tr.bahan-container {
    background-color: #f8f9fa;
  }

  #calc_excel_table tbody tr.bahan-container>td {
    padding: 0 !important;
  }

  #calc_excel_table tbody tr.bahan-container .nested-table-wrapper {
    padding: 15px;
    background-color: #f8f9fa;
    border-left: 3px solid #dee2e6;
  }

  #calc_excel_table .nested-table {
    background-color: white;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin: 0;
  }

  #calc_excel_table .nested-table thead {
    background-color: #2a3042;
  }

  #calc_excel_table .nested-table thead th {
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 8px 6px;
    border: 1px solid #dee2e6;
  }

  #calc_excel_table .nested-table tbody td {
    padding: 6px;
    border: 1px solid #dee2e6;
    font-size: 0.8rem;
  }

  #calc_excel_table .nested-table tbody tr:hover {
    background-color: #f8f9fa;
  }

  /* Input Fields */
  #calc_excel_table .form-control-sm {
    font-size: 0.85rem;
    padding: 6px 8px;
    border-radius: 4px;
  }

  #calc_excel_table .yield-input {
    max-width: 80px;
    text-align: center;
    font-weight: 600;
  }

  #calc_excel_table .bahan-qty {
    text-align: right;
    font-weight: 500;
  }

  /* Buttons */
  #calc_excel_table .toggle-bahan {
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: 500;
    background-color: #007bff !important;
    color: white !important;
    border: none !important;
  }

  #calc_excel_table .toggle-bahan:hover {
    background-color: #0056b3 !important;
  }

  #calc_excel_table .remove-bahan {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
  }

  /* Detail Bahan Header */
  #calc_excel_table .bahan-container .d-flex {
    background-color: #f8f9fa;
    color: #495057;
    padding: 8px 10px;
    border-radius: 0;
    margin-bottom: 0;
    border-bottom: 2px solid #dee2e6;
  }

  #calc_excel_table .bahan-container .add-bahan {
    background-color: #28a745;
    color: white;
    border: none;
    font-weight: 500;
    font-size: 0.8rem;
    padding: 5px 12px;
  }

  #calc_excel_table .bahan-container .add-bahan:hover {
    background-color: #218838;
    color: white;
  }

  /* Number Display */
  #calc_excel_table .batches-cell {
    font-weight: 700;
    color: #495057;
    font-size: 1rem;
  }

  /* Select2 in Modal */
  #calcModal .select2-container {
    width: 100% !important;
  }

  #calcModal .select2-container .select2-selection--single {
    height: 38px;
    border-radius: 4px;
  }

  #calcModal .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
  }

  /* Empty State */
  #calc_excel_body:empty::after {
    content: "Klik 'Muat Menu' untuk menampilkan data kondimen";
    display: block;
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
    font-style: italic;
    background-color: #f8f9fa;
    border-radius: 6px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    #calc_excel_table thead th {
      font-size: 0.75rem;
      padding: 8px 5px;
    }

    #calc_excel_table tbody td {
      font-size: 0.8rem;
      padding: 8px 5px;
    }
  }
</style>