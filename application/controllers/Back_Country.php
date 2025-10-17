<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Country extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Country');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Country Management';
    $this->load->view('back/country/V_Country', $data);
  }

  public function get_data_country()
  {
    $data = $this->M_Country->getAllCountry();
    echo json_encode($data);
  }

  public function get_data_country_by_id($id)
  {
    $data = $this->M_Country->getCountryById($id);
    echo json_encode($data);
  }

  public function tambah_data_country()
  {
    $this->form_validation->set_rules('country_nama', 'Nama Negara', 'required|trim');
    $this->form_validation->set_rules('country_deskripsi', 'Deskripsi', 'trim');

    if ($this->form_validation->run() == FALSE) {
      $response = array(
        'status' => false,
        'message' => validation_errors()
      );
    } else {
      // Cek apakah nama negara sudah ada
      if ($this->M_Country->checkCountryNameExists($this->input->post('country_nama'))) {
        $response = array(
          'status' => false,
          'message' => 'Nama negara sudah ada!'
        );
      } else {
        $data = array(
          'country_nama' => $this->input->post('country_nama'),
          'country_deskripsi' => $this->input->post('country_deskripsi'),
          'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->M_Country->addCountry($data)) {
          $response = array(
            'status' => true,
            'message' => 'Data negara berhasil ditambahkan!'
          );
        } else {
          $response = array(
            'status' => false,
            'message' => 'Gagal menambahkan data negara!'
          );
        }
      }
    }

    echo json_encode($response);
  }

  public function edit_data_country()
  {
    $this->form_validation->set_rules('country_nama', 'Nama Negara', 'required|trim');
    $this->form_validation->set_rules('country_deskripsi', 'Deskripsi', 'trim');

    if ($this->form_validation->run() == FALSE) {
      $response = array(
        'status' => false,
        'message' => validation_errors()
      );
    } else {
      $id = $this->input->post('id_country');

      // Cek apakah nama negara sudah ada (kecuali untuk data yang sedang diedit)
      if ($this->M_Country->checkCountryNameExists($this->input->post('country_nama'), $id)) {
        $response = array(
          'status' => false,
          'message' => 'Nama negara sudah ada!'
        );
      } else {
        $data = array(
          'country_nama' => $this->input->post('country_nama'),
          'country_deskripsi' => $this->input->post('country_deskripsi'),
          'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->M_Country->updateCountry($id, $data)) {
          $response = array(
            'status' => true,
            'message' => 'Data negara berhasil diupdate!'
          );
        } else {
          $response = array(
            'status' => false,
            'message' => 'Gagal mengupdate data negara!'
          );
        }
      }
    }

    echo json_encode($response);
  }

  public function hapus_data_country()
  {
    $id = $this->input->post('id_country');

    if ($this->M_Country->deleteCountry($id)) {
      $response = array(
        'status' => true,
        'message' => 'Data negara berhasil dihapus!'
      );
    } else {
      $response = array(
        'status' => false,
        'message' => 'Gagal menghapus data negara!'
      );
    }

    echo json_encode($response);
  }
}
