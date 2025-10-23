<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu extends CI_Model
{
  public function get_all_menu()
  {
    $this->db->select('m.*, k.nama_kategori, t.thematik_nama');
    $this->db->from('menu m');
    $this->db->join('kategori_menu k', 'm.id_kategori = k.id_kategori', 'left');
    $this->db->join('thematik t', 'm.id_thematik = t.id_thematik', 'left');
    $this->db->where('m.status_aktif', 1);
    $this->db->order_by('m.id_komponen', 'DESC');
    
    // Debug query
    $query = $this->db->get_compiled_select();
    log_message('debug', 'Menu Query: ' . $query);
    
    // Reset and execute
    $this->db->select('m.*, k.nama_kategori, t.thematik_nama');
    $this->db->from('menu m');
    $this->db->join('kategori_menu k', 'm.id_kategori = k.id_kategori', 'left');
    $this->db->join('thematik t', 'm.id_thematik = t.id_thematik', 'left');
    $this->db->where('m.status_aktif', 1);
    $this->db->order_by('m.id_komponen', 'DESC');
    
    return $this->db->get()->result_array();
  }

  public function get_all_categories()
  {
    $this->db->select('id_kategori, nama_kategori');
    $this->db->from('kategori_menu');
    $this->db->order_by('nama_kategori', 'ASC');
    return $this->db->get()->result_array();
  }

  public function get_all_thematik()
  {
    $this->db->select('id_thematik, thematik_nama');
    $this->db->from('thematik');
    $this->db->where('active', 1);
    $this->db->order_by('urutan_tampil', 'ASC');
    $this->db->order_by('thematik_nama', 'ASC');
    return $this->db->get()->result_array();
  }

  public function get_all_countries()
  {
    // This method now returns thematik data for backward compatibility
    return $this->get_all_thematik();
  }

  public function get_menu_by_id($id)
  {
    $this->db->where('id_komponen', $id);
    return $this->db->get('menu')->row_array();
  }

  public function insert_menu($data)
  {
    return $this->db->insert('menu', $data);
  }

  public function update_menu($id, $data)
  {
    $this->db->where('id_komponen', $id);
    return $this->db->update('menu', $data);
  }

  public function delete_menu($id)
  {
    $this->db->where('id_komponen', $id);
    return $this->db->delete('menu');
  }

  public function check_menu_name($menu_nama, $exclude_id = null)
  {
    $this->db->where('menu_nama', $menu_nama);
    if ($exclude_id) {
      $this->db->where('id_komponen !=', $exclude_id);
    }
    $query = $this->db->get('menu');
    return $query->num_rows() > 0;
  }
}
