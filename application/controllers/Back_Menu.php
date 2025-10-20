<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Menu extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu');
    $this->load->model('M_KategoriMenu');
    $this->load->library('form_validation');
    $this->load->helper('url');
  }

  public function index()
  {
    $data['title'] = 'Menu Management';
    $data['kategori_list'] = $this->M_KategoriMenu->get_all_kategori_menu();
    $this->load->view('back/menu/V_Menu', $data);
  }

  public function tampil()
  {
    header('Content-Type: application/json');

    try {
      $data = $this->M_Menu->get_all_menu_with_kategori();

      echo json_encode([
        'status' => 'success',
        'data' => $data
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data: ' . $e->getMessage(),
        'data' => []
      ]);
    }
  }

  public function get_dropdown_data()
  {
    header('Content-Type: application/json');

    try {
      $kategori = $this->M_KategoriMenu->get_all_kategori_menu();

      echo json_encode([
        'status' => 'success',
        'kategori' => $kategori
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data dropdown: ' . $e->getMessage()
      ]);
    }
  }

  public function save_data()
  {
    header('Content-Type: application/json');

    try {
            // Validasi form
            $this->form_validation->set_rules('menu_nama', 'Nama Menu', 'required|trim');
            $this->form_validation->set_rules('id_kategori', 'Kategori Menu', 'required');
            $this->form_validation->set_rules('menu_harga', 'Harga Menu', 'numeric');      if ($this->form_validation->run() == FALSE) {
        echo json_encode([
          'status' => 'error',
          'message' => validation_errors()
        ]);
        return;
      }

      $stat = $this->input->post('stat');
      $id = $this->input->post('id_komponen');

      // Handle file upload
      $gambar_menu = '';
      if (!empty($_FILES['menu_gambar']['name'])) {
        $config['upload_path'] = './file/products/menu/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 2048; // 2MB
        $config['file_name'] = date('YmdHis') . '.' . pathinfo($_FILES['gambar_menu']['name'], PATHINFO_EXTENSION);

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('menu_gambar')) {
          $upload_data = $this->upload->data();
          $gambar_menu = $upload_data['file_name'];
        } else {
          echo json_encode([
            'status' => 'error',
            'message' => 'Upload gambar gagal: ' . $this->upload->display_errors()
          ]);
          return;
        }
      }

            $data_menu = [
                'menu_nama' => $this->input->post('menu_nama'),
                'id_kategori_menu' => $this->input->post('id_kategori'),
                'harga_menu' => $this->input->post('menu_harga') ?: 0,
                'menu_deskripsi' => $this->input->post('menu_deskripsi'),
                'status_aktif' => $this->input->post('status_aktif') ?: 1
            ];

            if ($gambar_menu) {
                $data_menu['menu_gambar'] = $gambar_menu;
            }

      if ($stat == 'edit' && $id) {
        // Mode Edit
        $this->M_Menu->update_menu($id, $data_menu);
        $message = 'Menu berhasil diupdate!';
      } else {
        // Mode Add
        $this->M_Menu->insert_menu($data_menu);
        $message = 'Menu berhasil ditambahkan!';
      }

      echo json_encode([
        'status' => 'success',
        'message' => $message
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function edit_data()
  {
    header('Content-Type: application/json');

    try {
      $id = $this->input->post('id');

      if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
      }

      $menu = $this->M_Menu->get_menu_by_id($id);

      if (!$menu) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        return;
      }

      echo json_encode([
        'status' => 'success',
        'data' => $menu
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function delete_data()
  {
    header('Content-Type: application/json');

    try {
      $id = $this->input->post('id');

      if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
      }

      // Get menu data untuk hapus gambar jika ada
      $menu = $this->M_Menu->get_menu_by_id($id);

      // Delete menu
      $this->M_Menu->delete_menu($id);

            // Hapus file gambar jika ada
            if ($menu && $menu->menu_gambar && file_exists('./file/products/menu/' . $menu->menu_gambar)) {
                unlink('./file/products/menu/' . $menu->menu_gambar);
            }      echo json_encode([
        'status' => 'success',
        'message' => 'Menu berhasil dihapus!'
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus data: ' . $e->getMessage()
      ]);
    }
  }
}
