<script type="text/javascript">
  (function() {
    "use strict";

    if (typeof jQuery === "undefined") {
      if (window.console && typeof window.console.error === "function") {
        console.error("menu_harian.js: jQuery tidak ditemukan.");
      }
      return;
    }
    if (window.menuHarianInitialized) return;
    window.menuHarianInitialized = true;

    var base_url = $("#ajax_url").val();
    var kondimenList = [];
    var menuList = [];
    var isRendering = false;
    var renderTimeout = null;
    var dataTable = null; // ✅ TAMBAHKAN GLOBAL VARIABLE

    // Toast & error popup (SAMA SEPERTI V_Menu_js.php)
    function showSuccess(message) {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "Berhasil!",
          text: message,
          icon: "success",
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });
      } else {
        alert("✅ " + message);
      }
    }

    function showError(message) {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "Error!",
          text: message,
          icon: "error",
          timer: 3000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });
      } else {
        alert("❌ " + message);
      }
    }

    // Style tweaks for Select2
    (function() {
      var css =
        "\n.select2-container{width:100% !important;}\n.select2-container .select2-selection--single{height:38px;}\n.select2-dropdown{z-index:2100 !important;}\n";
      var style = document.createElement("style");
      style.type = "text/css";
      if (style.styleSheet) style.styleSheet.cssText = css;
      else style.appendChild(document.createTextNode(css));
      document.getElementsByTagName("head")[0].appendChild(style);
    })();

    function renderKantinRadio(kantinList) {
      console.log('[renderKantinRadio] Called with:', kantinList);

      var html = "";
      if (Array.isArray(kantinList) && kantinList.length > 0) {
        kantinList.forEach(function(kantin) {
          html +=
            "<div class='form-check'>" +
            "<input class='form-check-input kantin-checkbox' type='checkbox' name='id_kantin[]' id='kantin_" +
            kantin.id_kantin +
            "' data-nama='" +
            (kantin.nama_kantin || "") +
            "' value='" +
            kantin.id_kantin +
            "'>" +
            "<label class='form-check-label' for='kantin_" +
            kantin.id_kantin +
            "'>" +
            kantin.nama_kantin +
            "</label>" +
            "</div>";
        });
      } else {
        html = '<p class="text-muted mb-0">Tidak ada kantin tersedia</p>';
      }

      console.log('[renderKantinRadio] HTML generated, length:', html.length);

      // Update HTML
      $("#kantin-checkbox-group").html(html);

      console.log('[renderKantinRadio] Checkbox count:', $('.kantin-checkbox').length);

      // Update dropdown text
      updateKantinDropdownText();
    }

    // ✅ TAMBAHKAN FUNCTION INI (SETELAH renderKantinRadio)
    function updateKantinDropdownText() {
      var checkedCount = $('.kantin-checkbox:checked').length;
      var totalCount = $('.kantin-checkbox').length;
      var dropdown = $('#kantin-dropdown');
      var textSpan = $('#kantin-selected-count');

      console.log('[updateKantinDropdownText] Checked:', checkedCount, 'Total:', totalCount);

      if (!textSpan.length) {
        console.warn('[updateKantinDropdownText] Element #kantin-selected-count not found');
        return;
      }

      if (checkedCount === 0) {
        textSpan.text('- Pilih Kantin -');
        dropdown.removeClass('has-selection');
      } else if (checkedCount === totalCount && totalCount > 0) {
        textSpan.text('Semua Kantin (' + checkedCount + ')');
        dropdown.addClass('has-selection');
      } else {
        textSpan.text(checkedCount + ' Kantin Dipilih');
        dropdown.addClass('has-selection');
      }

      console.log('[updateKantinDropdownText] Updated text:', textSpan.text());
    }

    function getSelectedKantins() {
      var selected = [];
      $(".kantin-checkbox:checked").each(function() {
        var id = $(this).val();
        var nama =
          $(this).data("nama") || $('label[for="kantin_' + id + '"]').text();
        selected.push({
          id_kantin: id,
          nama_kantin: nama
        });
      });
      return selected;
    }

    // ✅ PERBAIKAN UTAMA: loadMenuHarianData()
    function loadMenuHarianData() {
      console.log('[loadMenuHarianData] Called');

      // ✅ DESTROY DENGAN AMAN (HANYA JIKA ADA)
      if (dataTable !== null) {
        try {
          dataTable.destroy();
          dataTable = null;
          console.log('[loadMenuHarianData] DataTable destroyed');
        } catch (e) {
          console.warn('[loadMenuHarianData] Error destroying:', e);
          dataTable = null;
        }
      }

      // ✅ KOSONGKAN TBODY
      $("#menu-harian-table tbody").empty();

      // ✅ TAMPILKAN LOADING
      $("#menu-harian-table tbody").html(buildLoadingRow());
      $("#menu-harian-table").show();

      $.ajax({
        url: base_url + "/ajax_list",
        type: "POST",
        dataType: "json",
        success: renderMenuHarianTable,
        error: function(xhr, status, error) {
          console.error("[loadMenuHarianData] AJAX Error:", error);
          console.error("[loadMenuHarianData] Response:", xhr.responseText);
          $("#menu-harian-table tbody").html(
            '<tr><td colspan="10" class="text-center text-danger">Gagal memuat data!</td></tr>'
          );
        },
      });
    }

    // ✅ PERBAIKAN: renderMenuHarianTable()
    function renderMenuHarianTable(result) {
      console.log("[renderMenuHarianTable] Data received:", result);

      var html = "";
      var no = 1;

      if (
        result &&
        result.show_data &&
        Array.isArray(result.show_data) &&
        result.show_data.length > 0
      ) {
        result.show_data.forEach(function(item) {
          html += buildTableRow(item, no++);
        });
      } else if (
        result &&
        result.data &&
        Array.isArray(result.data) &&
        result.data.length > 0
      ) {
        // ✅ SUPPORT UNTUK RESPONSE DARI CONTROLLER (result.data)
        result.data.forEach(function(item) {
          html += buildTableRow(item, no++);
        });
      } else {
        html = buildEmptyRow();
      }

      // ✅ INJECT HTML (TANPA DESTROY LAGI!)
      $("#menu-harian-table tbody").html(html);

      // ✅ INITIALIZE DATATABLES
      initDataTable();
    }

    function buildTableRow(item, no) {
      function getShiftBadge(shift) {
        var badgeClass = "";
        switch (shift) {
          case "lunch":
            badgeClass = "badge bg-success text-white";
            break;
          case "dinner":
            badgeClass = "badge bg-primary text-white";
            break;
          case "supper":
            badgeClass = "badge bg-danger text-white";
            break;
          default:
            badgeClass = "badge bg-secondary text-white";
        }
        return `<span class="${badgeClass}">${shift.charAt(0).toUpperCase() + shift.slice(1)}</span>`;
      }

      // Update badge kategori - hapus emoji untuk space
      function getKategoriBadge(kategori) {
        if (!kategori) {
          return '<span class="badge badge-secondary" style="font-size: 0.6rem;">-</span>';
        }

        var badgeConfig = {
          // ✅ NAMA LENGKAP KATEGORI
          "nasi": {
            class: "badge-nasi",
            color: "#ffc107"
          },
          "lauk utama": {
            class: "badge-lauk-utama",
            color: "#dc3545"
          },
          "lauk_utama": {
            class: "badge-lauk-utama",
            color: "#dc3545"
          },
          "pendamping basah": {
            class: "badge-pendamping-basah",
            color: "#28a745"
          },
          "pendamping_basah": {
            class: "badge-pendamping-basah",
            color: "#28a745"
          },
          "pendamping kering": {
            class: "badge-pendamping-kering",
            color: "#17a2b8"
          },
          "pendamping_kering": {
            class: "badge-pendamping-kering",
            color: "#17a2b8"
          },
          "sayuran berkuah": {
            class: "badge-sayuran-berkuah",
            color: "#20c997"
          },
          "sayuran_berkuah": {
            class: "badge-sayuran-berkuah",
            color: "#20c997"
          },
          "buah": {
            class: "badge-buah",
            color: "#e83e8c"
          },
          "sambal": {
            class: "badge-sambal",
            color: "#fd7e14"
          },
          "tumisan": {
            class: "badge-tumisan",
            color: "#6f42c1"
          },
          "kerupuk": {
            class: "badge-pendamping-kering",
            color: "#17a2b8"
          },
          "sayur": {
            class: "badge-sayuran-berkuah",
            color: "#20c997"
          }
        };

        var key = kategori.toLowerCase().trim();
        var config = badgeConfig[key] || {
          class: "badge-secondary",
          color: "#6c757d"
        };

        // ✅ RETURN DENGAN NAMA LENGKAP (TIDAK DISINGKAT)
        return `<span class="badge ${config.class}" 
                      style="font-size: 0.6rem; padding: 2px 4px; background-color: ${config.color}; color: white; border-radius: 3px; white-space: nowrap;"
                      title="${kategori}">
                  ${kategori}
                </span>`;
      }

      // ✅ BUILD NESTED TABLE KONDIMEN - NAMA KANTIN LENGKAP
      function buildNestedKondimenTable(kondimenData, kantins) {
        console.log('[buildNestedKondimenTable] Data:', kondimenData);
        console.log('[buildNestedKondimenTable] Kantins:', kantins);

        if (!kondimenData || kondimenData.length === 0) {
          return '<span class="text-muted fst-italic" style="font-size: 0.75rem;">- Tidak ada kondimen -</span>';
        }

        // ✅ SET CSS VARIABLE UNTUK DYNAMIC WIDTH
        var kantinCount = kantins.length;

        var nestedTable = `
          <div class="nested-kondimen-wrapper">
            <table class="table nested-kondimen-table" style="--kantin-count: ${kantinCount};">
              <thead>
                <tr>
                  <th class="col-number">#</th>
                  <th class="col-nama">Kondimen</th>
                  <th class="col-kategori">Kategori</th>`;

        // ✅ HEADER UNTUK SETIAP KANTIN - NAMA LENGKAP (TIDAK DISINGKAT)
        kantins.forEach(function(kantin) {
          nestedTable += `<th class="col-qty-kantin" title="${kantin}">${kantin}</th>`;
        });

        nestedTable += `
                </tr>
              </thead>
              <tbody>`;

        // ✅ BARIS UNTUK SETIAP KONDIMEN
        kondimenData.forEach(function(k, idx) {
          console.log('[buildNestedKondimenTable] Item:', idx, k);

          var shortNama = k.nama && k.nama.length > 15 ? k.nama.substring(0, 15) + '..' : (k.nama || '-');
          var kategori = k.kategori || k.kategori_kondimen || k.nama_kategori || '-';

          nestedTable += `
                <tr>
                  <td class="text-center col-number">${idx + 1}</td>
                  <td class="col-nama" title="${k.nama || '-'}">${shortNama}</td>
                  <td class="text-center col-kategori">${getKategoriBadge(kategori)}</td>`;

          // ✅ QTY UNTUK SETIAP KANTIN
          kantins.forEach(function(kantin) {
            var qty = '-';

            if (k.qty && typeof k.qty === 'object') {
              qty = k.qty[kantin] || '-';
            } else if (k.qty_per_kantin && typeof k.qty_per_kantin === 'object') {
              qty = k.qty_per_kantin[kantin] || '-';
            } else if (k.qty_kondimen) {
              qty = k.qty_kondimen;
            }

            nestedTable += `<td class="col-qty" title="QTY ${kantin}: ${qty}">${qty}</td>`;
          });

          nestedTable += `</tr>`;
        });

        nestedTable += `
              </tbody>
            </table>
          </div>`;

        return nestedTable;
      }

      // ✅ GABUNGKAN NAMA KANTIN
      var kantinDisplay = item.kantins ? item.kantins.join(', ') : '-';

      // ✅ BUILD NESTED TABLE
      var nestedKondimen = buildNestedKondimenTable(item.kondimen_data, item.kantins || []);

      // ✅ AMBIL ID PERTAMA UNTUK EDIT (KARENA DATA DIGABUNG)
      var firstId = item.ids && item.ids.length > 0 ? item.ids[0] : 0;

      return `
        <tr>
          <td class="text-center">${no}</td>
          <td>${item.tanggal}</td>
          <td class="text-center">${getShiftBadge(item.shift)}</td>
          <td>${item.nama_customer}</td>
          <td>${kantinDisplay}</td>
          <td>${item.jenis_menu}</td>
          <td>${item.nama_menu}</td>
          <td>${nestedKondimen}</td>
          <td class="text-end">${item.total_orderan || '0'}</td>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-warning btn-sm btn-edit-menu-harian" data-id="${firstId}" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-danger btn-sm btn-delete-menu-harian" data-ids='${JSON.stringify(item.ids)}' title="Hapus Semua">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>`;
    }

    function buildLoadingRow() {
      return `<tr><td class="text-center" colspan="10"><div class="text-muted p-3"><i class="fas fa-spinner fa-spin fa-2x mb-1"></i><div>Memuat data...</div></div></td></tr>`;
    }

    function buildEmptyRow() {
      return `<tr><td class="text-center" colspan="10"><div class="text-muted p-3"><i class="fas fa-utensils fa-2x mb-1"></i><div>Tidak ada data menu harian</div></div></td></tr>`;
    }

    // ✅ PERBAIKAN: initDataTable()
    function initDataTable() {
      if (!$.fn.DataTable) {
        console.warn("[initDataTable] DataTables plugin not loaded");
        return;
      }

      try {
        console.log('[initDataTable] Initializing...');

        dataTable = $("#menu-harian-table").DataTable({
          responsive: true,
          autoWidth: false,
          destroy: true,
          order: [
            [1, 'desc']
          ],
          pageLength: 10,
          language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data tersedia",
            paginate: {
              next: "Selanjutnya",
              previous: "Sebelumnya",
            },
          },
        });

        console.log("[initDataTable] DataTable initialized successfully");
      } catch (e) {
        console.error("[initDataTable] Error:", e);
        dataTable = null;
      }
    }

    // ✅ TAMBAHKAN FUNCTION INI (SETELAH initDataTable)
    function loadCustomerOptions() {
      $.get(
        base_url + "/get_customers",
        function(data) {
          var options = '<option value="">-- Pilih Customer --</option>';
          if (data && data.length > 0) {
            $.each(data, function(_, customer) {
              options += `<option value="${customer.id_customer}">${customer.nama_customer}</option>`;
            });
          }
          $("#id_customer").html(options);
        },
        "json"
      ).fail(function(xhr) {
        console.error("[loadCustomerOptions] Error:", xhr.status);
      });
    }

    function loadKantinRadioOptions(id_customer, callback) {
      console.log("[loadKantinRadioOptions] Called with customer:", id_customer);

      var url = id_customer ?
        base_url + "/get_kantin_by_customer" :
        base_url + "/get_kantins";
      var dataAjax = id_customer ? {
        id_customer: id_customer
      } : {};

      console.log("[loadKantinRadioOptions] URL:", url);
      console.log("[loadKantinRadioOptions] Data:", dataAjax);

      $.ajax({
        url: url,
        type: id_customer ? "POST" : "GET",
        data: dataAjax,
        dataType: "json",
        success: function(kantinList) {
          console.log("[loadKantinRadioOptions] Success, received:", kantinList);
          renderKantinRadio(kantinList);
          if (typeof callback === "function") {
            console.log("[loadKantinRadioOptions] Calling callback");
            callback(kantinList);
          }
        },
        error: function(xhr, status, error) {
          console.error("[loadKantinRadioOptions] Error:", error);
          console.error("[loadKantinRadioOptions] Status:", status);
          console.error("[loadKantinRadioOptions] Response:", xhr.responseText);
        }
      });
    }

    function loadMenuList(callback) {
      $.get(
        base_url + "/get_menu_list",
        function(data) {
          menuList = data;
          console.log("[loadMenuList] Loaded:", menuList.length, "items");
          if (typeof callback === "function") callback();
        },
        "json"
      ).fail(function(xhr) {
        console.error("[loadMenuList] Error:", xhr.status);
      });
    }

    function computeTotalOrder() {
      var selected = getSelectedKantins();
      if (!selected || selected.length === 0) {
        $("#total_orderan_perkantin").val("");
        return;
      }

      var sums = {};
      selected.forEach(function(k) {
        sums[k.id_kantin] = 0;
      });

      kondimenList.forEach(function(k) {
        if (!k) return;
        var kat = (k.kategori || "").toString().toLowerCase();
        if (kat.indexOf("lauk utama") === -1) return;
        var map = k.qty_per_kantin || {};
        selected.forEach(function(sk) {
          var q = parseFloat(map[sk.id_kantin]);
          if (!isFinite(q)) q = 0;
          sums[sk.id_kantin] = (sums[sk.id_kantin] || 0) + q;
        });
      });

      var first = selected[0].id_kantin;
      var total = sums[first] || 0;
      if (Math.abs(total - Math.round(total)) < 1e-6) total = Math.round(total);
      $("#total_orderan_perkantin").val(total);
      return sums;
    }

    function ajaxDeleteMenuHarian(id) {
      $.ajax({
        url: base_url + "/delete/" + id,
        type: "POST",
        dataType: "json",
        success: function(res) {
          showSuccess(res.message || "Menu harian berhasil dihapus!");
          if (res.status === "success") {
            loadMenuHarianData();
          }
        },
        error: function() {
          showError("Gagal menghapus data!");
        },
      });
    }

    // ========== GLOBAL FUNCTIONS ==========

    window.tambah_menu_harian = function() {
      console.log("[tambah_menu_harian] Called");
      kondimenList = [];
      $("#form-menu-harian")[0].reset();
      $(".kantin-checkbox").prop("checked", false);
      $("#id_menu_harian").val("");
      $("#table-kondimen-menu tbody").empty();
      $("#table-kondimen-menu thead").empty();

      // ✅ RESET DROPDOWN TEXT
      updateKantinDropdownText();

      // ✅ RESET KANTIN DROPDOWN CONTENT
      $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');

      if (typeof window.renderKondimenTable === "function")
        window.renderKondimenTable();
      $("#form-modal-menu-harian").modal("show");
      $("#modalMenuHarianLabel").text("Tambah Menu Harian");
    };

    // ✅ PERBAIKAN FUNCTION EDIT MENU HARIAN - SUPPORT MULTIPLE KANTIN
    window.edit_menu_harian = function(id) {
      console.log("[edit_menu_harian] Called with ID:", id);

      // Reset form dan kondimen list
      kondimenList = [];
      $("#form-menu-harian")[0].reset();
      $(".kantin-checkbox").prop("checked", false);
      $("#table-kondimen-menu tbody").empty();
      $("#table-kondimen-menu thead").empty();
      updateKantinDropdownText();

      $.ajax({
        url: base_url + "/get_by_id/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
          console.log("[edit_menu_harian] Response received:", response);

          if (response.status === "success") {
            var data = response.data;
            var kondimen = response.kondimen || [];

            // ✅ SET BASIC FORM DATA
            $("#id_menu_harian").val(data.id_menu_harian);
            $("#tanggal").val(data.tanggal);
            $("#shift").val(data.shift);
            $("#jenis_menu").val(data.jenis_menu);
            $("#id_customer").val(data.id_customer);
            $("#nama_menu").val(data.nama_menu);
            $("#total_orderan_perkantin").val(data.total_orderan_perkantin);

            // ✅ PARSE MULTIPLE KANTIN IDS
            var existingKantins = [];

            // Cek berbagai format data kantin
            if (data.id_kantins && Array.isArray(data.id_kantins)) {
              // Format array
              existingKantins = data.id_kantins;
            } else if (data.id_kantins && typeof data.id_kantins === 'string') {
              // Format string comma-separated
              existingKantins = data.id_kantins.split(',').map(s => s.trim());
            } else if (data.id_kantin) {
              // Single kantin (fallback)
              existingKantins = [data.id_kantin.toString()];
            }

            console.log("[edit_menu_harian] Existing kantins:", existingKantins);

            // ✅ PROCESS KONDIMEN DATA UNTUK MULTIPLE KANTIN
            var processedKondimen = [];
            if (kondimen && kondimen.length > 0) {
              // Group kondimen by id_komponen
              var kondimenGrouped = {};

              kondimen.forEach(function(k) {
                var key = k.id_komponen;
                if (!kondimenGrouped[key]) {
                  kondimenGrouped[key] = {
                    id_komponen: k.id_komponen,
                    kategori: k.kategori_kondimen,
                    qty_per_kantin: {}
                  };
                }

                // Set qty untuk kantin ini
                var kantinId = k.id_kantin || k.kantin_id;
                var qty = k.qty_kondimen || k.qty;

                if (kantinId && qty) {
                  kondimenGrouped[key].qty_per_kantin[kantinId] = qty;
                }
              });

              // Convert grouped data to array
              processedKondimen = Object.values(kondimenGrouped);
            }

            kondimenList = processedKondimen;
            console.log("[edit_menu_harian] Processed kondimen:", kondimenList);

            // ✅ LOAD KANTIN OPTIONS BERDASARKAN CUSTOMER
            loadKantinRadioOptions(data.id_customer, function() {
              console.log("[edit_menu_harian] Kantin options loaded");

              // ✅ DELAY UNTUK MEMASTIKAN DOM SUDAH RENDERED
              setTimeout(function() {
                // Set selected kantins
                existingKantins.forEach(function(kantinId) {
                  $("#kantin_" + kantinId).prop("checked", true);
                  console.log("[edit_menu_harian] Checked kantin:", kantinId);
                });

                // ✅ UPDATE DROPDOWN TEXT
                updateKantinDropdownText();

                // ✅ RENDER KONDIMEN TABLE DENGAN DATA EXISTING
                if (typeof window.renderKondimenTable === "function") {
                  window.renderKondimenTable();
                }

                console.log("[edit_menu_harian] Form populated successfully");
              }, 200);

              // ✅ SHOW MODAL
              $("#form-modal-menu-harian").modal("show");
              $("#modalMenuHarianLabel").text("Edit Menu Harian");
            });

          } else {
            showError(response.msg || "Data tidak ditemukan!");
          }
        },
        error: function(xhr, status, error) {
          console.error("[edit_menu_harian] AJAX Error:", error);
          console.error("[edit_menu_harian] Response:", xhr.responseText);
          showError("Gagal mengambil data!");
        },
      });
    };

    function delete_menu_harian(id) {
      console.log("[delete_menu_harian] Called with ID:", id);
      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "Hapus Menu Harian?",
          text: "Data yang dihapus tidak dapat dikembalikan!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Ya, Hapus!",
          cancelButtonText: "Batal",
          reverseButtons: true,
        }).then((result) => {
          if (result.isConfirmed) {
            ajaxDeleteMenuHarian(id);
          }
        });
      } else {
        if (confirm("Yakin ingin menghapus menu harian ini?")) {
          ajaxDeleteMenuHarian(id);
        }
      }
    }

    // ========== KONDIMEN TABLE ==========

    window.tambahKondimenRow = function() {
      var selectedKantins = getSelectedKantins();
      var qtyMap = {};
      selectedKantins.forEach(function(k) {
        qtyMap[k.id_kantin] = "";
      });

      kondimenList.push({
        id_komponen: "",
        kategori: "",
        qty_per_kantin: qtyMap,
      });

      if (typeof window.renderKondimenTable === "function")
        window.renderKondimenTable();
    };

    window.hapusKondimenRow = function(idx) {
      if (typeof idx === "undefined" || idx === null) return;
      kondimenList.splice(idx, 1);
      if (typeof window.renderKondimenTable === "function")
        window.renderKondimenTable();
    };

    window.renderKondimenTable = function() {
      if (renderTimeout) {
        clearTimeout(renderTimeout);
      }

      if (isRendering) {
        console.log("[renderKondimenTable] Skipped (already rendering)");
        return;
      }

      console.log("[renderKondimenTable] Called");

      renderTimeout = setTimeout(function() {
        actualRenderKondimenTable();
      }, 100);
    };

    // ✅ UPDATE actualRenderKondimenTable UNTUK HANDLE EDIT MODE
    function actualRenderKondimenTable() {
      isRendering = true;

      if (!Array.isArray(menuList) || menuList.length === 0) {
        loadMenuList(function() {
          isRendering = false;
          if (typeof window.renderKondimenTable === "function")
            window.renderKondimenTable();
        });
        return;
      }

      var selectedKantins = getSelectedKantins();
      console.log("[actualRenderKondimenTable] Selected kantins:", selectedKantins);

      // ✅ BUILD HEADER WITH DYNAMIC KANTIN COLUMNS
      var thead =
        "<tr>" +
        '<th class="text-center" style="width:40px;">No</th>' +
        '<th style="min-width:200px;">Nama Kondimen</th>' +
        '<th style="min-width:140px;">Kategori</th>';

      selectedKantins.forEach(function(k) {
        thead += `<th class="text-center">Qty<br>${k.nama_kantin}</th>`;
      });
      thead += '<th class="text-center" style="width:80px;">Aksi</th></tr>';
      $("#table-kondimen-menu thead").html(thead);

      var tbody = "";

      // ✅ RENDER EXISTING KONDIMEN ROWS
      kondimenList.forEach(function(kondimen, idx) {
        console.log("[actualRenderKondimenTable] Rendering kondimen:", idx, kondimen);

        // ✅ ENSURE qty_per_kantin STRUCTURE
        if (!kondimen.qty_per_kantin) {
          kondimen.qty_per_kantin = {};
        }

        // ✅ ADD MISSING KANTIN ENTRIES
        selectedKantins.forEach(function(k) {
          if (kondimen.qty_per_kantin[k.id_kantin] === undefined) {
            kondimen.qty_per_kantin[k.id_kantin] = "";
          }
        });

        // ✅ BUILD SELECT OPTIONS
        var options = '<option value="">-- Pilih Kondimen --</option>';
        menuList.forEach(function(menu) {
          var sel = menu.id_komponen == kondimen.id_komponen ? "selected" : "";
          options += `<option value="${menu.id_komponen}" ${sel}>${menu.menu_nama}</option>`;
        });

        // ✅ BUILD TABLE ROW
        tbody += `<tr>
        <td class="text-center">${idx + 1}</td>
        <td>
          <div class="kondimen-select-wrap">
            <select class="form-control kondimen-nama" data-idx="${idx}">
              ${options}
            </select>
          </div>
        </td>
        <td>
          <input type="text" class="form-control kondimen-kategori" data-idx="${idx}" value="${kondimen.kategori || ""}" readonly>
        </td>`;

        // ✅ ADD QTY COLUMNS FOR EACH SELECTED KANTIN
        selectedKantins.forEach(function(k) {
          var val = kondimen.qty_per_kantin[k.id_kantin] || "";
          tbody += `<td><input type="number" min="0" step="1" class="form-control kondimen-qty" data-idx="${idx}" data-kantin="${k.id_kantin}" value="${val}"></td>`;
        });

        tbody += `
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm" onclick="hapusKondimenRow(${idx})"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
      });

      $("#table-kondimen-menu tbody").html(tbody);

      // ✅ REINITIALIZE SELECT2
      $(".kondimen-nama").each(function() {
        if ($(this).hasClass("select2-hidden-accessible")) {
          try {
            $(this).select2("destroy");
          } catch (e) {}
        }
      });

      $(".kondimen-nama").select2({
        width: "100%",
        dropdownParent: $("#form-modal-menu-harian .modal-content"),
        dropdownCss: {
          "z-index": 2100
        },
      });

      // ✅ REATTACH EVENT HANDLERS
      $(document).off("change", ".kondimen-nama");
      $(document).on("change", ".kondimen-nama", function() {
        var idx = $(this).data("idx");
        var id_komponen = $(this).val();

        var isDuplicate = kondimenList.some(function(kondimen, i) {
          return kondimen.id_komponen === id_komponen && i !== idx && id_komponen !== '';
        });
        if (isDuplicate) {
          showError("Menu kondimen sudah ada di tabel!");
          $(this).val("");
          kondimenList[idx].id_komponen = "";
          $(this).select2().val("").trigger("change.select2");
          return;
        }

        kondimenList[idx].id_komponen = id_komponen;

        if (id_komponen) {
          $.post(
            base_url + "/get_kategori_by_menu", {
              id_komponen: id_komponen
            },
            function(data) {
              kondimenList[idx].kategori = data.nama_kategori;
              $('.kondimen-kategori[data-idx="' + idx + '"]').val(data.nama_kategori);
              computeTotalOrder();
            },
            "json"
          );
        } else {
          kondimenList[idx].kategori = "";
          $('.kondimen-kategori[data-idx="' + idx + '"]').val("");
          computeTotalOrder();
        }
      });

      $(document).off("input", ".kondimen-qty");
      $(document).on("input", ".kondimen-qty", function() {
        var idx = $(this).data("idx");
        var id_kantin = $(this).data("kantin");
        var val = $(this).val();
        kondimenList[idx].qty_per_kantin = kondimenList[idx].qty_per_kantin || {};
        kondimenList[idx].qty_per_kantin[id_kantin] = val;
        computeTotalOrder();
      });

      setTimeout(function() {
        isRendering = false;
        console.log("[actualRenderKondimenTable] Complete");
      }, 200);
    }

    // ========== DOCUMENT READY ==========

    $(document).ready(function() {
      console.log('[Document Ready] Initializing...');

      loadMenuHarianData();
      loadCustomerOptions();
      loadKantinRadioOptions();
      loadMenuList();

      // ✅ TAMBAHKAN LOG UNTUK CEK ELEMEN
      console.log('[Document Ready] #kantin-dropdown exists:', $('#kantin-dropdown').length);
      console.log('[Document Ready] #kantin-checkbox-group exists:', $('#kantin-checkbox-group').length);

      // ✅ EVENT CLOSE BUTTON
      $(document).on('click', '#btn-close-modal-menu-harian', function() {
        $('#form-modal-menu-harian').modal('hide');
      });

      $(document).on('click', '[data-bs-dismiss="modal"]', function() {
        $(this).closest('.modal').modal('hide');
      });

      // ✅ TAMBAHKAN EVENT HANDLER DROPDOWN BUTTON MANUAL
      $(document).on('click', '#kantin-dropdown', function(e) {
        e.preventDefault();
        console.log('[Kantin Dropdown] Button clicked');

        var $dropdownMenu = $(this).next('.dropdown-menu');

        // Toggle dropdown visibility
        if ($dropdownMenu.hasClass('show')) {
          $dropdownMenu.removeClass('show');
          $(this).attr('aria-expanded', 'false');
        } else {
          // Close other dropdowns first
          $('.dropdown-menu').removeClass('show');
          $('[data-bs-toggle="dropdown"]').attr('aria-expanded', 'false');

          // Open this dropdown
          $dropdownMenu.addClass('show');
          $(this).attr('aria-expanded', 'true');
        }
      });

      // ✅ CLOSE DROPDOWN SAAT KLIK DI LUAR
      $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
          $('.dropdown-menu').removeClass('show');
          $('[data-bs-toggle="dropdown"]').attr('aria-expanded', 'false');
        }
      });

      // ✅ EVENT DROPDOWN MENU - PREVENT CLOSE SAAT KLIK DI DALAM
      $(document).on('click', '.dropdown-menu', function(e) {
        console.log('[Dropdown Menu] Clicked inside, preventing close');
        e.stopPropagation();
      });

      // ✅ EVENT CHANGE CHECKBOX KANTIN
      $(document).on('change', '.kantin-checkbox', function() {
        console.log('[Kantin Checkbox] Changed:', $(this).val(), $(this).is(':checked'));
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      // ✅ EVENT SELECT ALL KANTIN
      $(document).on('click', '#selectAllKantin', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('[Select All Kantin] Clicked');
        $('.kantin-checkbox').prop('checked', true);
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      // ✅ EVENT DESELECT ALL KANTIN
      $(document).on('click', '#deselectAllKantin', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('[Deselect All Kantin] Clicked');
        $('.kantin-checkbox').prop('checked', false);
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      // ✅ EVENT CHANGE CUSTOMER
      $(document).on("change", "#id_customer", function() {
        var id_customer = $(this).val();
        console.log("[Customer Changed] ID:", id_customer);

        // Reset kantin yang sudah dipilih
        $(".kantin-checkbox").prop("checked", false);
        updateKantinDropdownText();

        if (id_customer) {
          // Load kantin berdasarkan customer
          loadKantinRadioOptions(id_customer, function() {
            console.log("[Customer Changed] Kantin loaded successfully");
            // Render ulang kondimen table
            if (typeof window.renderKondimenTable === "function")
              window.renderKondimenTable();
          });
        } else {
          // Reset kantin dropdown
          $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');
          updateKantinDropdownText();

          // Kosongkan kondimen table
          $("#table-kondimen-menu thead").html('');
          $("#table-kondimen-menu tbody").html('');
        }
      });

      // ✅ EVENT KLIK TAMBAH KONDIMEN
      $(document).on("click", "#btn-tambah-kondimen", function() {
        console.log('[Tambah Kondimen] Button clicked');
        if (menuList.length === 0) {
          loadMenuList(function() {
            if (typeof window.tambahKondimenRow === "function")
              window.tambahKondimenRow();
          });
        } else {
          if (typeof window.tambahKondimenRow === "function")
            window.tambahKondimenRow();
        }
      });

      $("#form-menu-harian").on("submit", function(e) {
        e.preventDefault();

        if (kondimenList.length === 0) {
          showError("Menu kondimen wajib diisi!");
          return;
        }

        var selectedKantins = getSelectedKantins();
        if (selectedKantins.length === 0) {
          showError("Pilih minimal 1 kantin!");
          return;
        }

        for (var k of kondimenList) {
          if (!k.id_komponen) {
            showError("Semua kondimen harus dipilih!");
            return;
          }
          if (!k.qty_per_kantin) {
            showError("Qty per kantin untuk semua kondimen harus diisi!");
            return;
          }
          for (var sk of selectedKantins) {
            var q = k.qty_per_kantin[sk.id_kantin];
            if (q === undefined || q === null || q === "") {
              showError(
                "Semua qty per kantin harus diisi untuk setiap kondimen!"
              );
              return;
            }
          }
        }

        var formData = new FormData(this);
        formData.append("kondimen", JSON.stringify(kondimenList));

        $(this).find(":focus").blur();

        var $submitButton = $(this).find('button[type="submit"]');
        $submitButton
          .prop("disabled", true)
          .html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

        $.ajax({
          url: base_url + "/save",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function(res) {
            $submitButton
              .prop("disabled", false)
              .html('<i class="fas fa-save me-1"></i>Simpan');

            if (res.status === "success") {
              $("#form-modal-menu-harian").modal("hide");
              showSuccess(res.msg || "Menu harian berhasil disimpan!");
            } else {
              showError(res.msg || "Gagal menyimpan data!");
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: ", textStatus, errorThrown);
            console.error("Response:", jqXHR.responseText);

            $submitButton
              .prop("disabled", false)
              .html('<i class="fas fa-save me-1"></i>Simpan');

            showError("Terjadi kesalahan saat menyimpan data!");
          },
        });
      });

      $(document).on("hidden.bs.modal", "#form-modal-menu-harian", function() {
        loadMenuHarianData();
      });

      $(document).on("hide.bs.modal", ".modal", function() {
        $(this).find(":focus").blur();
      });

      // ✅ PERBAIKI EVENT CHANGE CUSTOMER
      $(document).on("change", "#id_customer", function() {
        var id_customer = $(this).val();
        console.log("[Customer Changed] ID:", id_customer);

        // ✅ RESET KANTIN YANG SUDAH DIPILIH
        $(".kantin-checkbox").prop("checked", false);
        updateKantinDropdownText();

        if (id_customer) {
          // ✅ LOAD KANTIN BERDASARKAN CUSTOMER
          loadKantinRadioOptions(id_customer, function() {
            console.log("[Customer Changed] Kantin loaded");
            // ✅ RENDER ULANG KONDIMEN TABLE
            if (typeof window.renderKondimenTable === "function")
              window.renderKondimenTable();
          });
        } else {
          // ✅ RESET KANTIN DROPDOWN
          $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');
          updateKantinDropdownText();

          // ✅ KOSONGKAN KONDIMEN TABLE
          $("#table-kondimen-menu thead").html('');
          $("#table-kondimen-menu tbody").html('');
        }
      });

      $(document).on("click", ".btn-edit-menu-harian", function() {
        var id = $(this).data("id");
        window.edit_menu_harian(id);
      });

      $(document).on("click", ".btn-delete-menu-harian", function() {
        var idsJson = $(this).data("ids");
        var ids = typeof idsJson === 'string' ? JSON.parse(idsJson) : idsJson;

        console.log("[Delete] IDs to delete:", ids);

        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Hapus Menu Harian?",
            html: `Data yang akan dihapus: <strong>${ids.length} record(s)</strong><br>Data tidak dapat dikembalikan!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            reverseButtons: true,
          }).then((result) => {
            if (result.isConfirmed) {
              deleteMultipleMenuHarian(ids);
            }
          });
        } else {
          if (confirm(`Yakin ingin menghapus ${ids.length} menu harian ini?`)) {
            deleteMultipleMenuHarian(ids);
          }
        }
      });

      function deleteMultipleMenuHarian(ids) {
        var completed = 0;
        var failed = 0;

        ids.forEach(function(id) {
          $.ajax({
            url: base_url + "/delete/" + id,
            type: "POST",
            dataType: "json",
            success: function(res) {
              completed++;
              if (completed + failed === ids.length) {
                if (failed === 0) {
                  showSuccess(`Berhasil menghapus ${completed} menu harian!`);
                  loadMenuHarianData();
                } else {
                  showError(`${completed} berhasil, ${failed} gagal dihapus.`);
                  loadMenuHarianData();
                }
              }
            },
            error: function() {
              failed++;
              if (completed + failed === ids.length) {
                showError(`${completed} berhasil, ${failed} gagal dihapus.`);
                loadMenuHarianData();
              }
            }
          });
        });
      }
    });

  })();
</script>