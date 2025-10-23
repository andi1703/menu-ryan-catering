<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Add these lines at the top for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure CodeIgniter core is loaded
if (!class_exists('CI_Controller')) {
  require_once BASEPATH . 'core/Controller.php';
}

class Back_Menu extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu');
    $this->load->library('form_validation');
    $this->load->library('upload');
    $this->setCorsHeaders();
  }

  private function setCorsHeaders()
  {
    if (!headers_sent()) {
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      header('Access-Control-Max-Age: 3600');
      if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
      }
    }
  }

  public function index()
  {
    $data['title'] = 'Menu Management';
    // Pastikan path view benar (huruf kecil/kapital sesuai folder)
    $this->load->view('back/menu/V_Menu', $data);
  }

  public function tampil()
  {
    header('Content-Type: application/json');

    try {
      // Check database connection
      if (!$this->db->initialize()) {
        throw new Exception('Database connection failed');
      }

      $data = $this->M_Menu->get_all_menu();

      echo json_encode([
        'status' => 'success',
        'show_data' => $data,
        'count' => count($data)
      ]);
    } catch (Exception $e) {
      log_message('error', 'Back_Menu/tampil error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
      ]);
    }
  }

  public function get_dropdown_data()
  {
    header('Content-Type: application/json');

    try {
      // Check database connection
      if (!$this->db->initialize()) {
        throw new Exception('Database connection failed');
      }

      $categories = $this->M_Menu->get_all_categories();
      $thematik = $this->M_Menu->get_all_thematik(); // Changed from countries

      echo json_encode([
        'status' => 'success',
        'categories' => $categories,
        'thematik' => $thematik, // Changed from countries
        'categories_count' => count($categories),
        'thematik_count' => count($thematik) // Changed from countries_count
      ]);
    } catch (Exception $e) {
      log_message('error', 'Back_Menu/get_dropdown_data error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'line' => $e->getLine()
      ]);
    }
  }

  public function save_data()
  {
    header('Content-Type: application/json');
    $stat = $this->input->post('stat');
    $id = $this->input->post('id');
    $this->form_validation->set_rules('menu_nama', 'Nama Menu', 'required|trim');
    $this->form_validation->set_rules('id_kategori', 'Kategori', 'required|numeric');

    if ($this->form_validation->run() == FALSE) {
      echo json_encode([
        'status' => 'error',
        'message' => validation_errors()
      ]);
      return;
    }

    $menu_nama = $this->input->post('menu_nama');
    $exclude_id = ($stat == 'edit') ? $id : null;
    if ($this->M_Menu->check_menu_name($menu_nama, $exclude_id)) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Nama menu sudah digunakan!'
      ]);
      return;
    }

    $status_aktif = $this->input->post('status_aktif');
    $menu_harga = $this->input->post('menu_harga'); // Ambil harga dari form

    $data_menu = [
      'menu_nama'     => $menu_nama,
      'id_kategori'   => $this->input->post('id_kategori'),
      'id_thematik'   => $this->input->post('id_thematik'), // Changed from id_negara
      'menu_deskripsi' => $this->input->post('menu_deskripsi'),
      'menu_harga'    => $menu_harga, // Tambahkan ini
      'status_aktif'  => $status_aktif
    ];

    // Handle image upload
    if (!empty($_FILES['menu_gambar']['name'])) {
      $config['upload_path'] = './file/products/menu/';
      $config['allowed_types'] = 'jpg|png|gif|jpeg';
      $config['max_size']      = 2048;
      $config['file_name']     = date('YmdHis');
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('menu_gambar')) {
        $upload_data = $this->upload->data();
        $data_menu['menu_gambar'] = $upload_data['file_name'];
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Gagal upload gambar: ' . $this->upload->display_errors('', '')
        ]);
        return;
      }
    }

    // Save data
    if ($stat == 'new') {
      $result = $this->M_Menu->insert_menu($data_menu);
      $message = 'Menu berhasil ditambahkan!';
    } else {
      $result = $this->M_Menu->update_menu($id, $data_menu);
      $message = 'Menu berhasil diperbarui!';
    }

    if ($result) {
      echo json_encode([
        'status' => 'success',
        'message' => $message
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data!'
      ]);
    }
  }
  public function edit_data()
  {
    $id = $this->input->post('id');
    $menu = $this->M_Menu->get_menu_by_id($id);
    if ($menu) {
      echo json_encode(['status' => 'success', 'data' => $menu]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Data menu tidak ditemukan!']);
    }
  }

  public function delete_data()
  {
    header('Content-Type: application/json');
    $id = $this->input->post('id');
    $menu = $this->M_Menu->get_menu_by_id($id);

    // Hapus file gambar jika ada
    if ($menu && !empty($menu['menu_gambar'])) {
      $file_path = FCPATH . 'file/products/menu/' . $menu['menu_gambar'];
      if (file_exists($file_path)) {
        @unlink($file_path);
      }
    }

    $result = $this->M_Menu->delete_menu($id);
    if ($result) {
      echo json_encode(['status' => 'success', 'message' => 'Menu berhasil dihapus!']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus menu!']);
    }
  }

  public function toggle_status()
  {
    header('Content-Type: application/json');
    $id = $this->input->post('id');
    $status = $this->input->post('status');
    $data = ['status_aktif' => $status];
    $result = $this->M_Menu->update_menu($id, $data);
    if ($result) {
      $message = $status == 1 ? 'Menu berhasil diaktifkan!' : 'Menu berhasil dinonaktifkan!';
      echo json_encode([
        'status' => 'success',
        'message' => $message
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengubah status!'
      ]);
    }
  }

  // Debug methods
  public function debug_menu_thematik()
  {
    header('Content-Type: application/json');

    try {
      // Cek data menu dengan thematik
      $menus_with_thematik = $this->db->query("SELECT COUNT(*) as count FROM menu WHERE id_thematik IS NOT NULL AND id_thematik != 0")->row()->count;

      // Cek sample data
      $sample_data = $this->db->query("SELECT id_komponen, menu_nama, id_thematik FROM menu LIMIT 5")->result_array();

      // Cek data thematik
      $thematik_data = $this->db->query("SELECT * FROM thematik WHERE active = 1")->result_array();

      echo json_encode([
        'status' => 'success',
        'menus_with_thematik' => $menus_with_thematik,
        'sample_menu_data' => $sample_data,
        'thematik_data' => $thematik_data
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function debug_menu_query()
  {
    header('Content-Type: application/json');

    try {
      // Test raw query
      $sql = "SELECT m.*, k.nama_kategori, t.thematik_nama 
              FROM menu m 
              LEFT JOIN kategori_menu k ON m.id_kategori = k.id_kategori 
              LEFT JOIN thematik t ON m.id_thematik = t.id_thematik 
              WHERE m.status_aktif = 1 
              ORDER BY m.id_komponen DESC 
              LIMIT 5";

      $result = $this->db->query($sql)->result_array();

      echo json_encode([
        'status' => 'success',
        'sql' => $sql,
        'data' => $result,
        'count' => count($result)
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }
}
