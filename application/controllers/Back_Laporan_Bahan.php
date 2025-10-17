<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Back_Laporan_Bahan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Laporan_Bahan');
    $this->load->model('M_Food_Cost');
    $this->load->helper('url');
    $this->load->library('session');
  }

  public function index()
  {
    $data['title'] = 'Laporan Kebutuhan Bahan Baku';
    $data['menus'] = $this->M_Food_Cost->get_all_menu_list(); // Get list of menus for filter

    // Load bahan list for manual input form
    $this->load->model('M_Bahan');
    $data['bahan_list'] = $this->M_Bahan->get_bahan_with_satuan();

    $this->load->view('back/V_Laporan_Bahan', $data);
  }

  public function get_laporan()
  {
    $post = $this->input->post();

    $tanggal_mulai = isset($post['tanggal_mulai']) ? $post['tanggal_mulai'] : '';
    $tanggal_selesai = isset($post['tanggal_selesai']) ? $post['tanggal_selesai'] : '';
    $menu_ids = isset($post['menu_ids']) ? $post['menu_ids'] : [];
    $porsi_total = isset($post['porsi_total']) ? (int)$post['porsi_total'] : 1;

    // Validasi input
    if (empty($tanggal_mulai) || empty($tanggal_selesai)) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Tanggal mulai dan selesai harus diisi'
      ]);
      return;
    }

    // Get laporan data
    $laporan_data = $this->M_Laporan_Bahan->get_kebutuhan_bahan(
      $tanggal_mulai,
      $tanggal_selesai,
      $menu_ids,
      $porsi_total
    );

    echo json_encode([
      'status' => 'success',
      'data' => $laporan_data,
      'summary' => [
        'periode' => $tanggal_mulai . ' s/d ' . $tanggal_selesai,
        'total_menu' => count($menu_ids > 0 ? $menu_ids : $this->M_Food_Cost->get_all_menu_list()),
        'total_bahan' => count($laporan_data),
        'porsi' => $porsi_total
      ]
    ]);
  }

  public function export_excel()
  {
    $this->load->library('excel');

    $tanggal_mulai = $this->input->get('tanggal_mulai');
    $tanggal_selesai = $this->input->get('tanggal_selesai');
    $menu_ids = $this->input->get('menu_ids') ? explode(',', $this->input->get('menu_ids')) : [];
    $porsi_total = $this->input->get('porsi_total') ? (int)$this->input->get('porsi_total') : 1;

    // Get data
    $laporan_data = $this->M_Laporan_Bahan->get_kebutuhan_bahan(
      $tanggal_mulai,
      $tanggal_selesai,
      $menu_ids,
      $porsi_total
    );

    // Create Excel file
    $excel = new PHPExcel();
    $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();

    // Set header
    $sheet->setTitle('Laporan Kebutuhan Bahan Baku');
    $sheet->setCellValue('A1', 'LAPORAN KEBUTUHAN BAHAN BAKU');
    $sheet->setCellValue('A2', 'Periode: ' . $tanggal_mulai . ' s/d ' . $tanggal_selesai);
    $sheet->setCellValue('A3', 'Total Porsi: ' . $porsi_total);

    // Set table header
    $sheet->setCellValue('A5', 'No');
    $sheet->setCellValue('B5', 'Nama Bahan');
    $sheet->setCellValue('C5', 'Satuan');
    $sheet->setCellValue('D5', 'Kebutuhan Total');
    $sheet->setCellValue('E5', 'Harga Satuan');
    $sheet->setCellValue('F5', 'Total Biaya');

    // Fill data
    $row = 6;
    $no = 1;
    foreach ($laporan_data as $item) {
      $sheet->setCellValue('A' . $row, $no++);
      $sheet->setCellValue('B' . $row, $item['nama_bahan']);
      $sheet->setCellValue('C' . $row, $item['nama_satuan']);
      $sheet->setCellValue('D' . $row, $item['total_kebutuhan']);
      $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['harga_sekarang'], 0, ',', '.'));
      $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_biaya'], 0, ',', '.'));
      $row++;
    }

    // Set auto size
    foreach (range('A', 'F') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output file
    $filename = 'Laporan_Kebutuhan_Bahan_' . date('Y-m-d_H-i-s') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    $writer->save('php://output');
  }

  public function export_pdf()
  {
    $this->load->library('pdf');

    $tanggal_mulai = $this->input->get('tanggal_mulai');
    $tanggal_selesai = $this->input->get('tanggal_selesai');
    $menu_ids = $this->input->get('menu_ids') ? explode(',', $this->input->get('menu_ids')) : [];
    $porsi_total = $this->input->get('porsi_total') ? (int)$this->input->get('porsi_total') : 1;

    // Get data
    $laporan_data = $this->M_Laporan_Bahan->get_kebutuhan_bahan(
      $tanggal_mulai,
      $tanggal_selesai,
      $menu_ids,
      $porsi_total
    );

    $data = [
      'laporan_data' => $laporan_data,
      'periode' => $tanggal_mulai . ' s/d ' . $tanggal_selesai,
      'porsi_total' => $porsi_total,
      'tanggal_cetak' => date('d-m-Y H:i:s')
    ];

    $html = $this->load->view('back/V_Laporan_Bahan_PDF', $data, true);

    $this->pdf->createPDF($html, 'Laporan_Kebutuhan_Bahan_' . date('Y-m-d_H-i-s'), false);
  }

  // Method untuk mendapatkan ringkasan per menu
  public function get_summary_per_menu()
  {
    $post = $this->input->post();

    $tanggal_mulai = isset($post['tanggal_mulai']) ? $post['tanggal_mulai'] : '';
    $tanggal_selesai = isset($post['tanggal_selesai']) ? $post['tanggal_selesai'] : '';
    $menu_ids = isset($post['menu_ids']) ? $post['menu_ids'] : [];
    $porsi_total = isset($post['porsi_total']) ? (int)$post['porsi_total'] : 1;

    $summary_data = $this->M_Laporan_Bahan->get_summary_per_menu(
      $tanggal_mulai,
      $tanggal_selesai,
      $menu_ids,
      $porsi_total
    );

    echo json_encode([
      'status' => 'success',
      'data' => $summary_data
    ]);
  }

  /**
   * Save manual input bahan baku
   */
  public function save_manual_input()
  {
    $post = $this->input->post();

    $tanggal = isset($post['tanggal']) ? $post['tanggal'] : '';
    $shift = isset($post['shift']) ? $post['shift'] : '';
    $keterangan = isset($post['keterangan']) ? $post['keterangan'] : '';
    $bahan_data = isset($post['bahan_data']) ? $post['bahan_data'] : [];

    // Validasi input
    if (empty($tanggal) || empty($shift) || empty($bahan_data)) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap. Pastikan tanggal, shift, dan minimal satu bahan telah diisi.'
      ]);
      return;
    }

    // Load model untuk shift bahan
    $this->load->model('M_Shift_Bahan');

    // Prepare data for saving
    $header_data = [
      'tanggal_shift' => $tanggal,
      'shift_type' => $shift,
      'keterangan' => $keterangan,
      'status' => 'draft',
      'created_by' => $this->session->userdata('user_id') ?: 1,
      'created_at' => date('Y-m-d H:i:s')
    ];

    $detail_data = [];
    foreach ($bahan_data as $item) {
      $detail_data[] = [
        'id_bahan' => $item['id_bahan'],
        'jumlah_kebutuhan' => $item['jumlah'],
        'satuan' => $item['satuan'],
        'harga_per_unit' => $item['harga_per_unit'],
        'total_harga' => $item['jumlah'] * $item['harga_per_unit'],
        'id_divisi' => 1, // Default divisi
        'id_shift_kategori' => 1, // Default kategori
        'keterangan' => null
      ];
    }

    // Save data
    $result = $this->M_Shift_Bahan->save_shift_data($header_data, $detail_data);

    if ($result['status'] === 'success') {
      echo json_encode([
        'status' => 'success',
        'message' => 'Data bahan baku berhasil disimpan dengan ' . count($detail_data) . ' item.'
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => $result['message']
      ]);
    }
  }

  /**
   * Get template data
   */
  public function get_template()
  {
    $this->load->model('M_Shift_Bahan');

    $template_data = $this->M_Shift_Bahan->get_template_data();

    if (empty($template_data)) {
      echo json_encode([
        'status' => 'info',
        'message' => 'Template tidak tersedia',
        'data' => []
      ]);
    } else {
      echo json_encode([
        'status' => 'success',
        'message' => 'Template berhasil dimuat',
        'data' => $template_data
      ]);
    }
  }
}

/* End of file Back_Laporan_Bahan.php */
/* Location: ./application/controllers/Back_Laporan_Bahan.php */