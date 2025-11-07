(function () {
	"use strict";

	if (typeof jQuery === "undefined") {
		// Informasi debugging jika jQuery belum dimuat
		if (window.console && typeof window.console.error === "function") {
			console.error(
				'menu_harian.js: jQuery tidak ditemukan. Pastikan <script src="assets_back/libs/jquery/jquery.min.js"> dimuat BEFORE menu_harian.js'
			);
		}
		return;
	}
	if (window.menuHarianInitialized) return;
	window.menuHarianInitialized = true;

	var base_url = $("#ajax_url").val();
	var kondimenList = []; // Array: [{id_komponen: '', qty: ''}]
	var menuList = [];

	// Lightweight notification helpers (ensure they exist for this page)
	if (typeof window.showSuccess !== "function") {
		window.showSuccess = function (message) {
			if (typeof Swal !== "undefined") {
				Swal.fire({
					icon: "success",
					title: message || "Sukses",
					timer: 1500,
					showConfirmButton: false,
				});
			} else if (typeof toastr !== "undefined") {
				toastr.success(message || "Sukses");
			} else {
				console.log("Success:", message);
				try {
					alert(message || "Sukses");
				} catch (e) {}
			}
		};
	}
	if (typeof window.showError !== "function") {
		window.showError = function (message) {
			if (typeof Swal !== "undefined") {
				Swal.fire({ icon: "error", title: message || "Error" });
			} else if (typeof toastr !== "undefined") {
				toastr.error(message || "Error");
			} else {
				console.error("Error:", message);
				try {
					alert(message || "Error");
				} catch (e) {}
			}
		};
	}

	// Small style tweaks to make Select2 fit inside table cells
	(function () {
		var css =
			"\n.select2-container{width:100% !important; box-sizing:border-box;}\n.select2-container .select2-selection--single{height:38px; padding:4px 8px;}\n.select2-container .select2-selection__rendered{white-space:normal;}\n#table-kondimen-menu td{vertical-align:middle;}\n.kondimen-select-wrap{width:100%;}\n.kondimen-select-wrap .select2-container{width:100% !important;}\n/* ensure dropdown appears above modal backdrop */\n.select2-container--open .select2-dropdown, .select2-dropdown{z-index:2100 !important;}\n";
		var style = document.createElement("style");
		style.type = "text/css";
		if (style.styleSheet) style.styleSheet.cssText = css;
		else style.appendChild(document.createTextNode(css));
		document.getElementsByTagName("head")[0].appendChild(style);
	})();

	// Render kantin as multi-checkbox (allows selecting multiple kantins)
	function renderKantinRadio(kantinList) {
		var html = "";
		if (Array.isArray(kantinList) && kantinList.length > 0) {
			kantinList.forEach(function (kantin) {
				// checkbox with data-nama so we can read the label later
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
		}
		$("#kantin-radio-group").html(html);

		// Bind change event to update kondimen table headers when selection changes
		$(".kantin-checkbox")
			.off("change")
			.on("change", function () {
				if (typeof window.renderKondimenTable === "function")
					window.renderKondimenTable();
			});
	}

	// Helper: get selected kantins as array of {id_kantin, nama_kantin}
	function getSelectedKantins() {
		var selected = [];
		$(".kantin-checkbox:checked").each(function () {
			var id = $(this).val();
			// try data-nama, fall back to label text by for attribute
			var nama = $(this).data("nama");
			if (!nama) {
				var lab = $('label[for="kantin_' + id + '"]');
				nama = lab && lab.length ? lab.text() : "";
			}
			selected.push({ id_kantin: id, nama_kantin: nama });
		});
		return selected;
	}

	$(document).ready(function () {
		// Muat data tabel saat halaman siap
		loadMenuHarianData();

		// Muat data customer dan radio kantin
		loadCustomerOptions();
		loadKantinRadioOptions();

		// Tambah menu harian
		window.tambah_menu_harian = function () {
			kondimenList = [];
			$("#form-menu-harian")[0].reset();
			// ensure kantin checkboxes are cleared
			$(".kantin-checkbox").prop("checked", false);
			$("#id_menu_harian").val(""); // Pastikan ID kosong
			if (typeof window.renderKondimenTable === "function")
				window.renderKondimenTable();
			var modal = new bootstrap.Modal(
				document.getElementById("form-modal-menu-harian")
			);
			modal.show();
			$("#modalMenuHarianLabel").text("Tambah Menu Harian");
		};

		// Reload data otomatis setelah modal tertutup
		$(document).on("hidden.bs.modal", "#form-modal-menu-harian", function () {
			// Tidak perlu reload di sini jika reload setelah sukses
			// loadMenuHarianData();
		});

		// Tambah kondimen
		$("#btn-tambah-kondimen").on("click", function () {
			if (menuList.length === 0) {
				loadMenuList(function () {
					if (typeof window.tambahKondimenRow === "function")
						window.tambahKondimenRow();
				});
			} else {
				if (typeof window.tambahKondimenRow === "function")
					window.tambahKondimenRow();
			}
		});

		// Submit form via AJAX
		$("#form-menu-harian").on("submit", function (e) {
			e.preventDefault();
			if (kondimenList.length === 0) {
				showError("Menu kondimen wajib diisi!");
				return;
			}
			// Cek setiap kondimen ada id_komponen dan qty untuk setiap selected kantin
			var selectedKantins = getSelectedKantins();
			for (var k of kondimenList) {
				if (!k.id_komponen) {
					showError("Semua kondimen harus dipilih!");
					return;
				}
				// ensure qty_per_kantin exists and has values for each selected kantin
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

			// Tampilkan loading
			var $submitButton = $(this).find('button[type="submit"]');
			$submitButton
				.prop("disabled", true)
				.html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

			$.ajax({
				url: base_url + "/save", // Tambah /
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (res) {
					if (res.status === "success") {
						$("#form-modal-menu-harian").modal("hide");
						showSuccess(res.msg || "Menu harian berhasil disimpan!");
						loadMenuHarianData(); // Muat ulang data tabel setelah sukses
					} else {
						showError(res.msg || "Gagal menyimpan data!");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error(
						"AJAX Error: ",
						textStatus,
						errorThrown,
						jqXHR && jqXHR.responseText
					);
					showError("Terjadi kesalahan saat menyimpan data! Cek console log.");
				},
				complete: function () {
					// Kembalikan tombol submit ke normal
					$submitButton
						.prop("disabled", false)
						.html('<i class="fas fa-save me-1"></i>Simpan');
				},
			});
		});

		// Edit menu harian
		$(document).on("click", ".btn-edit", function () {
			var id = $(this).data("id");
			window.edit_menu_harian(id); // Pastikan fungsi ini ada
		});

		window.edit_menu_harian = function (id) {
			$.get(
				base_url + "/get_by_id/" + id,
				function (response) {
					if (response.status === "success") {
						var data = response.data;
						var kondimen = response.kondimen || [];

						// Isi form modal
						$("#id_menu_harian").val(data.id_menu_harian);
						$("#tanggal").val(data.tanggal);
						$("#shift").val(data.shift);
						$("#jenis_menu").val(data.jenis_menu);
						$("#id_customer").val(data.id_customer);
						// nama_menu and total order
						$("#nama_menu").val(data.nama_menu);
						$("#total_orderan_perkantin").val(data.total_orderan_perkantin);

						// Isi kondimen (convert to qty_per_kantin if server returned single qty)
						kondimenList = kondimen.map(function (k) {
							return {
								id_komponen: k.id_komponen,
								kategori: k.kategori_kondimen,
								// if server provides qty_kondimen per kantin structure, adapt accordingly
								qty_per_kantin:
									k.qty_per_kantin ||
									(k.qty_kondimen ? { [data.id_kantin]: k.qty_kondimen } : {}),
							};
						});

						// Load kantin checkboxes for the customer and then pre-select existing kantins (if any) before rendering kondimen table
						var existingKantins = data.id_kantin || data.id_kantins || "";
						loadKantinRadioOptions(data.id_customer, function () {
							// normalize existingKantins into array
							var arr = [];
							if (Array.isArray(existingKantins)) arr = existingKantins;
							else if (
								typeof existingKantins === "string" &&
								existingKantins.indexOf(",") !== -1
							)
								arr = existingKantins.split(",").map(function (s) {
									return s.trim();
								});
							else if (existingKantins) arr = [existingKantins.toString()];

							// check the checkboxes
							arr.forEach(function (id) {
								$("#kantin_" + id).prop("checked", true);
							});

							// Now render kondimen table which will pick up selected kantins
							if (typeof window.renderKondimenTable === "function")
								window.renderKondimenTable();
							$("#form-modal-menu-harian").modal("show");
							$("#modalMenuHarianLabel").text("Edit Menu Harian");
						});
					} else {
						showError(response.msg || "Data tidak ditemukan!");
					}
				},
				"json"
			).fail(function () {
				showError("Gagal mengambil data!");
			});
		};

		// Delete menu harian
		$(document).on("click", ".btn-delete", function () {
			var id = $(this).data("id");
			window.delete_menu_harian(id); // Pastikan fungsi ini ada
		});

		window.delete_menu_harian = function (id) {
			if (typeof Swal !== "undefined") {
				Swal.fire({
					title: "Hapus Menu Harian?",
					text: "Data yang dihapus (termasuk kondimennya) tidak dapat dikembalikan!",
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
		};

		function ajaxDeleteMenuHarian(id) {
			$.ajax({
				url: base_url + "/delete/" + id, // Tambah /
				type: "POST",
				dataType: "json",
				success: function (res) {
					if (res.status === "success") {
						showSuccess(res.message || "Menu harian dihapus!");
						loadMenuHarianData(); // Muat ulang data
					} else {
						showError(res.message || "Gagal menghapus data");
					}
				},
				error: function () {
					showError("Gagal menghapus data!");
				},
			});
		}

		// Dropdown data
		function loadCustomerOptions() {
			$.get(
				base_url + "/get_customers",
				function (data) {
					var options = '<option value="">-- Pilih Customer --</option>';
					if (data && data.length > 0) {
						$.each(data, function (_, customer) {
							options += `<option value="${customer.id_customer}">${customer.nama_customer}</option>`;
						});
					}
					$("#id_customer").html(options);
				},
				"json"
			);
		}

		function loadKantinRadioOptions(id_customer, callback) {
			// Jika ada customer, filter kantin by customer
			var url = id_customer
				? base_url + "/get_kantin_by_customer"
				: base_url + "/get_kantins";
			var dataAjax = id_customer ? { id_customer: id_customer } : {};
			$.ajax({
				url: url,
				type: id_customer ? "POST" : "GET",
				data: dataAjax,
				dataType: "json",
				success: function (kantinList) {
					renderKantinRadio(kantinList);
					if (typeof callback === "function") callback(kantinList);
				},
			});
		}

		// Show data
		function loadMenuHarianData() {
			// Tampilkan loading di tabel
			if ($.fn.DataTable && $.fn.DataTable.isDataTable("#menu-harian-table")) {
				$("#menu-harian-table").DataTable().destroy();
			}
			$("#menu-harian-table tbody").html(buildLoadingRow());
			$("#menu-harian-table").show();

			$.ajax({
				url: base_url + "/ajax_list", // Tambah /
				type: "GET",
				dataType: "json",
				success: renderMenuHarianTable,
				error: function () {
					showError("Gagal memuat data menu harian!");
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
				result.show_data.forEach(function (item) {
					html += buildTableRow(item, no++);
				});
			} else {
				html = buildEmptyRow();
			}

			if ($.fn.DataTable && $.fn.DataTable.isDataTable("#menu-harian-table")) {
				$("#menu-harian-table").DataTable().destroy();
			}
			$("#menu-harian-table tbody").html(html);
			initDataTable();
		}

		function buildTableRow(item, no) {
			function getShiftBadge(shift) {
				var badgeClass = "";
				var badgeText = shift.charAt(0).toUpperCase() + shift.slice(1);
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
				return `<span class="${badgeClass}">${badgeText}</span>`;
			}

			function getKategoriBadge(kategori) {
				var badgeClass = "badge bg-secondary text-white";
				switch (kategori.toLowerCase()) {
					case "lauk utama":
						badgeClass = "badge bg-primary text-white";
						break;
					case "pendamping kering":
						badgeClass = "badge bg-warning text-dark";
						break;
					case "pendamping basah":
						badgeClass = "badge bg-info text-white";
						break;
					case "sayur":
						badgeClass = "badge bg-success text-white";
						break;
					case "buah":
						badgeClass = "badge bg-danger text-white";
						break;
					case "sambal":
						badgeClass = "badge bg-dark text-white";
						break;
					case "nasi":
						badgeClass = "badge bg-secondary text-white";
						break;
					case "sayuran berkuah":
						badgeClass = "badge bg-success text-white";
						break;
					case "tumisan":
						badgeClass = "badge bg-info text-white";
						break;
					default:
						badgeClass = "badge bg-danger text-white";
				}
				return `<span class="${badgeClass}">${kategori}</span>`;
			}

			var kondimen = item.kondimen ? item.kondimen : "-";
			if (kondimen !== "-") {
				var kondimenItems = kondimen.split(", ");
				kondimen = kondimenItems
					.map(function (str, idx) {
						var match = str.match(/^(.*?) \((.*?)\) \((.*?)\)$/);
						if (match) {
							var nama = match[1];
							var kategori = match[2];
							var qty = match[3];
							return `${idx + 1}. ${nama} ${getKategoriBadge(
								kategori
							)} (${qty})`;
						} else {
							return `${idx + 1}. ${str}`;
						}
					})
					.join("<br>");
			}
			return `
  <tr>
    <td class="text-center">${no}</td>
    <td>${item.tanggal}</td>
    <td class="text-center">${getShiftBadge(item.shift)}</td>
    <td>${item.nama_customer}</td>
    <td>${item.nama_kantin}</td>
    <td>${item.jenis_menu}</td>
    <td>${item.nama_menu}</td>
    <td>${kondimen}</td>  <!-- List dengan nomor -->
    <td class="text-end">${item.total_orderan_perkantin}</td>
    <td class="text-center">
      <div class="btn-group" role="group" aria-label="Actions">
    <button type="button" class="btn btn-warning btn-sm" onclick="edit_menu_harian(${
			item.id_menu_harian
		})" title="Edit">
      <i class="fas fa-edit"></i>
    </button>
    <button type="button" class="btn btn-danger btn-sm" onclick="delete_menu_harian(${
			item.id_menu_harian
		})" title="Hapus">
      <i class="fas fa-trash"></i>
    </button>
    </td>
  </tr>
`;
		}

		function buildLoadingRow() {
			return `
        <tr>
          <td class="text-center" colspan="9">
            <div class="text-muted p-3">
              <i class="fas fa-spinner fa-spin fa-2x mb-1"></i>
              <div>Memuat data...</div>
            </div>
          </td>
        </tr>
      `;
		}

		function buildEmptyRow() {
			return `
        <tr>
          <td class="text-center" colspan="9">
            <div class="text-muted p-3">
              <i class="fas fa-utensils fa-2x mb-1"></i>
              <div>Tidak ada data menu harian</div>
            </div>
          </td>
        </tr>
      `;
		}

		function initDataTable() {
			if ($.fn.DataTable) {
				// Hancurkan tabel lama jika ada
				if ($.fn.DataTable.isDataTable("#menu-harian-table")) {
					$("#menu-harian-table").DataTable().destroy();
				}
				// Inisialisasi DataTables baru
				$("#menu-harian-table").DataTable({
					responsive: true,
					autoWidth: false,
					order: [], // Biarkan sorting default
					language: {
						search: "Cari:",
						lengthMenu: "Tampilkan _MENU_ data",
						zeroRecords: "Tidak ada data yang ditemukan",
						info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
						infoEmpty: "Tidak ada data tersedia",
						infoFiltered: "(disaring dari _MAX_ total data)",
						paginate: {
							first: "Pertama",
							last: "Terakhir",
							next: "›",
							previous: "‹",
						},
					},
				});
			}
		}

		// Kondimen logic
		// Expose as globals so handlers bound earlier can call them
		window.tambahKondimenRow = function () {
			var selectedKantins = getSelectedKantins();
			var qtyMap = {};
			selectedKantins.forEach(function (k) {
				qtyMap[k.id_kantin] = "";
			});

			// Add new kondimen entry with qty_per_kantin
			kondimenList.push({
				id_komponen: "",
				kategori: "",
				qty_per_kantin: qtyMap,
			});
			if (typeof window.renderKondimenTable === "function")
				window.renderKondimenTable();
		};

		window.renderKondimenTable = function () {
			console.log(
				"[menu_harian] renderKondimenTable called, menuList:",
				menuList
			);

			if (menuList.length === 0) {
				console.log(
					"[menu_harian] renderKondimenTable: menuList empty, loading..."
				);
				loadMenuList(function () {
					renderKondimenTable();
				});
				return;
			}

			console.log(
				"[menu_harian] renderKondimenTable: rendering with",
				menuList.length,
				"menu items"
			);

			var tbody = "";
			kondimenList.forEach(function (kondimen, idx) {
				var options = '<option value="">-- Pilih Kondimen --</option>';
				menuList.forEach(function (menu) {
					var selected =
						menu.id_komponen == kondimen.id_komponen ? "selected" : "";
					options += `<option value="${menu.id_komponen}" ${selected}>${menu.menu_nama}</option>`;
				});
				tbody += `<tr>
      <td class="text-center">${idx + 1}</td>
      <td>
        <select class="form-control kondimen-nama" data-idx="${idx}">
          ${options}
        </select>
      </td>
      <td>
        <input type="text" class="form-control kondimen-kategori" data-idx="${idx}" value="${
					kondimen.kategori || ""
				}" readonly>
      </td>
      <td>
        <input type="number" class="form-control kondimen-qty" data-idx="${idx}" value="${
					kondimen.qty || ""
				}" required min="1">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="hapusKondimenRow(${idx})">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    </tr>`;
			});
			$("#table-kondimen-menu tbody").html(tbody);

			// Aktifkan Select2 agar dropdown searchable
			$(".kondimen-nama").select2({
				width: "100%",
				dropdownParent: $("#form-modal-menu-harian"),
			});

			// Event handler untuk dropdown kondimen
			$(".kondimen-nama")
				.off("change")
				.on("change", function () {
					var idx = $(this).data("idx");
					var id_komponen = $(this).val();

					console.log(
						"[menu_harian] Kondimen changed at index",
						idx,
						"id_komponen:",
						id_komponen
					);

					// Cek duplikat
					var isDuplicate = kondimenList.some(function (kondimen, i) {
						return (
							kondimen.id_komponen === id_komponen &&
							i !== idx &&
							id_komponen !== ""
						);
					});

					if (isDuplicate) {
						showError("Menu kondimen sudah ada di tabel!");
						$(this).val("").trigger("change");
						kondimenList[idx].id_komponen = "";
						kondimenList[idx].kategori = "";
						$('.kondimen-kategori[data-idx="' + idx + '"]').val("");
						return;
					}

					kondimenList[idx].id_komponen = id_komponen;

					// Ajax ambil kategori
					if (id_komponen) {
						console.log(
							"[menu_harian] Fetching kategori for id_komponen:",
							id_komponen
						);
						$.post(
							base_url + "/get_kategori_by_menu",
							{
								id_komponen: id_komponen,
							},
							function (data) {
								console.log("[menu_harian] Kategori response:", data);
								kondimenList[idx].kategori = data.nama_kategori;
								$('.kondimen-kategori[data-idx="' + idx + '"]').val(
									data.nama_kategori
								);
							},
							"json"
						).fail(function (xhr, status, error) {
							console.error("[menu_harian] Error fetching kategori:", error);
							showError("Gagal memuat kategori menu!");
						});
					} else {
						kondimenList[idx].kategori = "";
						$('.kondimen-kategori[data-idx="' + idx + '"]').val("");
					}
				});

			// Event handler untuk input qty
			$(".kondimen-qty")
				.off("input")
				.on("input", function () {
					var idx = $(this).data("idx");
					kondimenList[idx].qty = $(this).val();
				});

			// TRIGGER CHANGE untuk row yang sudah ada id_komponen (saat edit)
			kondimenList.forEach(function (kondimen, idx) {
				if (kondimen.id_komponen) {
					console.log(
						"[menu_harian] Triggering change for existing kondimen at index",
						idx
					);
					$('.kondimen-nama[data-idx="' + idx + '"]').trigger("change");
				}
			});
		};

		// Hapus kondimen row (global so onclick in rendered HTML can call it)
		window.hapusKondimenRow = function (idx) {
			if (typeof idx === "undefined" || idx === null) return;
			kondimenList.splice(idx, 1);
			if (typeof window.renderKondimenTable === "function")
				window.renderKondimenTable();
		};

		// Tambahkan fungsi load menu
		function loadMenuList(callback) {
			$.get(
				base_url + "/get_menu_list",
				function (data) {
					menuList = data;
					if (typeof callback === "function") callback();
				},
				"json"
			);
		}

		// Compute total order per kantin based on kondimen with kategori 'Lauk Utama'
		function computeTotalOrder() {
			var selected = getSelectedKantins();
			if (!selected || selected.length === 0) {
				$("#total_orderan_perkantin").val("");
				return;
			}
			// Calculate sums per kantin
			var sums = {};
			selected.forEach(function (k) {
				sums[k.id_kantin] = 0;
			});
			kondimenList.forEach(function (k) {
				if (!k) return;
				var kat = (k.kategori || "").toString().toLowerCase();
				if (kat.indexOf("lauk utama") === -1) return; // only count lauk utama
				var map = k.qty_per_kantin || {};
				selected.forEach(function (sk) {
					var q = parseFloat(map[sk.id_kantin]);
					if (!isFinite(q)) q = 0;
					sums[sk.id_kantin] = (sums[sk.id_kantin] || 0) + q;
				});
			});
			// Use the first selected kantin's total as the total_orderan_perkantin value
			var first = selected[0].id_kantin;
			var total = sums[first] || 0;
			// If integer-like, set as integer (no trailing .0)
			if (Math.abs(total - Math.round(total)) < 1e-6) total = Math.round(total);
			$("#total_orderan_perkantin").val(total);
			return sums;
		}

		// Load menu saat halaman siap
		loadMenuList();

		// Render radio kantin saat halaman siap
		loadKantinRadioOptions();

		// Render radio kantin saat customer berubah
		$("#id_customer").on("change", function () {
			var id_customer = $(this).val();
			loadKantinRadioOptions(id_customer);
		});
	});
})();
