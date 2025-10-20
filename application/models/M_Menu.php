<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu extends CI_Model
{
  private $table = 'menu';
  private $table_kategori = 'kategori_menu';
  private $table_negara = 'country';

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Get all menu with joins
   */
  public function get_all_menu()
  {
    return $this->db
      ->select('menu.*, kategori_menu.nama_kategori as kategori_nama, country.country_nama as country_nama')
      ->join('kategori_menu', 'kategori_menu.id_kategori = menu.id_kategori_menu', 'left')
      ->join('country', 'country.id_country = menu.id_negara', 'left')
      ->get('menu')
      ->result();
  }

  /**
   * Get all menu with kategori for regular menu dropdown
   */
  public function get_all_menu_with_kategori()
  {
    return $this->db
      ->select('menu.*, kategori_menu.nama_kategori as kategori_nama')
      ->join('kategori_menu', 'kategori_menu.id_kategori = menu.id_kategori_menu', 'left')
      ->order_by('menu.menu_nama', 'ASC')
      ->get('menu')
      ->result();
  }

  /**
   * Get menu with kategori by ID
   */
  public function get_menu_with_kategori($id)
  {
    return $this->db
      ->select('menu.*, kategori_menu.nama_kategori as kategori_nama')
      ->join('kategori_menu', 'kategori_menu.id_kategori = menu.id_kategori_menu', 'left')
      ->where('menu.id_komponen', $id)
      ->get('menu')
      ->row();
  }

  /**
   * Get menu by ID
   */
  public function get_menu_by_id($id)
  {
    return $this->db->get_where('menu', ['id_komponen' => $id])->row_array();
  }

  /**
   * Insert new menu
   */
  public function insert_menu($data)
  {
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    return $this->db->insert($this->table, $data);
  }

  /**
   * Update menu
   */
  public function update_menu($id, $data)
  {
    $data['updated_at'] = date('Y-m-d H:i:s');

    $this->db->where('id_komponen', $id);
    return $this->db->update($this->table, $data);
  }

  /**
   * Soft delete menu
   */
  public function delete_menu($id)
  {
    return $this->db->delete('menu', ['id_komponen' => $id]);
  }

  /**
   * Get all categories for dropdown
   */
  public function get_all_categories()
  {
    $this->db->select('id_kategori, nama_kategori');
    $this->db->from($this->table_kategori);
    $this->db->order_by('nama_kategori', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Get all countries for dropdown
   */
  public function get_all_countries()
  {
    $this->db->select('id_country, country_nama');
    $this->db->from($this->table_negara);
    $this->db->order_by('country_nama', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Check if menu name exists
   */
  public function check_menu_name($name, $exclude_id = null)
  {
    $this->db->where('menu_nama', $name);
    $this->db->where('status_aktif', 1);

    if ($exclude_id) {
      $this->db->where('id_komponen !=', $exclude_id);
    }

    return $this->db->get($this->table)->num_rows() > 0;
  }

  /**
   * Get menu count by category
   */
  public function get_menu_count_by_category($category_id)
  {
    $this->db->where('id_kategori', $category_id);
    $this->db->where('status_aktif', 1);

    return $this->db->count_all_results($this->table);
  }

  /**
   * Upload image
   */
  public function upload_image($file_data)
  {
    $config['upload_path'] = './file/products/menu/';
    $config['allowed_types'] = 'gif|jpg|png|jpeg';
    $config['max_size'] = 2048; // 2MB
    $config['max_width'] = 1920;
    $config['max_height'] = 1080;
    $config['encrypt_name'] = TRUE;

    // Create directory if not exists
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0777, TRUE);
    }

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('menu_gambar')) {
      return $this->upload->data();
    } else {
      return false;
    }
  }

  /**
   * Get all bahan untuk dropdown
   */
  public function get_all_bahan()
  {
    $this->db->select('b.*, s.nama_satuan');
    $this->db->from('bahan b');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->order_by('b.nama_bahan', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Get bahan by menu id
   */
  public function get_bahan_by_menu($id_menu)
  {
    $this->db->select('mb.*, b.nama_bahan, s.nama_satuan, b.harga_sekarang');
    $this->db->from('menu_bahan mb');
    $this->db->join('bahan b', 'mb.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->where('mb.id_menu', $id_menu);
    return $this->db->get()->result();
  }

  /**
   * Save bahan for menu
   */
  public function save_menu_bahan($id_menu, $bahan_data)
  {
    // Hapus bahan lama
    $this->db->where('id_menu', $id_menu);
    $this->db->delete('menu_bahan');

    // Insert bahan baru
    if (!empty($bahan_data)) {
      foreach ($bahan_data as $bahan) {
        $data = array(
          'id_menu' => $id_menu,
          'id_bahan' => $bahan['id_bahan'],
          'qty' => isset($bahan['qty']) ? $bahan['qty'] : 0,
          'keterangan' => isset($bahan['keterangan']) ? $bahan['keterangan'] : ''
        );
        $this->db->insert('menu_bahan', $data);
      }
    }
    return true;
  }

  /**
   * Delete menu bahan
   */
  public function delete_menu_bahan($id_menu)
  {
    $this->db->where('id_menu', $id_menu);
    return $this->db->delete('menu_bahan');
  }

  /**
   * Get total harga bahan by menu
   */
  public function get_total_harga_bahan($id_menu)
  {
    $this->db->select('SUM(mb.qty * b.harga_sekarang) as total_harga');
    $this->db->from('menu_bahan mb');
    $this->db->join('bahan b', 'mb.id_bahan = b.id_bahan', 'left');
    $this->db->where('mb.id_menu', $id_menu);
    $result = $this->db->get()->row();
    return $result ? $result->total_harga : 0;
  }

  /**
   * Get bahan count by menu
   */
  public function get_bahan_count($id_menu)
  {
    $this->db->where('id_menu', $id_menu);
    return $this->db->count_all_results('menu_bahan');
  }
}
