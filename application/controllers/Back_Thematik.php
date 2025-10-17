<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Thematik extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Thematik');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Thematic Management';
    $this->load->view('back/thematik/V_Thematik', $data);
  }

  public function get_data_thematik()
  {
    try {
      $data = $this->M_Thematik->getAllThematik();

      // Set proper header
      header('Content-Type: application/json');
      echo json_encode($data);
    } catch (Exception $e) {
      // Log error
      log_message('error', 'Error in get_data_thematik: ' . $e->getMessage());

      header('Content-Type: application/json');
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function get_data_thematik_by_id($id)
  {
    try {
      $data = $this->M_Thematik->getThematikById($id);

      header('Content-Type: application/json');
      echo json_encode($data);
    } catch (Exception $e) {
      log_message('error', 'Error in get_data_thematik_by_id: ' . $e->getMessage());

      header('Content-Type: application/json');
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function tambah_data_thematik()
  {
    $this->form_validation->set_rules('thematik_nama', 'Nama Thematik', 'required|trim');
    $this->form_validation->set_rules('thematik_deskripsi', 'Deskripsi', 'trim');

    if ($this->form_validation->run() == FALSE) {
      $response = array(
        'status' => false,
        'message' => validation_errors()
      );
    } else {
      // Cek apakah nama thematik sudah ada
      if ($this->M_Thematik->checkThematikNameExists($this->input->post('thematik_nama'))) {
        $response = array(
          'status' => false,
          'message' => 'Nama thematik sudah ada!'
        );
      } else {
        $data = array(
          'thematik_nama' => $this->input->post('thematik_nama'),
          'thematik_deskripsi' => $this->input->post('thematik_deskripsi'),
          'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->M_Thematik->addThematik($data)) {
          $response = array(
            'status' => true,
            'message' => 'Data thematik berhasil ditambahkan!'
          );
        } else {
          $response = array(
            'status' => false,
            'message' => 'Gagal menambahkan data thematik!'
          );
        }
      }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
  }

  public function edit_data_thematik()
  {
    $this->form_validation->set_rules('thematik_nama', 'Nama Thematik', 'required|trim');
    $this->form_validation->set_rules('thematik_deskripsi', 'Deskripsi', 'trim');

    if ($this->form_validation->run() == FALSE) {
      $response = array(
        'status' => false,
        'message' => validation_errors()
      );
    } else {
      $id = $this->input->post('id_thematik');

      // Cek apakah nama thematik sudah ada (kecuali untuk data yang sedang diedit)
      if ($this->M_Thematik->checkThematikNameExists($this->input->post('thematik_nama'), $id)) {
        $response = array(
          'status' => false,
          'message' => 'Nama thematik sudah ada!'
        );
      } else {
        $data = array(
          'thematik_nama' => $this->input->post('thematik_nama'),
          'thematik_deskripsi' => $this->input->post('thematik_deskripsi'),
          'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->M_Thematik->updateThematik($id, $data)) {
          $response = array(
            'status' => true,
            'message' => 'Data thematik berhasil diupdate!'
          );
        } else {
          $response = array(
            'status' => false,
            'message' => 'Gagal mengupdate data thematik!'
          );
        }
      }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
  }

  public function hapus_data_thematik()
  {
    try {
      $id = $this->input->post('id_thematik');

      if (empty($id)) {
        $response = array(
          'status' => false,
          'message' => 'ID thematik tidak valid!'
        );
      } else {
        // Cek apakah thematik digunakan di menu
        if ($this->M_Thematik->isUsedInMenu($id)) {
          $response = array(
            'status' => false,
            'message' => 'Thematik tidak dapat dihapus karena masih digunakan di menu!'
          );
        } else {
          if ($this->M_Thematik->deleteThematik($id)) {
            $response = array(
              'status' => true,
              'message' => 'Data thematik berhasil dihapus!'
            );
          } else {
            $response = array(
              'status' => false,
              'message' => 'Gagal menghapus data thematik!'
            );
          }
        }
      }
    } catch (Exception $e) {
      log_message('error', 'Error in hapus_data_thematik: ' . $e->getMessage());
      $response = array(
        'status' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
  }
}
