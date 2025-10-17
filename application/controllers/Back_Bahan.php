<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Bahan extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Bahan');
    $this->load->model('M_Satuan');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Master Bahan';
    $data['bahan_list'] = $this->M_Bahan->getAllBahan();
    $data['satuan_list'] = $this->M_Satuan->getAllSatuan();
    $this->load->view('back/bahan/V_Bahan', $data);
  }

  public function get_data_bahan()
  {
    $data = $this->M_Bahan->getAllBahan();
    echo json_encode(['show_data' => $data]);
  }

  public function get_bahan_by_id()
  {
    $id = $this->input->post('id');
    $data = $this->M_Bahan->getBahanById($id);
    if ($data) {
      echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
  }

  public function save_data()
  {
    // Set validation rules dengan pesan bahasa Indonesia
    $this->form_validation->set_rules('nama_bahan', 'Nama Bahan', 'required|trim|min_length[2]|max_length[100]', [
      'required' => 'Nama bahan wajib diisi',
      'min_length' => 'Nama bahan minimal 2 karakter',
      'max_length' => 'Nama bahan maksimal 100 karakter'
    ]);
    $this->form_validation->set_rules('id_satuan', 'Satuan', 'required|numeric|greater_than[0]', [
      'required' => 'Satuan bahan wajib dipilih',
      'numeric' => 'Satuan harus berupa angka',
      'greater_than' => 'Satuan tidak valid'
    ]);
    $this->form_validation->set_rules('harga_awal', 'Harga Awal', 'required|numeric|greater_than_equal_to[0]', [
      'required' => 'Harga awal wajib diisi',
      'numeric' => 'Harga awal harus berupa angka',
      'greater_than_equal_to' => 'Harga awal tidak boleh negatif'
    ]);
    $this->form_validation->set_rules('harga_sekarang', 'Harga Sekarang', 'required|numeric|greater_than_equal_to[0]', [
      'required' => 'Harga sekarang wajib diisi',
      'numeric' => 'Harga sekarang harus berupa angka',
      'greater_than_equal_to' => 'Harga sekarang tidak boleh negatif'
    ]);
    $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[500]', [
      'max_length' => 'Keterangan maksimal 500 karakter'
    ]);

    if ($this->form_validation->run() == FALSE) {
      $errors = validation_errors();
      echo json_encode(['status' => 'error', 'message' => strip_tags($errors)]);
      return;
    }

    // Periksa apakah satuan yang dipilih valid
    $satuan_exists = $this->M_Satuan->getSatuanById($this->input->post('id_satuan'));
    if (!$satuan_exists) {
      echo json_encode(['status' => 'error', 'message' => 'Satuan yang dipilih tidak tersedia dalam sistem']);
      return;
    }

    // Periksa duplikasi nama bahan untuk operasi tambah
    if ($this->input->post('stat') != 'edit') {
      $duplicate_check = $this->M_Bahan->checkDuplicateName($this->input->post('nama_bahan'));
      if ($duplicate_check) {
        echo json_encode(['status' => 'error', 'message' => 'Nama bahan sudah terdaftar, silakan gunakan nama yang berbeda']);
        return;
      }
    }

    $data = [
      'nama_bahan' => ucwords(strtolower(trim($this->input->post('nama_bahan')))),
      'id_satuan' => $this->input->post('id_satuan'),
      'harga_awal' => $this->input->post('harga_awal'),
      'harga_sekarang' => $this->input->post('harga_sekarang'),
      'keterangan' => trim($this->input->post('keterangan')) ?: null,
    ];

    if ($this->input->post('stat') == 'edit') {
      $id = $this->input->post('id');

      // Periksa duplikasi nama jika mengubah nama bahan
      $existing_bahan = $this->M_Bahan->getBahanById($id);
      if ($existing_bahan && $existing_bahan['nama_bahan'] != $data['nama_bahan']) {
        $duplicate_check = $this->M_Bahan->checkDuplicateName($data['nama_bahan'], $id);
        if ($duplicate_check) {
          echo json_encode(['status' => 'error', 'message' => 'Nama bahan sudah terdaftar, silakan gunakan nama yang berbeda']);
          return;
        }
      }

      $data['updated_at'] = date('Y-m-d H:i:s');
      $update = $this->M_Bahan->updateBahan($id, $data);
      if ($update) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Data bahan "' . $data['nama_bahan'] . '" berhasil diupdate'
        ]);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data bahan']);
      }
    } else {
      $data['created_at'] = date('Y-m-d H:i:s');
      $insert = $this->M_Bahan->addBahan($data);
      if ($insert) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Bahan "' . $data['nama_bahan'] . '" berhasil ditambahkan'
        ]);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data bahan ke database']);
      }
    }
  }

  public function delete_data()
  {
    $id = $this->input->post('id');
    $delete = $this->M_Bahan->deleteBahan($id);
    if ($delete) {
      echo json_encode(['status' => 'success', 'message' => 'Data bahan berhasil dihapus']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
  }
}
