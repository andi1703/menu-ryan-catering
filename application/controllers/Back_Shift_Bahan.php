<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Back_Shift_Bahan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Shift_Bahan');
    $this->load->model('M_Bahan');
    $this->load->helper('url');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Data Bahan Baku Per Shift';
    $data['shift_list'] = $this->M_Shift_Bahan->get_shift_list();

    // Load partial views dengan struktur yang benar
    $this->load->view('back_partial/title-meta', $data);
    $this->load->view('back_partial/head-css');
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    $this->load->view('back/shift_bahan/V_Shift_Bahan', $data);
    $this->load->view('back_partial/vendor-scripts');
    $this->load->view('back_partial/footer');
  }

  public function input_data($tanggal = null)
  {
    $tanggal = $tanggal ?: date('Y-m-d');

    $data['title'] = 'Input Data Bahan Baku - ' . date('d/m/Y', strtotime($tanggal));
    $data['tanggal_shift'] = $tanggal;
    $data['bahan_list'] = $this->M_Bahan->getAllBahan();
    $data['divisi_list'] = $this->M_Shift_Bahan->get_divisi_list();
    $data['kategori_list'] = $this->M_Shift_Bahan->get_kategori_list();
    $data['existing_data'] = $this->M_Shift_Bahan->get_data_by_tanggal($tanggal);

    // Load partial views
    $this->load->view('back_partial/title-meta', $data);
    $this->load->view('back_partial/head-css');
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    $this->load->view('back/shift_bahan/V_Input_Shift_Bahan', $data);
    $this->load->view('back_partial/vendor-scripts');
    $this->load->view('back_partial/footer');
  }

  public function save_data()
  {
    $this->form_validation->set_rules('tanggal_shift', 'Tanggal Shift', 'required', [
      'required' => 'Tanggal shift wajib diisi'
    ]);
    $this->form_validation->set_rules('shift_type', 'Tipe Shift', 'required', [
      'required' => 'Tipe shift wajib dipilih'
    ]);

    if ($this->form_validation->run() == FALSE) {
      echo json_encode(['status' => 'error', 'message' => validation_errors()]);
      return;
    }

    $tanggal_shift = $this->input->post('tanggal_shift');
    $shift_type = $this->input->post('shift_type');
    $bahan_data = $this->input->post('bahan_data'); // Array data bahan

    // Validasi data bahan
    if (empty($bahan_data) || !is_array($bahan_data)) {
      echo json_encode(['status' => 'error', 'message' => 'Data bahan tidak valid']);
      return;
    }

    $result = $this->M_Shift_Bahan->save_shift_data($tanggal_shift, $shift_type, $bahan_data);

    if ($result['status'] === 'success') {
      echo json_encode([
        'status' => 'success',
        'message' => 'Data bahan shift berhasil disimpan',
        'data' => $result['data']
      ]);
    } else {
      echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
  }

  public function get_data_by_tanggal()
  {
    $tanggal = $this->input->post('tanggal');
    $shift_type = $this->input->post('shift_type') ?: 'lunch';

    if (empty($tanggal)) {
      echo json_encode(['status' => 'error', 'message' => 'Tanggal tidak valid']);
      return;
    }

    $data = $this->M_Shift_Bahan->get_data_by_tanggal($tanggal, $shift_type);
    echo json_encode(['status' => 'success', 'data' => $data]);
  }

  public function load_template()
  {
    $template_data = $this->M_Shift_Bahan->get_template_data();
    echo json_encode(['status' => 'success', 'data' => $template_data]);
  }

  public function save_template()
  {
    $template_data = $this->input->post('template_data');

    if (empty($template_data) || !is_array($template_data)) {
      echo json_encode(['status' => 'error', 'message' => 'Data template tidak valid']);
      return;
    }

    $result = $this->M_Shift_Bahan->save_template_data($template_data);
    echo json_encode($result);
  }

  public function get_summary()
  {
    $tanggal_mulai = $this->input->post('tanggal_mulai');
    $tanggal_selesai = $this->input->post('tanggal_selesai');
    $divisi_filter = $this->input->post('divisi_filter');

    $summary = $this->M_Shift_Bahan->get_summary_data($tanggal_mulai, $tanggal_selesai, $divisi_filter);
    echo json_encode(['status' => 'success', 'data' => $summary]);
  }

  public function export_excel()
  {
    $tanggal = $this->input->get('tanggal');
    $shift_type = $this->input->get('shift_type') ?: 'lunch';

    if (empty($tanggal)) {
      show_error('Tanggal tidak valid');
      return;
    }

    $data = $this->M_Shift_Bahan->get_export_data($tanggal, $shift_type);

    // Load PhpSpreadsheet library
    require_once APPPATH . 'third_party/PhpSpreadsheet/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header
    $sheet->setTitle('Data Bahan Shift');
    $sheet->setCellValue('A1', 'LIST RINCIAN BAHAN BAKU (SHIFT ' . strtoupper($shift_type) . ')');
    $sheet->setCellValue('A2', date('l, F j, Y', strtotime($tanggal)));

    // Merge cells untuk header
    $sheet->mergeCells('A1:Z1');
    $sheet->mergeCells('A2:Z2');

    // Style header
    $sheet->getStyle('A1:A2')->getFont()->setBold(true);
    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Build table headers
    $col = 1; // Column A
    $row = 4;

    $sheet->setCellValueByColumnAndRow($col++, $row, 'NO');
    $sheet->setCellValueByColumnAndRow($col++, $row, 'BAHAN UTAMA');

    // Add divisi columns
    $divisi_list = $this->M_Shift_Bahan->get_divisi_list();
    foreach ($divisi_list as $divisi) {
      $sheet->setCellValueByColumnAndRow($col++, $row, $divisi['kode_divisi']);
    }

    $sheet->setCellValueByColumnAndRow($col++, $row, 'TOTAL');

    // Add data rows
    $row++;
    $no = 1;
    foreach ($data as $bahan) {
      $col = 1;
      $sheet->setCellValueByColumnAndRow($col++, $row, $no++);
      $sheet->setCellValueByColumnAndRow($col++, $row, $bahan['nama_bahan']);

      $total = 0;
      foreach ($divisi_list as $divisi) {
        $jumlah = isset($bahan['divisi'][$divisi['id_divisi']]) ? $bahan['divisi'][$divisi['id_divisi']] : 0;
        $sheet->setCellValueByColumnAndRow($col++, $row, $jumlah);
        $total += $jumlah;
      }

      $sheet->setCellValueByColumnAndRow($col++, $row, $total);
      $row++;
    }

    // Set auto width for columns
    foreach (range('A', $sheet->getHighestColumn()) as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output file
    $filename = 'Data_Bahan_Shift_' . date('Y-m-d', strtotime($tanggal)) . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
  }

  public function delete_data()
  {
    $id_header = $this->input->post('id_header');

    if (empty($id_header)) {
      echo json_encode(['status' => 'error', 'message' => 'ID header tidak valid']);
      return;
    }

    $result = $this->M_Shift_Bahan->delete_shift_data($id_header);
    echo json_encode($result);
  }

  public function approve_data()
  {
    $id_header = $this->input->post('id_header');
    $user_id = $this->session->userdata('user_id') ?: 1; // Default user ID

    if (empty($id_header)) {
      echo json_encode(['status' => 'error', 'message' => 'ID header tidak valid']);
      return;
    }

    $result = $this->M_Shift_Bahan->approve_shift_data($id_header, $user_id);
    echo json_encode($result);
  }

  public function get_bahan_usage_report()
  {
    $tanggal_mulai = $this->input->post('tanggal_mulai');
    $tanggal_selesai = $this->input->post('tanggal_selesai');

    $report = $this->M_Shift_Bahan->get_bahan_usage_report($tanggal_mulai, $tanggal_selesai);
    echo json_encode(['status' => 'success', 'data' => $report]);
  }

  public function load_template()
  {
    $template_data = $this->M_Shift_Bahan->get_template_data();

    if (empty($template_data)) {
      echo json_encode(['status' => 'info', 'message' => 'Template tidak tersedia', 'data' => []]);
    } else {
      echo json_encode(['status' => 'success', 'message' => 'Template berhasil dimuat', 'data' => $template_data]);
    }
  }
}

/* End of file Back_Shift_Bahan.php */
/* Location: ./application/controllers/Back_Shift_Bahan.php */