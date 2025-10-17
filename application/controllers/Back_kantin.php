<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Kantin extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Kantin');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Master Kantin';
    $data['customer_list'] = $this->db->get('customer')->result_array();
    $this->load->view('back/kantin/V_Kantin', $data);
  }

  public function get_data_kantin()
  {
    $data = $this->M_Kantin->getAllKantin();
    echo json_encode(['show_data' => $data]);
  }

  public function get_kantin_by_id()
  {
    $id = $this->input->post('id');
    $data = $this->M_Kantin->getKantinById($id);
    if ($data) {
      echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
  }

  public function save_data()
  {
    $this->form_validation->set_rules('nama_kantin', 'Nama Kantin', 'required|trim');
    $this->form_validation->set_rules('id_customer', 'Customer', 'required|numeric');
    $this->form_validation->set_rules('alamat_lokasi', 'Alamat Lokasi', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
      echo json_encode(['status' => 'error', 'message' => validation_errors()]);
      return;
    }

    $data = [
      'nama_kantin' => $this->input->post('nama_kantin'),
      'id_customer' => $this->input->post('id_customer'),
      'alamat'      => $this->input->post('alamat_lokasi'), // mapping ke field 'alamat' di DB
    ];

    if ($this->input->post('stat') == 'edit') {
      $id = $this->input->post('id');
      $update = $this->M_Kantin->updateKantin($id, $data);
      if ($update) {
        echo json_encode(['status' => 'success', 'message' => 'Data kantin berhasil diupdate']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update data']);
      }
    } else {
      $data['created_at'] = date('Y-m-d H:i:s');
      $insert = $this->M_Kantin->addKantin($data);
      if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Data kantin berhasil disimpan']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
      }
    }
  }

  public function delete_data()
  {
    $id = $this->input->post('id');
    $delete = $this->M_Kantin->deleteKantin($id);
    if ($delete) {
      echo json_encode(['status' => 'success', 'message' => 'Data kantin berhasil dihapus']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
  }
}
