<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Customer extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Customer');
    $this->load->library('session');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Customer Management';
    $this->load->view('back/customer/V_Customer', $data);
  }

  public function get_data_customer()
  {
    $data = $this->M_Customer->getAllCustomer();
    echo json_encode(['show_data' => $data]);
  }


  public function get_customer_by_id()
  {
    $id = $this->input->post('id');
    $data = $this->M_Customer->getCustomerById($id);
    if ($data) {
      echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
  }

  public function save_data()
  {
    $this->form_validation->set_rules('nama_customer', 'Nama Customer', 'required|trim');
    $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
    $this->form_validation->set_rules('no_hp', 'No HP', 'required|trim');
    $this->form_validation->set_rules('email', 'Email', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
      echo json_encode(['status' => 'error', 'message' => validation_errors()]);
      return;
    }

    $customer_img = null;
    if (!empty($_FILES['customer_img']['name'])) {
      $config['upload_path']   = './file/customer/';
      $config['allowed_types'] = 'jpg|jpeg|png|gif';
      $config['max_size']      = 4096; // 4MB
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('customer_img')) {
        $upload_data = $this->upload->data();
        $customer_img = $upload_data['file_name'];
      } else {
        echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
        return;
      }
    }

    $data = [
      'nama_customer' => $this->input->post('nama_customer'),
      'alamat'        => $this->input->post('alamat'),
      'no_hp'         => $this->input->post('no_hp'),
      'email'         => $this->input->post('email'),
      'harga_makan'   => $this->input->post('harga_makan'),    // Tambahkan ini
      'food_cost_max' => $this->input->post('food_cost_max'),  // Tambahkan ini
    ];

    // Jika upload gambar, tambahkan ke data
    if ($customer_img) {
      $data['customer_img'] = $customer_img;
    }

    // Edit data
    if ($this->input->post('stat') == 'edit') {
      $id = $this->input->post('id');
      $update = $this->M_Customer->updateCustomer($id, $data);
      if ($update) {
        echo json_encode(['status' => 'success', 'message' => 'Data customer berhasil diupdate']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update data']);
      }
    } else {
      // Tambah data
      $data['created_at'] = date('Y-m-d H:i:s');
      $insert = $this->M_Customer->addCustomer($data);
      if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Data customer berhasil disimpan']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
      }
    }
  }

  public function delete_data()
  {
    $id = $this->input->post('id');
    $delete = $this->M_Customer->deleteCustomer($id);
    if ($delete) {
      echo json_encode(['status' => 'success', 'message' => 'Data customer berhasil dihapus']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
  }
}
