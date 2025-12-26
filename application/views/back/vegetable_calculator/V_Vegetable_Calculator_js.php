<script>
  $(function() {
    const BASE_URL = window.BASE_URL || '<?php echo base_url(); ?>';
    let editingSessionId = null; // Track session ID saat edit mode
    let bahanList = []; // Store bahan from database

    // CSS untuk Select2 search box
    if (!$('#select2-bahan-custom-css').length) {
      $('<style id="select2-bahan-custom-css">')
        .text(`
          .select2-container--default .select2-search--dropdown {
            display: block !important;
            padding: 4px;
          }
          .select2-container--default .select2-search--dropdown .select2-search__field {
            padding: 4px;
            width: 100%;
            box-sizing: border-box;
          }
          .select2-bahan-dropdown .select2-search--dropdown {
            display: block !important;
          }
        `)
        .appendTo('head');
    }

    // Load bahan list from database
    $.getJSON(`${BASE_URL}vegetable-calculator/get-bahan-dropdown`)
      .done(function(res) {
        console.log('Bahan dropdown response:', res);
        if (res && res.success && res.data) {
          bahanList = res.data;
          console.log('Bahan list loaded:', bahanList.length, 'items');
        }
      })
      .fail(function(xhr, status, error) {
        console.error('Gagal memuat data bahan:', error);
        console.error('Response:', xhr.responseText);
      });

    function number(n) {
      return Intl.NumberFormat().format(n || 0);
    }

    // Helper function to generate bahan dropdown options
    function generateBahanOptions(selectedNama = '') {
      let options = '<option value="">-- Pilih Bahan --</option>';
      bahanList.forEach(b => {
        const selected = (b.nama_bahan === selectedNama) ? 'selected' : '';
        options += `<option value="${b.id_bahan}" data-satuan="${b.nama_satuan || ''}" ${selected}>${b.nama_bahan}</option>`;
      });
      return options;
    }

    // Initialize Select2 for bahan dropdown
    function initBahanSelect2() {
      $('.bahan-dropdown').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
          try {
            $(this).select2('destroy');
          } catch (e) {}
        }
      });

      $('.bahan-dropdown').select2({
        width: '100%',
        dropdownParent: $('#calcModal .modal-content'),
        placeholder: '-- Pilih Bahan --',
        allowClear: true,
        minimumResultsForSearch: 0, // Always show search box
        dropdownCssClass: 'select2-bahan-dropdown',
        language: {
          noResults: function() {
            return "Tidak ada hasil";
          },
          searching: function() {
            return "Mencari...";
          }
        }
      });

      // Re-attach change handler after Select2 initialization
      $('.bahan-dropdown').off('change.select2custom');
      $('.bahan-dropdown').on('change.select2custom', function() {
        const $row = $(this).closest('tr');
        const $satuan = $row.find('.bahan-satuan');
        const selectedOption = $(this).find('option:selected');
        const satuanValue = selectedOption.data('satuan') || '';
        $satuan.val(satuanValue);
      });
    }

    function renderExcelTable(condiments) {
      const $body = $('#excel_body');
      $body.empty();

      condiments.forEach(c => {
        const total = parseInt(c.total_order || 0, 10);
        const yieldPorsi = parseInt(c.yield_porsi || 1, 10) || 1;
        const batches = Math.ceil(total / yieldPorsi);

        const id = c.recipe_id || `${c.menu_harian_id}_${c.nama_kondimen}`;
        const menuId = c.menu_harian_id || 0;
        const komponenId = c.recipe_id || 0;
        const namaAttr = (c.nama_kondimen || '').replace(/"/g, '&quot;');
        const condRow = `
          <tr class="condimen-row" data-recipe-id="${id}" data-menu-id="${menuId}" data-komponen-id="${komponenId}" data-nama-kondimen="${namaAttr}">
            <td>${c.nama_kondimen || '-'}</td>
            <td class="text-end">${number(total)}</td>
            <td class="w-120"><input type="number" class="form-control form-control-sm yield-input" min="1" value="${yieldPorsi}" data-recipe-id="${id}"></td>
            <td class="text-end batches-cell" data-recipe-id="${id}">${number(batches)}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-outline-primary toggle-bahan" data-recipe-id="${id}"><i class="ri-list-check-2"></i> Detail Bahan</button>
            </td>
          </tr>
          <tr class="bahan-container" data-recipe-id="${id}" data-menu-id="${menuId}" data-komponen-id="${komponenId}" data-nama-kondimen="${namaAttr}" style="display:none;">
            <td colspan="5">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Detail Bahan</strong>
                <div class="btn-group">
                  <button class="btn btn-sm btn-outline-success add-bahan" data-recipe-id="${id}"><i class="ri-add-line"></i> Tambah Bahan</button>
                  <button class="btn btn-sm btn-primary save-bahan" data-recipe-id="${id}"><i class="ri-save-3-line"></i> Simpan</button>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 nested-table">
                  <thead class="table-light">
                    <tr>
                      <th width="45%">Bahan Utama</th>
                      <th width="25%">jumlah qty</th>
                      <th width="20%">satuan</th>
                      <th width="10%">aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bahan-list"></tbody>
                </table>
              </div>
            </td>
          </tr>`;
        $body.append(condRow);
      });
    }

    // Recalculate when yield changes
    $('#excel_body').on('input', '.yield-input', function() {
      const recipeId = $(this).data('recipe-id');
      const yieldVal = Math.max(1, parseInt($(this).val() || 1, 10));
      const totalOrder = parseInt($(this).closest('tr').find('td:eq(1)').text().replace(/\D/g, '') || 0, 10);
      const batches = Math.ceil(totalOrder / yieldVal);
      $(this).closest('tr').find('.batches-cell').text(number(batches));
    });

    // Toggle bahan subtable
    $('#excel_body').on('click', '.toggle-bahan', function() {
      const id = $(this).data('recipe-id');
      const $detailRow = $(`#excel_body tr.bahan-container[data-recipe-id='${id}']`);
      $detailRow.toggle();
      if ($detailRow.is(':visible')) {
        const menuId = parseInt($detailRow.data('menu-id') || 0, 10);
        const komponenId = parseInt($detailRow.data('komponen-id') || 0, 10);
        if (komponenId) {
          const $list = $detailRow.find('.bahan-list');
          if ($list.children().length === 0) {
            const namaKondimen = ($detailRow.data('nama-kondimen') || '').toString();
            const params = {
              id_komponen: komponenId
            };
            if (menuId) params.menu_harian_id = menuId;
            if (namaKondimen) params.nama_kondimen = namaKondimen;
            $.getJSON(`${BASE_URL}vegetable-calculator/bahan-get`, params).done(res => {
              const rows = (res && res.data) ? res.data : [];
              if (window.console && console.debug) console.debug('bahan-get data', rows);
              rows.forEach(r => {
                const nama = (r.bahan_nama || r.nama_bahan || '').toString();
                const satuan = (r.satuan || r.nama_satuan || '').toString();

                // Build dropdown options
                let options = '<option value="">Pilih Bahan</option>';
                bahanList.forEach(b => {
                  const selected = (b.nama_bahan === nama) ? 'selected' : '';
                  options += `<option value="${b.id_bahan}" data-satuan="${b.nama_satuan || ''}" ${selected}>${b.nama_bahan}</option>`;
                });

                const rowHtml = `
                  <tr>
                    <td><select class="form-control form-control-sm bahan-dropdown">${options}</select></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm bahan-qty" value="${(r.qty||0)}"></td>
                    <td><input type="text" class="form-control form-control-sm bahan-satuan" value="${satuan.replace(/"/g,'&quot;')}" readonly></td>
                    <td class="text-center"><button class="btn btn-sm btn-outline-danger remove-bahan"><i class="ri-delete-bin-6-line"></i></button></td>
                  </tr>`;
                $list.append(rowHtml);
              });

              // Initialize Select2 after adding rows
              initBahanSelect2();
            }).fail((jqX) => {
              console.error('bahan-get error:', jqX && jqX.responseText);
            });
          }
        }
      }
    });

    // Add bahan row
    $('#excel_body').on('click', '.add-bahan', function() {
      const id = $(this).data('recipe-id');
      const $list = $(`#excel_body tr.bahan-container[data-recipe-id='${id}'] .bahan-list`);

      const rowHtml = `
        <tr>
          <td><select class="form-control form-control-sm bahan-dropdown">${generateBahanOptions()}</select></td>
          <td><input type="number" step="0.01" class="form-control form-control-sm bahan-qty" placeholder="0"></td>
          <td><input type="text" class="form-control form-control-sm bahan-satuan" placeholder="satuan" readonly></td>
          <td class="text-center"><button class="btn btn-sm btn-outline-danger remove-bahan"><i class="ri-delete-bin-6-line"></i></button></td>
        </tr>`;
      $list.append(rowHtml);

      // Initialize Select2 for the new row
      initBahanSelect2();
    });

    // Handle bahan dropdown change - auto populate satuan
    $('#excel_body').on('change', '.bahan-dropdown', function() {
      const $row = $(this).closest('tr');
      const $satuan = $row.find('.bahan-satuan');
      const selectedOption = $(this).find('option:selected');
      const satuanValue = selectedOption.data('satuan') || '';
      $satuan.val(satuanValue);
    });

    // Remove bahan row
    $('#excel_body').on('click', '.remove-bahan', function() {
      $(this).closest('tr').remove();
    });

    // Save bahan rows
    $('#excel_body').on('click', '.save-bahan', function() {
      const id = $(this).data('recipe-id');
      const $detailRow = $(`#excel_body tr.bahan-container[data-recipe-id='${id}']`);
      const menuId = parseInt($detailRow.data('menu-id') || 0, 10);
      const komponenId = parseInt($detailRow.data('komponen-id') || 0, 10);
      if (!menuId || !komponenId) {
        return Swal.fire('Gagal', 'Kunci kondimen tidak valid', 'error');
      }
      const items = [];
      $detailRow.find('.bahan-list tr').each(function() {
        const $dropdown = $(this).find('.bahan-dropdown');
        const nama = $dropdown.find('option:selected').text();
        const qty = parseFloat($(this).find('.bahan-qty').val() || '0');
        const satuan = $(this).find('.bahan-satuan').val().trim();
        if (nama && nama !== 'Pilih Bahan') items.push({
          bahan_nama: nama,
          qty: isNaN(qty) ? 0 : qty,
          satuan: satuan
        });
      });
      $.post(`${BASE_URL}vegetable-calculator/bahan-save`, {
        menu_harian_id: menuId,
        id_komponen: komponenId,
        items: JSON.stringify(items)
      }).done(res => {
        try {
          res = (typeof res === 'string') ? JSON.parse(res) : res;
        } catch (e) {}
        if (res && res.success) {
          Swal.fire('Tersimpan', 'Detail bahan berhasil disimpan', 'success');
        } else {
          Swal.fire('Gagal', 'Tidak dapat menyimpan detail bahan', 'error');
        }
      }).fail(() => Swal.fire('Error', 'Gagal menyimpan detail bahan', 'error'));
    });

    // Load data (legacy manual IDs or date range)
    $('#btn_load').on('click', function() {
      const ids = ($('#menu_harian_ids').val() || '').trim();
      if (ids.length > 0) {
        $.getJSON(`${BASE_URL}vegetable-calculator/kondimen`, {
            menu_ids: ids
          })
          .done(res => renderExcelTable(res.data || []))
          .fail(() => Swal.fire('Error', 'Gagal memuat kondimen dari ID menu harian', 'error'));
        return;
      }

      // fallback by date range
      const start = $('#start_date').val();
      const end = $('#end_date').val();
      if (!start || !end) return Swal.fire('Missing input', 'Isi Menu Harian ID atau tanggal', 'warning');

      $.getJSON(`${BASE_URL}vegetable-calculator/table`, {
        start,
        end,
        customer_id: $('#customer_id').val(),
        kantin_id: $('#kantin_id').val()
      }).done(res => {
        renderExcelTable(res.data || []);
      }).fail((jqX) => {
        console.error('vegetable-calculator/table error:', jqX && jqX.responseText);
        Swal.fire('Error', 'Failed to load kondimen detail', 'error');
      });
    });

    // --- New Flow: Sessions list + modal ---
    window.loadCalcSessions = function() {
      $.getJSON(`${BASE_URL}vegetable-calculator/sessions`).done(res => {
        const rows = (res && res.data) ? res.data : [];
        const $body = $('#calc-sessions-tbody');
        $body.empty();
        rows.forEach((r, i) => {
          const shiftClass = r.shift ? `shift-${r.shift.toLowerCase()}` : '';
          const tr = `
            <tr>
              <td class="text-center">${i + 1}</td>
              <td>${r.tanggal}</td>
              <td class="text-center"><span class="shift-badge ${shiftClass}">${r.shift || '-'}</span></td>
              <td>${r.customer_nama || r.customer_id || '-'}</td>
              <td class="text-center">${number(r.total_menu || 0)}</td>
              <td class="text-center">${number(r.total_bahan || 0)}</td>
              <td class="text-center">
                <div class="btn-group btn-group-sm" role="group">
                  <a href="${BASE_URL}vegetable-calculator/detail/${r.id}" class="btn btn-info" title="View"><i class="ri-eye-line"></i></a>
                  <button class="btn btn-warning" onclick="editSession(${r.id})" title="Edit"><i class="ri-edit-2-line"></i></button>
                  <button class="btn btn-danger" onclick="deleteSession(${r.id})" title="Delete"><i class="ri-delete-bin-line"></i></button>
                </div>
              </td>
            </tr>`;
          $body.append(tr);
        });
      });
    };

    window.editSession = function(id) {
      // Set edit mode
      editingSessionId = id;

      // Load session data dan isi form
      $.getJSON(`${BASE_URL}vegetable-calculator/session-detail`, {
          id: id
        })
        .done(function(res) {
          const data = (res && res.data) ? res.data : null;
          if (!data || !data.session) {
            Swal.fire('Error', 'Data session tidak ditemukan', 'error');
            editingSessionId = null;
            return;
          }

          const s = data.session;

          // Isi form filter
          $('#f_tanggal').val(s.tanggal || '');
          $('#f_customer').val(s.customer_id || '');
          $('#f_shift').val((s.shift || '').toLowerCase());

          // Load kondimen data
          if (data.items && data.items.length > 0) {
            renderCalcTable(data.items.map(function(item) {
              return {
                menu_harian_id: item.menu_harian_id,
                recipe_id: item.id_komponen,
                id_komponen: item.id_komponen,
                nama_kondimen: item.nama_kondimen,
                total_order: item.qty_kondimen,
                yield_porsi: 1
              };
            }));
          }

          // Buka modal
          $('#calcModal').modal('show');
        })
        .fail(function() {
          Swal.fire('Error', 'Gagal memuat data session', 'error');
          editingSessionId = null;
        });
    };

    window.deleteSession = function(id) {
      Swal.fire({
        title: 'Hapus sesi?',
        text: 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
      }).then((resSw) => {
        if (resSw.isConfirmed) {
          $.post(`${BASE_URL}vegetable-calculator/session-delete`, {
              id
            })
            .done(resp => {
              try {
                resp = (typeof resp === 'string') ? JSON.parse(resp) : resp;
              } catch (e) {}
              if (resp && resp.success) {
                Swal.fire('Terhapus', 'Sesi penghitungan dihapus', 'success');
                window.loadCalcSessions();
              } else {
                Swal.fire('Gagal', (resp && resp.message) || 'Tidak dapat menghapus sesi', 'error');
              }
            })
            .fail(() => Swal.fire('Error', 'Gagal menghapus sesi', 'error'));
        }
      });
    };

    $('#btn_open_modal').on('click', function() {
      // Reset edit mode saat create new
      editingSessionId = null;
      $('#f_tanggal').val('');
      $('#f_customer').val('');
      $('#f_shift').val('');
      $('#calc_excel_body').empty();
      $('#calcModal').modal('show');
    });

    // ============== MODAL FORM HANDLERS (from V_Vegetable_Calculator_Form.php) ==============

    function number(n) {
      return Intl.NumberFormat().format(n || 0);
    }

    function renderCalcTable(condiments) {
      const $body = $('#calc_excel_body');
      $body.empty();
      let firstOpened = false;

      console.log('renderCalcTable received:', condiments);

      // Deduplikasi berdasarkan kombinasi menu_harian_id + id_komponen + nama_kondimen
      const uniqueMap = new Map();
      condiments.forEach((c) => {
        const key = `${c.menu_harian_id || 0}_${c.recipe_id || c.id_komponen || 0}_${(c.nama_kondimen || '').trim()}`;

        if (uniqueMap.has(key)) {
          // Jika sudah ada, tambahkan qty nya
          const existing = uniqueMap.get(key);
          existing.total_order = (parseInt(existing.total_order || 0, 10) + parseInt(c.total_order || 0, 10));
          console.log(`Duplicate found: ${key}, merging qty to ${existing.total_order}`);
        } else {
          uniqueMap.set(key, c);
        }
      });

      const uniqueCondiments = Array.from(uniqueMap.values());
      console.log('After deduplication:', uniqueCondiments);

      uniqueCondiments.forEach((c, idx) => {
        const total = parseInt(c.total_order || 0, 10);
        const yieldPorsi = parseInt(c.yield_porsi || 1, 10) || 1;
        const batches = Math.ceil(total / yieldPorsi);

        const id = c.recipe_id || `${c.menu_harian_id}_${c.nama_kondimen}`;
        const menuId = c.menu_harian_id || 0;
        const komponenId = c.recipe_id || 0;
        const namaAttr = (c.nama_kondimen || '').replace(/"/g, '&quot;');
        const row = `
          <tr class="condimen-row" data-recipe-id="${id}" data-menu-id="${menuId}" data-komponen-id="${komponenId}" data-nama-kondimen="${namaAttr}">
            <td>${c.nama_kondimen || '-'}</td>
            <td class="text-end">${number(total)}</td>
            <td class="w-120"><input type="number" class="form-control form-control-sm yield-input" min="1" value="${yieldPorsi}" data-recipe-id="${id}"></td>
            <td class="text-end batches-cell" data-recipe-id="${id}">${number(batches)}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-outline-primary toggle-bahan" data-recipe-id="${id}"><i class="ri-list-check-2"></i> Detail Bahan</button>
            </td>
          </tr>
          <tr class="bahan-container" data-recipe-id="${id}" data-menu-id="${menuId}" data-komponen-id="${komponenId}" data-nama-kondimen="${namaAttr}" style="display:none;">
            <td colspan="5">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Detail Bahan</strong>
                <button class="btn btn-sm btn-outline-success add-bahan" data-recipe-id="${id}"><i class="ri-add-line"></i> Tambah Bahan</button>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 nested-table">
                  <thead class="table-light">
                    <tr>
                      <th width="45%">Bahan Utama</th>
                      <th width="25%">jumlah qty</th>
                      <th width="20%">satuan</th>
                      <th width="10%">aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bahan-list"></tbody>
                </table>
              </div>
            </td>
          </tr>`;
        $body.append(row);

        // Prefetch bahan untuk semua kondimen; otomatis buka baris pertama yang punya mapping
        const $detailRow = $(`#calc_excel_body tr.bahan-container[data-recipe-id='${id}']`);
        const $list = $detailRow.find('.bahan-list');
        const params = {
          id_komponen: komponenId
        };
        if (menuId) params.menu_harian_id = menuId;
        const nk = ($detailRow.data('nama-kondimen') || '').toString();
        if (nk) params.nama_kondimen = nk;
        console.log('Prefetch bahan for:', {
          nama: nk,
          komponenId,
          menuId,
          params
        });
        $.getJSON(`${BASE_URL}vegetable-calculator/bahan-get`, params).done((res) => {
          console.log('Bahan response:', res);
          const rows = (res && res.data) ? res.data : [];
          $list.empty();
          rows.forEach(r => {
            const nama = (r.bahan_nama || r.nama_bahan || '').toString();
            const satuan = (r.satuan || r.nama_satuan || '').toString();

            const rowHtml = `
              <tr>
                <td><select class="form-control form-control-sm bahan-dropdown">${generateBahanOptions(nama)}</select></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm bahan-qty" value="${(r.qty||0)}"></td>
                <td><input type="text" class="form-control form-control-sm bahan-satuan" value="${satuan.replace(/\"/g,'&quot;')}" readonly></td>
                <td class="text-center"><button class="btn btn-sm btn-outline-danger remove-bahan"><i class="ri-delete-bin-6-line"></i></button></td>
              </tr>`;
            $list.append(rowHtml);
          });

          // Initialize Select2 after loading data
          initBahanSelect2();
          if (!firstOpened && rows.length > 0) {
            $detailRow.show();
            firstOpened = true;
            // Scroll agar terlihat
            const top = $detailRow.offset() ? $detailRow.offset().top - 120 : null;
            if (top) $(document).scrollTop(top);
          }
        }).fail((xhr, status, error) => {
          console.error('Bahan request FAILED:', {
            xhr,
            status,
            error,
            params,
            komponenId,
            nama: nk
          });
        });
      });
    }

    // events inside modal scope
    $(document).on('input', '#calc_excel_body .yield-input', function() {
      const recipeId = $(this).data('recipe-id');
      const yieldVal = Math.max(1, parseInt($(this).val() || 1, 10));
      const totalOrder = parseInt($(this).closest('tr').find('td:eq(1)').text().replace(/\D/g, '') || 0, 10);
      const batches = Math.ceil(totalOrder / yieldVal);
      $(this).closest('tr').find('.batches-cell').text(number(batches));
    });

    $(document).on('click', '#calc_excel_body .toggle-bahan', function() {
      const id = $(this).data('recipe-id');
      const $detailRow = $(`#calc_excel_body tr.bahan-container[data-recipe-id='${id}']`);
      $detailRow.toggle();
      if ($detailRow.is(':visible')) {
        const menuId = parseInt($detailRow.data('menu-id') || 0, 10);
        const komponenId = parseInt($detailRow.data('komponen-id') || 0, 10);
        const $list = $detailRow.find('.bahan-list');
        if ($list.children().length === 0) {
          const namaKondimen = ($detailRow.data('nama-kondimen') || '').toString();
          const params = {
            id_komponen: komponenId
          };
          if (menuId) params.menu_harian_id = menuId;
          if (namaKondimen) params.nama_kondimen = namaKondimen;
          $.getJSON(`${BASE_URL}vegetable-calculator/bahan-get`, params).done(res => {
            const rows = (res && res.data) ? res.data : [];
            rows.forEach(r => {
              const nama = (r.bahan_nama || r.nama_bahan || '').toString();
              const satuan = (r.satuan || r.nama_satuan || '').toString();

              // Build dropdown options
              let options = '<option value="">Pilih Bahan</option>';
              bahanList.forEach(b => {
                const selected = (b.nama_bahan === nama) ? 'selected' : '';
                options += `<option value="${b.id_bahan}" data-satuan="${b.nama_satuan || ''}" ${selected}>${b.nama_bahan}</option>`;
              });

              const rowHtml = `
                <tr>
                  <td><select class="form-control form-control-sm bahan-dropdown">${options}</select></td>
                  <td><input type="number" step="0.01" class="form-control form-control-sm bahan-qty" value="${(r.qty||0)}"></td>
                  <td><input type="text" class="form-control form-control-sm bahan-satuan" value="${satuan.replace(/"/g,'&quot;')}" readonly></td>
                  <td class="text-center"><button class="btn btn-sm btn-outline-danger remove-bahan"><i class="ri-delete-bin-6-line"></i></button></td>
                </tr>`;
              $list.append(rowHtml);
            });

            // Initialize Select2 for loaded bahan
            initBahanSelect2();
          });
        }
      }
    });

    // Handle bahan dropdown change in calc_excel_body
    $(document).on('change', '#calc_excel_body .bahan-dropdown', function() {
      const $row = $(this).closest('tr');
      const $satuan = $row.find('.bahan-satuan');
      const selectedOption = $(this).find('option:selected');
      const satuanValue = selectedOption.data('satuan') || '';
      $satuan.val(satuanValue);
    });

    // Add bahan row in modal (calc_excel_body)
    $(document).on('click', '.add-bahan', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Add bahan clicked!');
      const id = $(this).data('recipe-id');
      const $container = $(this).closest('.bahan-container');
      const $list = $container.find('.bahan-list');
      console.log('Recipe ID:', id, 'Container found:', $container.length, 'List found:', $list.length, 'Bahan list size:', bahanList.length);

      const newRow = `
        <tr>
          <td><select class="form-control form-control-sm bahan-dropdown">${generateBahanOptions()}</select></td>
          <td><input type="number" step="0.01" class="form-control form-control-sm bahan-qty" placeholder="0"></td>
          <td><input type="text" class="form-control form-control-sm bahan-satuan" placeholder="satuan" readonly></td>
          <td class="text-center"><button class="btn btn-sm btn-outline-danger remove-bahan"><i class="ri-delete-bin-6-line"></i></button></td>
        </tr>`;
      $list.append(newRow);

      // Initialize Select2 for the new dropdown
      initBahanSelect2();

      console.log('Row appended with Select2');
    });

    // Remove bahan row  
    $(document).on('click', '.remove-bahan', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).closest('tr').remove();
    });

    // Load menus based on filters
    $('#btn_filter_load').on('click', function() {
      const tanggal = $('#f_tanggal').val();
      const customer_id = $('#f_customer').val();
      const shift = $('#f_shift').val();
      if (!tanggal || !customer_id || !shift) {
        return Swal.fire('Input belum lengkap', 'Isi tanggal, customer, dan shift', 'warning');
      }
      $.getJSON(`${BASE_URL}vegetable-calculator/table`, {
        start: tanggal,
        end: tanggal,
        customer_id: customer_id,
        kantin_id: '',
        shift: shift
      }).done(res => {
        console.log('Kondimen data from server:', res);
        renderCalcTable(res.data || []);
      }).fail(() => Swal.fire('Error', 'Gagal memuat data kondimen', 'error'));
    });

    // Save session + semua data bahan sekaligus
    $('#btn_save_session').on('click', function() {
      const tanggal = $('#f_tanggal').val();
      const customer_id = $('#f_customer').val();
      const shift = $('#f_shift').val();
      if (!tanggal || !customer_id || !shift) {
        return Swal.fire('Input belum lengkap', 'Isi tanggal, customer, dan shift', 'warning');
      }

      // Collect semua data bahan dari semua kondimen
      const allBahanData = [];
      $('#calc_excel_body tr.bahan-container').each(function() {
        const menuId = parseInt($(this).data('menu-id') || 0, 10);
        const komponenId = parseInt($(this).data('komponen-id') || 0, 10);
        if (!menuId || !komponenId) return;

        const items = [];
        $(this).find('.bahan-list tr').each(function() {
          const $dropdown = $(this).find('.bahan-dropdown');
          const nama = $dropdown.find('option:selected').text();
          const qty = $(this).find('.bahan-qty').val();
          const satuan = $(this).find('.bahan-satuan').val();
          if (nama && nama !== 'Pilih Bahan') {
            items.push({
              bahan_nama: nama.trim(),
              qty: parseFloat(qty) || 0,
              satuan: satuan ? satuan.trim() : null
            });
          }
        });

        if (items.length > 0) {
          allBahanData.push({
            menu_harian_id: menuId,
            id_komponen: komponenId,
            items: items
          });
        }
      });

      const postData = {
        tanggal,
        customer_id,
        shift,
        bahan_data: JSON.stringify(allBahanData)
      };

      // Jika edit mode, tambahkan session ID
      if (editingSessionId) {
        postData.session_id = editingSessionId;
      }

      console.log('SAVE REQUEST:', postData);
      console.log('Edit mode:', editingSessionId ? 'UPDATE' : 'CREATE');
      console.log('Bahan data collected:', allBahanData);

      const endpoint = editingSessionId ? 'session-update' : 'session-create';
      $.post(`${BASE_URL}vegetable-calculator/${endpoint}`, postData)
        .done(resp => {
          console.log('SAVE RESPONSE:', resp);
          try {
            resp = (typeof resp === 'string') ? JSON.parse(resp) : resp;
          } catch (e) {
            console.error('JSON parse error:', e);
          }
          if (resp && resp.success) {
            const message = editingSessionId ? 'Sesi penghitungan diupdate' : 'Sesi penghitungan disimpan';
            Swal.fire('Berhasil', message, 'success');
            editingSessionId = null; // Reset edit mode
            $('#calcModal').modal('hide');
            // refresh list
            if (typeof window.loadCalcSessions === 'function') window.loadCalcSessions();
          } else {
            console.error('Save failed:', resp);
            Swal.fire('Gagal', (resp && resp.message) || 'Tidak dapat menyimpan sesi', 'error');
          }
        })
        .fail((xhr, status, error) => {
          console.error('SAVE AJAX FAILED:', {
            xhr,
            status,
            error,
            responseText: xhr.responseText
          });
          Swal.fire('Error', 'Gagal menyimpan sesi penghitungan: ' + error, 'error');
        });
    });

    // Populate customer dropdown saat modal dibuka (Bootstrap 4)
    $('#calcModal').on('show.bs.modal', function() {
      console.log('Modal show event fired!');
      const $customer = $('#f_customer');
      console.log('Current customer options:', $customer.children('option').length);

      if ($customer.children('option').length <= 1) {
        console.log('Loading customer dropdown...');
        $.getJSON(`${BASE_URL}menu-harian/get_customers`)
          .done(function(customers) {
            console.log('Customers loaded:', customers);
            if (Array.isArray(customers) && customers.length > 0) {
              customers.forEach(function(c) {
                const id = c.id_customer || c.id;
                const nama = c.nama_customer || c.nama || 'Unknown';
                console.log('Adding customer:', id, nama);
                $customer.append(`<option value="${id}">${nama}</option>`);
              });
              console.log('Final customer options:', $customer.children('option').length);
            } else {
              console.warn('No customers returned from API');
            }
          })
          .fail(function(xhr, status, error) {
            console.error('Failed to load customers:', {
              status: status,
              error: error,
              responseText: xhr.responseText
            });
          });
      } else {
        console.log('Customer dropdown already populated');
      }
    });

    // initial list
    window.loadCalcSessions();
  });
</script>