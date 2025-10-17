<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Satuan extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Satuan');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Master Satuan';
    $data['satuan_list'] = $this->M_Satuan->getAllSatuan();
    $this->load->view('back/satuan/V_Satuan', $data);
  }

  public function get_data_satuan()
  {
    $data = $this->M_Satuan->getAllSatuan();
    echo json_encode(['show_data' => $data]);
  }

  public function get_satuan_by_id()
  {
    $id = $this->input->post('id');
    $data = $this->M_Satuan->getSatuanById($id);
    if ($data) {
      echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
  }

  public function save_data()
  {
    $this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
      echo json_encode(['status' => 'error', 'message' => validation_errors()]);
      return;
    }

    $data = [
      'nama_satuan' => $this->input->post('nama_satuan'),
    ];

    if ($this->input->post('stat') == 'edit') {
      $id = $this->input->post('id');
      $update = $this->M_Satuan->updateSatuan($id, $data);
      if ($update) {
        echo json_encode(['status' => 'success', 'message' => 'Data satuan berhasil diupdate']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update data']);
      }
    } else {
      $data['created_at'] = date('Y-m-d H:i:s');
      $insert = $this->M_Satuan->addSatuan($data);
      if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Data satuan berhasil disimpan']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
      }
    }
  }

  public function delete_data()
  {
    $id = $this->input->post('id');
    $delete = $this->M_Satuan->deleteSatuan($id);
    if ($delete) {
      echo json_encode(['status' => 'success', 'message' => 'Data satuan berhasil dihapus']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
  }
}
