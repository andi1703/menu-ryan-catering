<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Menu_Harian extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu_Harian');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $filter = [
      'shift' => $this->input->get('shift'),
      'tanggal' => $this->input->get('tanggal'),
      'id_customer' => $this->input->get('id_customer')
    ];
    $data['menu_harian'] = $this->M_Menu_Harian->get_all($filter);
    $this->load->view('back/menu_harian/V_Menu_Harian', $data);
  }

  public function save()
  {
    $data = $this->input->post();
    $kondimen = json_decode($this->input->post('kondimen'), true);

    // Debug: log data yang diterima
    log_message('error', 'POST DATA: ' . print_r($data, true));
    log_message('error', 'KONDIMEN: ' . print_r($kondimen, true));

    // Validasi data
    if (!$data['tanggal'] || !$data['shift'] || !$data['id_customer'] || !$data['id_kantin'] || !$data['nama_menu'] || !$data['jenis_menu'] || !$data['total_menu_perkantin']) {
      echo json_encode(['status' => 'error', 'msg' => 'Data wajib diisi!']);
      return;
    }

    // Simpan menu_harian
    $menu_id = $this->M_Menu_Harian->insert($data);

    // Simpan kondimen
    if ($menu_id && !empty($kondimen)) {
      foreach ($kondimen as $row) {
        $this->M_Menu_Harian->insert_kondimen([
          'id_menu_harian' => $menu_id,
          'nama_kondimen' => $row['nama'],
          'qty_kondimen' => $row['qty']
        ]);
      }
    }

    echo json_encode(['status' => 'success']);
  }

  public function delete($id)
  {
    $this->M_Menu_Harian->delete($id);
    echo json_encode(['status' => 'success']);
  }

  public function get_customers()
  {
    $data = $this->db->get('customer')->result_array();
    echo json_encode($data);
  }

  public function get_kantins()
  {
    $data = $this->db->get('kantin')->result_array();
    echo json_encode($data);
  }

  public function get_menu_list()
  {
    $data = $this->db->select('id_komponen, menu_nama')->get('menu')->result_array();
    echo json_encode($data);
  }
}

// AJAX
function get_kantins()
{
  $data = $this->db->get('kantin')->result_array();
  echo json_encode($data);
}

// AJAX
function get_customers()
{
  $data = $this->db->get('customer')->result_array();
  echo json_encode($data);
}
