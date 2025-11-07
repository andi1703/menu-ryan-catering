<script>
  $(document).ready(function() {
    // Inisialisasi DataTables
    $('#report-daily-menu-table').DataTable({
      responsive: true,
      paging: true,
      searching: true,
      ordering: true,
      lengthChange: true,
      pageLength: 25,
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        paginate: {
          previous: "Sebelumnya",
          next: "Berikutnya"
        }
      }
    });
  });
</script>