<!-- Modal Form -->
<div class="modal fade" id="modal_form" tabindex="-1" aria-labelledby="modal_form_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_form_label">Form Thematic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_thematic" method="POST">
        <div class="modal-body">
          <input type="hidden" id="method" name="method" value="">
          <input type="hidden" id="id_thematik" name="id_thematik" value="">

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="thematik_nama" class="form-label">Nama Thematic *</label>
                <input type="text" class="form-control" id="thematik_nama" name="thematik_nama" placeholder="Contoh: Indonesia, Malaysia, Singapura" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="urutan_tampil" class="form-label">Urutan Tampil *</label>
                <input type="number" class="form-control" id="urutan_tampil" name="urutan_tampil" min="1" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label for="thematik_deskripsi" class="form-label">Deskripsi Thematic</label>
                <textarea class="form-control" id="thematik_deskripsi" name="thematik_deskripsi" rows="4" placeholder="Masukkan deskripsi tentang thematic, budaya kuliner, atau ciri khas masakan..."></textarea>
                <small class="form-text text-muted">Opsional - Deskripsikan karakteristik kuliner atau budaya thematic</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-2"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary" id="btn_save">
            <i class="ri-save-line me-2"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>