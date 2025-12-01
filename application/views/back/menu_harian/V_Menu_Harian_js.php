<script type="text/javascript">
  (function() {
    "use strict";

    if (typeof jQuery === "undefined") {
      return;
    }
    if (window.menuHarianInitialized) return;
    window.menuHarianInitialized = true;

    var base_url = $("#ajax_url").val();
    var kondimenList = [];
    var menuList = [];
    var isRendering = false;
    var renderTimeout = null;
    var dataTable = null;

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
      $("#kantin-checkbox-group").html(html);
      updateKantinDropdownText();
    }

    function updateKantinDropdownText() {
      var checkedCount = $('.kantin-checkbox:checked').length;
      var totalCount = $('.kantin-checkbox').length;
      var dropdown = $('#kantin-dropdown');
      var textSpan = $('#kantin-selected-count');

      if (!textSpan.length) return;

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

    function loadMenuHarianData() {
      if (dataTable !== null) {
        try {
          dataTable.destroy();
          dataTable = null;
        } catch (e) {
          dataTable = null;
        }
      }
      $("#menu-harian-table tbody").empty();
      $("#menu-harian-table tbody").html(buildLoadingRow());
      $("#menu-harian-table").show();

      $.ajax({
        url: base_url + "/ajax_list",
        type: "POST",
        dataType: "json",
        success: renderMenuHarianTable,
        error: function() {
          $("#menu-harian-table tbody").html(
            '<tr><td colspan="10" class="text-center text-danger">Gagal memuat data!</td></tr>'
          );
        },
      });
    }

    function renderMenuHarianTable(result) {
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
        result.data.forEach(function(item) {
          html += buildTableRow(item, no++);
        });
      } else {
        html = buildEmptyRow();
      }

      $("#menu-harian-table tbody").html(html);
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

      function getKategoriBadge(kategori) {
        if (!kategori) {
          return '<span class="badge badge-secondary" style="font-size: 0.6rem;">-</span>';
        }

        var badgeConfig = {
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

        return `<span class="badge ${config.class}" 
                      style="font-size: 0.6rem; padding: 2px 4px; background-color: ${config.color}; color: white; border-radius: 3px; white-space: nowrap;"
                      title="${kategori}">
                  ${kategori}
                </span>`;
      }

      function buildNestedKondimenTable(kondimenData, kantins) {
        if (!kondimenData || kondimenData.length === 0) {
          return '<span class="text-muted fst-italic" style="font-size: 0.75rem;">- Tidak ada kondimen -</span>';
        }

        var kantinCount = kantins.length;

        var nestedTable = `
          <div class="nested-kondimen-wrapper">
            <table class="table nested-kondimen-table" style="--kantin-count: ${kantinCount};">
              <thead>
                <tr>
                  <th class="col-number">#</th>
                  <th class="col-nama">Kondimen</th>
                  <th class="col-kategori">Kategori</th>`;

        kantins.forEach(function(kantin) {
          nestedTable += `<th class="col-qty-kantin" title="${kantin}">${kantin}</th>`;
        });

        nestedTable += `
                </tr>
              </thead>
              <tbody>`;

        kondimenData.forEach(function(k, idx) {
          var shortNama = k.nama && k.nama.length > 15 ? k.nama.substring(0, 15) + '..' : (k.nama || '-');
          var kategori = k.kategori || k.kategori_kondimen || k.nama_kategori || '-';

          nestedTable += `
                <tr>
                  <td class="text-center col-number">${idx + 1}</td>
                  <td class="col-nama" title="${k.nama || '-'}">${shortNama}</td>
                  <td class="text-center col-kategori">${getKategoriBadge(kategori)}</td>`;

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

      var kantinDisplay = item.kantins ? item.kantins.join(', ') : '-';
      var nestedKondimen = buildNestedKondimenTable(item.kondimen_data, item.kantins || []);
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
          <td class="text-center remark-cell">${item.remark ? item.remark : ''}</td>
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

    function initDataTable() {
      if (!$.fn.DataTable) return;

      try {
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
              previous: "Sebelumnya"
            },
          },
        });
      } catch (e) {
        dataTable = null;
      }
    }

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
      );
    }

    function loadKantinRadioOptions(id_customer, callback) {
      var url = id_customer ? base_url + "/get_kantin_by_customer" : base_url + "/get_kantins";
      var dataAjax = id_customer ? {
        id_customer: id_customer
      } : {};

      $.ajax({
        url: url,
        type: id_customer ? "POST" : "GET",
        data: dataAjax,
        dataType: "json",
        success: function(kantinList) {
          renderKantinRadio(kantinList);
          if (typeof callback === "function") callback(kantinList);
        }
      });
    }

    function loadMenuList(callback) {
      $.get(
        base_url + "/get_menu_list",
        function(data) {
          menuList = data || [];
          if (typeof callback === "function") callback();
        },
        "json"
      );
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

    window.tambah_menu_harian = function() {
      kondimenList = [];
      $("#form-menu-harian")[0].reset();
      $(".kantin-checkbox").prop("checked", false);
      $("#id_menu_harian").val("");
      $("#table-kondimen-menu tbody").empty();
      $("#table-kondimen-menu thead").empty();
      updateKantinDropdownText();
      $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');
      if (typeof window.renderKondimenTable === "function") window.renderKondimenTable();
      $("#form-modal-menu-harian").modal("show");
      $("#modalMenuHarianLabel").text("Tambah Menu Harian");
    };

    window.edit_menu_harian = function(id) {
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
            $("#remark").val(data.remark || "");

            // ✅ PARSE MULTIPLE KANTIN IDS
            var existingKantins = [];
            if (data.id_kantins && Array.isArray(data.id_kantins)) {
              existingKantins = data.id_kantins.map(function(k) {
                return k.toString();
              });
            } else if (data.id_kantin) {
              existingKantins = [data.id_kantin.toString()];
            }

            // ✅ PROCESS KONDIMEN DATA - PERBAIKAN UTAMA
            kondimenList = kondimen.map(function(k) {
              return {
                id_komponen: k.id_komponen,
                kategori: k.kategori_kondimen || k.nama_kategori || "", // ✅ PASTIKAN KATEGORI ADA
                qty_per_kantin: k.qty_per_kantin || {}
              };
            });

            // ✅ LOAD KANTIN OPTIONS BERDASARKAN CUSTOMER
            loadKantinRadioOptions(data.id_customer, function() {
              setTimeout(function() {
                // Set selected kantins
                existingKantins.forEach(function(kantinId) {
                  $("#kantin_" + kantinId).prop("checked", true);
                });

                updateKantinDropdownText();

                // ✅ RENDER KONDIMEN TABLE DENGAN DATA EXISTING
                if (typeof window.renderKondimenTable === "function") {
                  window.renderKondimenTable();
                }
              }, 300);

              $("#form-modal-menu-harian").modal("show");
              $("#modalMenuHarianLabel").text("Edit Menu Harian");
            });

          } else {
            showError(response.msg || "Data tidak ditemukan!");
          }
        },
        error: function(xhr, status, error) {
          showError("Gagal mengambil data!");
        },
      });
    };

    function delete_menu_harian(id) {
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
      if (renderTimeout) clearTimeout(renderTimeout);
      if (isRendering) return;
      renderTimeout = setTimeout(function() {
        actualRenderKondimenTable();
      }, 100);
    };

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

      kondimenList.forEach(function(kondimen, idx) {
        if (!kondimen.qty_per_kantin) {
          kondimen.qty_per_kantin = {};
        }
        selectedKantins.forEach(function(k) {
          if (kondimen.qty_per_kantin[k.id_kantin] === undefined) {
            kondimen.qty_per_kantin[k.id_kantin] = "";
          }
        });

        var options = '<option value="">-- Pilih Kondimen --</option>';
        menuList.forEach(function(menu) {
          var sel = menu.id_komponen == kondimen.id_komponen ? "selected" : "";
          options += `<option value="${menu.id_komponen}" ${sel}>${menu.menu_nama}</option>`;
        });

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
      }, 200);
    }

    $(document).ready(function() {
      loadMenuHarianData();
      loadCustomerOptions();
      loadKantinRadioOptions();
      loadMenuList();

      $(document).on('click', '#btn-close-modal-menu-harian', function() {
        $('#form-modal-menu-harian').modal('hide');
      });

      $(document).on('click', '[data-bs-dismiss="modal"]', function() {
        $(this).closest('.modal').modal('hide');
      });

      $(document).on('click', '#kantin-dropdown', function(e) {
        e.preventDefault();
        var $dropdownMenu = $(this).next('.dropdown-menu');
        if ($dropdownMenu.hasClass('show')) {
          $dropdownMenu.removeClass('show');
          $(this).attr('aria-expanded', 'false');
        } else {
          $('.dropdown-menu').removeClass('show');
          $('[data-bs-toggle="dropdown"]').attr('aria-expanded', 'false');
          $dropdownMenu.addClass('show');
          $(this).attr('aria-expanded', 'true');
        }
      });

      $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
          $('.dropdown-menu').removeClass('show');
          $('[data-bs-toggle="dropdown"]').attr('aria-expanded', 'false');
        }
      });

      $(document).on('click', '.dropdown-menu', function(e) {
        e.stopPropagation();
      });

      $(document).on('change', '.kantin-checkbox', function() {
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      $(document).on('click', '#selectAllKantin', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.kantin-checkbox').prop('checked', true);
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      $(document).on('click', '#deselectAllKantin', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.kantin-checkbox').prop('checked', false);
        updateKantinDropdownText();
        if (typeof window.renderKondimenTable === "function")
          window.renderKondimenTable();
      });

      $(document).on("change", "#id_customer", function() {
        var id_customer = $(this).val();
        $(".kantin-checkbox").prop("checked", false);
        updateKantinDropdownText();

        if (id_customer) {
          loadKantinRadioOptions(id_customer, function() {
            if (typeof window.renderKondimenTable === "function")
              window.renderKondimenTable();
          });
        } else {
          $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');
          updateKantinDropdownText();
          $("#table-kondimen-menu thead").html('');
          $("#table-kondimen-menu tbody").html('');
        }
      });

      $(document).on("click", "#btn-tambah-kondimen", function() {
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
              showError("Semua qty per kantin harus diisi untuk setiap kondimen!");
              return;
            }
          }
        }

        var formData = new FormData(this);
        formData.append("kondimen", JSON.stringify(kondimenList));

        $(this).find(":focus").blur();

        var $submitButton = $(this).find('button[type="submit"]');
        $submitButton.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

        $.ajax({
          url: base_url + "/save",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function(res) {
            $submitButton.prop("disabled", false).html('<i class="fas fa-save me-1"></i>Simpan');
            if (res.status === "success") {
              $("#form-modal-menu-harian").modal("hide");
              showSuccess(res.msg || "Menu harian berhasil disimpan!");
            } else {
              showError(res.msg || "Gagal menyimpan data!");
            }
          },
          error: function() {
            $submitButton.prop("disabled", false).html('<i class="fas fa-save me-1"></i>Simpan');
            showError("Terjadi kesalahan saat menyimpan data!");
          },
        });
      });

      $(document).on("hidden.bs.modal", "#form-modal-menu-harian", function() {
        // ✅ DELAY SEBENTAR LALU REFRESH DATA
        setTimeout(function() {
          loadMenuHarianData();
        }, 500);
      });

      $(document).on("hide.bs.modal", ".modal", function() {
        $(this).find(":focus").blur();
      });

      $(document).on("change", "#id_customer", function() {
        var id_customer = $(this).val();
        $(".kantin-checkbox").prop("checked", false);
        updateKantinDropdownText();

        if (id_customer) {
          loadKantinRadioOptions(id_customer, function() {
            if (typeof window.renderKondimenTable === "function")
              window.renderKondimenTable();
          });
        } else {
          $("#kantin-checkbox-group").html('<p class="text-muted mb-0">Pilih customer terlebih dahulu</p>');
          updateKantinDropdownText();
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
            success: function() {
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

      $('#vertical-menu-btn').on('click', function() {
        $('.vertical-menu').toggleClass('sidebar-collapsed');
        $('body').toggleClass('sidebar-collapsed');
      });
    });

  })();
</script>