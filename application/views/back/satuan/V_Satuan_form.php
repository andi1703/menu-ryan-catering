<div class="modal fade" id="form-modal-satuan" tabindex="-1" data-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <form id="formSatuan" method="post">
        <input type="hidden" name="stat" value="edit">
        <input type="hidden" name="id" id="id" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="modalSatuanLabel">Tambah Satuan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nama_satuan">Nama Satuan <span class="text-danger">*</span></label>
            <input type="text" required name="nama_satuan" id="nama_satuan" class="form-control" placeholder="Contoh: kg, gram, ml, l">
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