<div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calcModalLabel">Tambahkan Penghitungan Sayur</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-3">
          <div class="col-md-3">
            <label class="form-label">Tanggal</label>
            <input type="date" id="f_tanggal" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Customer</label>
            <select id="f_customer" class="form-control">
              <option value="">Pilih Customer</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Shift</label>
            <select id="f_shift" class="form-control">
              <option value="">Pilih Shift</option>
              <option value="lunch">lunch</option>
              <option value="dinner">dinner</option>
              <option value="supper">supper</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" id="btn_filter_load"><i class="ri-filter-2-line me-1"></i> Muat Menu</button>
          </div>
        </div>

        <div class="alert alert-info py-2">
          Isi ketiga filter untuk memuat menu harian customer pada tanggal dan shift tersebut. Tabel di bawah mirip proses lama dan berisi kondimen beserta detail bahan otomatis. Simpan untuk menambahkan sesi penghitungan ini.
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle" id="calc_excel_table">
            <thead class="table-warning">
              <tr>
                <th>Menu Kondimen</th>
                <th>Jumlah Order /porsi</th>
                <th>Pembagian Sayur/porsi</th>
                <th>Hasil Pembagian Sayur</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="calc_excel_body"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success" id="btn_save_session"><i class="ri-save-3-line me-1"></i> Simpan Penghitungan</button>
      </div>
    </div>
  </div>
</div>