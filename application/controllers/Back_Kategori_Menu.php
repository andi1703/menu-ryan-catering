<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Kategori_Menu extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_KategoriMenu');
    $this->load->library('form_validation');
    // CORS headers
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $this->output->set_header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    $this->output->set_header('Access-Control-Allow-Headers: Content-Type, Authorization');
    // Preflight
    if ($this->input->method() === 'options') {
      $this->output->set_status_header(200);
      exit();
    }
  }

  public function index()
  {
    $data['data_kategori'] = $this->M_KategoriMenu->get_all_categories();
    $this->load->view('back/KategoriKomponenMenu/V_Kategori_Menu', $data);
  }

  public function tampil()
  {
    header('Content-Type: application/json');
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $query = $this->db->order_by('nama_kategori', 'ASC')->get('kategori_menu');
    echo json_encode([
      'status' => 'success',
      'show_data' => $query->result_array()
    ]);
  }

  public function save_data()
  {
    header('Content-Type: application/json');
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $stat = $this->input->post('stat');
    $id = $this->input->post('id');
    $nama_kategori = $this->input->post('nama_kategori');
    $deskripsi_kategori = $this->input->post('deskripsi_kategori');

    // Debug
    if (!$nama_kategori) {
      echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi!']);
      return;
    }

    $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode([
        'status' => 'error',
        'message' => validation_errors()
      ]);
      return;
    }
    $data_insert = [
      'nama_kategori' => $this->input->post('nama_kategori'),
      'deskripsi_kategori' => $this->input->post('deskripsi_kategori'),
      'urutan_tampil' => 0,
      'updated_at' => date('Y-m-d H:i:s')
    ];
    if ($stat == 'new') {
      $data_insert['created_at'] = date('Y-m-d H:i:s');
      $this->db->insert('kategori_menu', $data_insert);
      $msg = 'Data berhasil disimpan!';
    } else {
      $this->db->where('id_kategori', $id)->update('kategori_menu', $data_insert);
      $msg = 'Data berhasil diperbarui!';
    }
    echo json_encode(['status' => 'success', 'message' => $msg]);
  }

  public function edit_data()
  {
    header('Content-Type: application/json');
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $id = $this->input->post('id');
    $row = $this->db->get_where('kategori_menu', ['id_kategori' => $id])->row_array();
    if ($row) {
      echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan!']);
    }
  }

  public function delete_data()
  {
    header('Content-Type: application/json');
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $id = $this->input->post('id');
    if (!$id) {
      echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
      return;
    }
    $dipakai = $this->db->get_where('menu', ['id_kategori' => $id])->num_rows();
    if ($dipakai > 0) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Kategori tidak bisa dihapus karena masih dipakai di data menu.'
      ]);
      return;
    }
    $deleted = $this->db->where('id_kategori', $id)->delete('kategori_menu');
    if ($deleted) {
      echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } else {
      $error = $this->db->error();
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data', 'debug' => $error]);
    }
  }
}
