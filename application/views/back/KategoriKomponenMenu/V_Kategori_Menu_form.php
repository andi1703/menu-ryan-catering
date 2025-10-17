<!-- Modal Form Kategori Menu -->
<div class="modal fade" id="form-modal-kategori-form" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-data" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Tambah Kategori Menu</h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Hidden Fields -->
            <div class="col-md-12" style="display: none;">
              <input type="hidden" name="id" id="id" value="">
              <input type="hidden" name="stat" id="stat" value="new">
            </div>

            <!-- Nama Kategori -->
            <div class="col-md-12">
              <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori<span class="text-danger">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" placeholder="Masukkan nama kategori" required>
              </div>
            </div>

            <!-- Deskripsi Kategori -->
            <div class="col-md-12">
              <div class="mb-3">
                <label for="deskripsi_kategori" class="form-label">Deskripsi Kategori</label>
                <textarea name="deskripsi_kategori" id="deskripsi_kategori" class="form-control" rows="4" placeholder="Masukkan deskripsi kategori (opsional)"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">
            <i class="bx bx-x me-1"></i> Batal
          </button>
          <button type="submit" id="btn_save" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>