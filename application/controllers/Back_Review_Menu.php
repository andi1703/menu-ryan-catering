<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Review_Menu extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu_Harian');
    $this->load->model('M_Customer');
    $this->load->model('M_Kantin');
  }

  public function index()
  {
    $data['title'] = 'Review Menu - Katalog Menu Harian';
    $data['customers'] = $this->M_Customer->getAllCustomer();

    $this->load->view('back/review_menu/V_Review_Menu', $data);
    $this->load->view('back/review_menu/V_Review_Menu_js');
  }

  public function get_menu_list()
  {
    try {
      // Ambil SEMUA menu_harian dari database
      $this->db->select('
                mh.id_menu_harian,
                mh.nama_menu,
                mh.foto_menu,
                mh.jenis_menu,
                mh.tanggal,
                mh.shift,
                c.nama_customer
            ');
      $this->db->from('menu_harian mh');
      $this->db->join('customer c', 'c.id_customer = mh.id_customer', 'left');

      // Filter by Customer
      if ($this->input->post('id_customer')) {
        $this->db->where('mh.id_customer', $this->input->post('id_customer'));
      }

      // Filter by Jenis Menu
      if ($this->input->post('jenis_menu')) {
        $this->db->where('mh.jenis_menu', $this->input->post('jenis_menu'));
      }

      // Search by nama menu
      if ($this->input->post('search')) {
        $this->db->like('mh.nama_menu', $this->input->post('search'));
      }

      // Sort by jenis_menu
      $this->db->order_by('mh.jenis_menu', 'ASC');
      $this->db->order_by('mh.nama_menu', 'ASC');

      $query = $this->db->get();
      $all_menus = $query->result_array();

      // Group berdasarkan signature (nama_menu + kondimen)
      $menu_signatures = [];

      foreach ($all_menus as $menu) {
        $kondimen = $this->get_kondimen_by_id($menu['id_menu_harian']);

        // Buat signature dari nama_menu + kondimen (sorted)
        $kondimen_signature = [];
        foreach ($kondimen as $k) {
          $kondimen_signature[] = ($k['nama_kondimen'] ?: '') . '|' . ($k['kategori_kondimen'] ?: '');
        }
        sort($kondimen_signature);
        $signature = $menu['nama_menu'] . '::' . implode(',', $kondimen_signature);

        // Jika signature belum ada, simpan sebagai menu utama
        if (!isset($menu_signatures[$signature])) {
          $menu_signatures[$signature] = [
            'nama_menu' => $menu['nama_menu'],
            'foto_menu' => $menu['foto_menu'],
            'jenis_menu' => $menu['jenis_menu'],
            'kondimen_list' => $kondimen,
            'customers' => [$menu['nama_customer']],
            'total_dibuat' => 1,
            'first_id' => $menu['id_menu_harian']
          ];
        } else {
          // Jika signature sudah ada, increment counter
          $menu_signatures[$signature]['total_dibuat']++;

          // Tambah customer ke list jika belum ada
          if (!in_array($menu['nama_customer'], $menu_signatures[$signature]['customers'])) {
            $menu_signatures[$signature]['customers'][] = $menu['nama_customer'];
          }

          // Update foto jika belum ada
          if (empty($menu_signatures[$signature]['foto_menu']) && !empty($menu['foto_menu'])) {
            $menu_signatures[$signature]['foto_menu'] = $menu['foto_menu'];
          }
        }
      }

      // Convert associative array ke indexed array dan format customers
      $result = [];
      foreach ($menu_signatures as $menu_data) {
        $menu_data['customers'] = implode(', ', array_unique($menu_data['customers']));
        $result[] = $menu_data;
      }

      echo json_encode([
        'status' => 'success',
        'data' => $result,
        'total' => count($result)
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  private function get_kondimen_by_id($id_menu_harian)
  {
    $this->db->select('
            m.menu_nama as nama_kondimen,
            km.nama_kategori as kategori_kondimen,
            mhk.qty_kondimen as qty
        ');
    $this->db->from('menu_harian_kondimen mhk');
    $this->db->join('menu m', 'm.id_komponen = mhk.id_komponen', 'left');
    $this->db->join('kategori_menu km', 'km.id_kategori = m.id_kategori', 'left');
    $this->db->where('mhk.id_menu_harian', $id_menu_harian);
    $this->db->order_by('km.nama_kategori', 'ASC');
    $this->db->order_by('m.menu_nama', 'ASC');

    $query = $this->db->get();
    return $query->result_array();
  }
}
