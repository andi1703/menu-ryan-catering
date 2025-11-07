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
    $this->load->helper('url');
  }

  public function index()
  {
    try {
      $data['bahan_list'] = $this->M_Bahan->get_all_bahan();
      $data['satuan_list'] = $this->M_Satuan->getAllSatuan();

      $this->load->view('back/bahan/V_Bahan', $data);
    } catch (Exception $e) {
      log_message('error', 'Bahan Index Error: ' . $e->getMessage());
      show_error('Terjadi kesalahan saat memuat halaman: ' . $e->getMessage());
    }
  }

  public function get_satuan_list()
  {
    try {
      header('Content-Type: application/json');

      $satuan_list = $this->M_Satuan->getAllSatuan();

      if ($satuan_list) {
        echo json_encode([
          'status' => 'success',
          'data' => $satuan_list
        ]);
      } else {
        echo json_encode([
          'status' => 'success',
          'data' => []
        ]);
      }
    } catch (Exception $e) {
      log_message('error', 'Get Satuan List Error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memuat data satuan: ' . $e->getMessage()
      ]);
    }
  }

  public function get_bahan_by_id()
  {
    try {
      header('Content-Type: application/json');

      $id = $this->input->post('id');

      if (empty($id) || !is_numeric($id)) {
        echo json_encode([
          'status' => 'error',
          'message' => 'ID bahan tidak valid'
        ]);
        return;
      }

      $data = $this->M_Bahan->get_bahan_by_id($id);

      if ($data) {
        echo json_encode([
          'status' => 'success',
          'data' => $data
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Data bahan tidak ditemukan'
        ]);
      }
    } catch (Exception $e) {
      log_message('error', 'Get Bahan By ID Error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function save_data()
  {
    try {
      header('Content-Type: application/json');

      $stat = $this->input->post('stat');
      $id = $this->input->post('id');
      $nama_bahan = trim($this->input->post('nama_bahan'));
      $id_satuan = $this->input->post('id_satuan');
      $harga_awal = $this->input->post('harga_awal');
      $harga_sekarang = $this->input->post('harga_sekarang');

      // Validasi input
      if (empty($nama_bahan) || empty($id_satuan) || empty($harga_awal) || empty($harga_sekarang)) {
        echo json_encode([
          'status' => 'error',
          'message' => 'Semua field wajib diisi'
        ]);
        return;
      }

      if ($stat === 'edit' && (empty($id) || !is_numeric($id))) {
        echo json_encode([
          'status' => 'error',
          'message' => 'ID bahan tidak valid untuk update'
        ]);
        return;
      }

      // Prepare data
      $data = [
        'nama_bahan' => $nama_bahan,
        'id_satuan' => intval($id_satuan),
        'harga_awal' => floatval($harga_awal),
        'harga_sekarang' => floatval($harga_sekarang)
      ];

      if ($stat === 'add') {
        // Check duplicate name
        if ($this->M_Bahan->check_bahan_exists($nama_bahan)) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Nama bahan sudah ada, gunakan nama yang lain'
          ]);
          return;
        }

        $result = $this->M_Bahan->insert_bahan($data);
        $message = 'Data bahan berhasil ditambahkan';
      } else if ($stat === 'edit') {
        // Check if data exists
        $existing = $this->M_Bahan->get_bahan_by_id($id);
        if (!$existing) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Data bahan tidak ditemukan'
          ]);
          return;
        }

        // Check duplicate name (exclude current record)
        if ($this->M_Bahan->check_bahan_exists($nama_bahan, $id)) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Nama bahan sudah ada, gunakan nama yang lain'
          ]);
          return;
        }

        $result = $this->M_Bahan->update_bahan($id, $data);
        $message = 'Data bahan berhasil diupdate';
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Status tidak valid'
        ]);
        return;
      }

      if ($result) {
        echo json_encode([
          'status' => 'success',
          'message' => $message
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Gagal menyimpan data ke database'
        ]);
      }
    } catch (Exception $e) {
      log_message('error', 'Save Bahan Error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }

  public function delete_data()
  {
    try {
      header('Content-Type: application/json');

      $id = $this->input->post('id');

      if (empty($id)) {
        echo json_encode([
          'status' => 'error',
          'message' => 'ID bahan tidak boleh kosong'
        ]);
        return;
      }

      if (!is_numeric($id) || intval($id) <= 0) {
        echo json_encode([
          'status' => 'error',
          'message' => 'ID bahan tidak valid'
        ]);
        return;
      }

      $id = intval($id);

      // Cek apakah bahan dengan ID tersebut exists
      $existing = $this->M_Bahan->get_bahan_by_id($id);
      if (!$existing) {
        echo json_encode([
          'status' => 'error',
          'message' => 'Bahan tidak ditemukan'
        ]);
        return;
      }

      $nama_bahan = $existing['nama_bahan'];

      // Hapus data
      $delete_result = $this->M_Bahan->delete_bahan($id);

      if ($delete_result) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Bahan "' . $nama_bahan . '" berhasil dihapus'
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Gagal menghapus bahan dari database'
        ]);
      }
    } catch (Exception $e) {
      log_message('error', 'Delete Bahan Error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }
}
