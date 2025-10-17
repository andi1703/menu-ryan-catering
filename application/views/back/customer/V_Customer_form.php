<!-- Modal Form Customer -->
<div class="modal fade" id="form-modal-customer-form" tabindex="-1" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formCustomer" enctype="multipart/form-data" method="post">
        <input type="hidden" name="stat" value="edit">
        <input type="hidden" name="id" id="id" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="modalCustomerLabel">Tambah Customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="nama_customer">Nama Customer <span class="text-danger">*</span></label>
                <input type="text" required name="nama_customer" id="nama_customer" class="form-control" placeholder="Masukkan nama customer">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="no_hp">No Hp <span class="text-danger">*</span></label>
                <input type="text" required name="no_hp" id="no_hp" class="form-control" placeholder="Masukkan no hp">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" required name="email" id="email" class="form-control" placeholder="Masukkan Email">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="status_aktif">Status</label>
                <select name="status_aktif" id="status_aktif" class="form-control">
                  <option value="1">Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="harga_makan">Harga Makan <span class="text-danger">*</span></label>
                <input type="number" required min="0" name="harga_makan" id="harga_makan" class="form-control" placeholder="Masukkan harga makan">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="food_cost_max">Food Cost Max <span class="text-danger">*</span></label>
                <input type="number" required min="0" name="food_cost_max" id="food_cost_max" class="form-control" placeholder="Masukkan food cost maksimal">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="alamat">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" id="alamat" class="form-control" rows="4" placeholder="Masukkan alamat"></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="customer_img">Gambar Customer</label>
                <input type="file" name="customer_img" id="customer_img" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
              </div>
              <div id="image-preview" class="text-center" style="display: none;">
                <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e3e6f0;">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>