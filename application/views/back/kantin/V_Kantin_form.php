<?php
?>
<div class="modal fade" id="form-modal-kantin" tabindex="-1" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formKantin" method="post">
        <input type="hidden" name="stat" value="edit">
        <input type="hidden" name="id" id="id" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="modalKantinLabel">Tambah Kantin</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="nama_kantin">Nama Kantin <span class="text-danger">*</span></label>
                <input type="text" required name="nama_kantin" id="nama_kantin" class="form-control" placeholder="Masukkan nama kantin">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_customer">Customer <span class="text-danger">*</span></label>
                <select name="id_customer" id="id_customer" class="form-control" required>
                  <option value="">Pilih Customer</option>
                  <?php if (isset($customer_list)) : ?>
                    <?php foreach ($customer_list as $c) : ?>
                      <option value="<?= $c['id_customer'] ?>"><?= htmlspecialchars($c['nama_customer']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="alamat_lokasi">Alamat Lokasi <span class="text-danger">*</span></label>
                <input type="text" required name="alamat_lokasi" id="alamat_lokasi" class="form-control" placeholder="Masukkan alamat lokasi">
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