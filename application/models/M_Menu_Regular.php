<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu_Regular extends CI_Model
{
  private $table_regular = 'regular_menu';
  private $table_relasi = 'regular_menu_komponen';
  private $table_menu = 'menu';

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // Ambil semua regular menu + komponen
  public function get_all_regular_menu_with_komponen()
  {
    try {
      // Query utama untuk regular menu
      $this->db->select('id, nama_menu_reg, harga, deskripsi');
      $this->db->from('regular_menu');
      $this->db->order_by('id', 'DESC');
      $query = $this->db->get();
      $regular_menus = $query->result();

      // Debug: Log query
      log_message('debug', 'Regular menu query: ' . $this->db->last_query());

      // Ambil komponen untuk setiap menu
      foreach ($regular_menus as &$menu) {
        $this->db->select('rmk.id_komponen, m.menu_nama, k.nama_kategori as kategori_nama');
        $this->db->from('regular_menu_komponen rmk');
        $this->db->join('menu m', 'rmk.id_komponen = m.id_komponen', 'left');
        $this->db->join('kategori_menu k', 'm.id_kategori_menu = k.id_kategori', 'left');
        $this->db->where('rmk.regular_menu_id', $menu->id);
        $komponen_query = $this->db->get();
        $menu->komponen = $komponen_query->result();

        // Debug: Pastikan harga ada
        if (!isset($menu->harga) || $menu->harga === null) {
          $menu->harga = 0;
          log_message('warning', 'Menu ' . $menu->nama_menu_reg . ' tidak memiliki harga, set ke 0');
        }
      }

      return $regular_menus;
    } catch (Exception $e) {
      log_message('error', 'Error in get_all_regular_menu_with_komponen: ' . $e->getMessage());
      return array();
    }
  }

  // Ambil satu regular menu + komponen
  public function get_regular_menu_by_id($id)
  {
    $this->db->select('rm.id, rm.nama_menu_reg, rm.harga, rmk.id_komponen');
    $this->db->from('regular_menu rm');
    $this->db->join('regular_menu_komponen rmk', 'rm.id = rmk.regular_menu_id', 'left');
    $this->db->where('rm.id', $id);
    return $this->db->get()->result_array();
  }

  // Insert regular menu
  public function insert_regular_menu($data)
  {
    $this->db->insert($this->table_regular, $data);
    return $this->db->insert_id();
  }

  // Insert relasi komponen
  public function insert_regular_menu_komponen($data)
  {
    return $this->db->insert($this->table_relasi, $data);
  }

  // Update regular menu
  public function update_regular_menu($id, $data)
  {
    $this->db->where('id', $id);
    return $this->db->update($this->table_regular, $data);
  }

  // Delete regular menu & relasi
  public function delete_regular_menu($id)
  {
    $this->db->delete($this->table_relasi, ['regular_menu_id' => $id]);
    return $this->db->delete($this->table_regular, ['id' => $id]);
  }

  public function delete_regular_menu_komponen($regular_menu_id)
  {
    $this->db->where('regular_menu_id', $regular_menu_id);
    return $this->db->delete($this->table_relasi);
  }

  public function get_komponen_with_kategori($regular_menu_id)
  {
    return $this->db
      ->select('menu.menu_nama, kategori_menu.nama_kategori as kategori_nama')
      ->from('regular_menu_komponen')
      ->join('menu', 'regular_menu_komponen.id_komponen = menu.id_komponen')
      ->join('kategori_menu', 'menu.id_kategori_menu = kategori_menu.id_kategori', 'left')
      ->where('regular_menu_komponen.regular_menu_id', $regular_menu_id)
      ->get()
      ->result(); // return array of object
  }

  public function get_by_id($id)
  {
    try {
      $this->db->where('id', $id);
      $query = $this->db->get($this->table_regular);
      return $query->row();
    } catch (Exception $e) {
      log_message('error', 'Error get_by_id: ' . $e->getMessage());
      return null;
    }
  }

  public function get_komponen_by_menu_id($id)
  {
    try {
      $this->db->select('a.id_komponen, b.menu_nama, c.nama_kategori as kategori_nama, b.harga_menu');
      $this->db->from('regular_menu_komponen a');
      $this->db->join('menu b', 'a.id_komponen = b.id_komponen', 'left');
      $this->db->join('kategori_menu c', 'b.id_kategori_menu = c.id_kategori', 'left');
      $this->db->where('a.regular_menu_id', $id);
      $this->db->group_by('a.id_komponen');
      $query = $this->db->get();
      log_message('debug', 'SQL: ' . $this->db->last_query());
      return $query->result();
    } catch (Exception $e) {
      log_message('error', 'Error get_komponen_by_menu_id: ' . $e->getMessage());
      return [];
    }
  }
}
