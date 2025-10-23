<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu_Harian extends CI_Model
{
  private $table = 'menu_harian';
  private $table_kondimen = 'menu_harian_kondimen';

  public function get_all($filter = [])
  {
    // Filter by shift, tanggal, customer jika ada
    if (!empty($filter['shift'])) $this->db->where('shift', $filter['shift']);
    if (!empty($filter['tanggal'])) $this->db->where('tanggal', $filter['tanggal']);
    if (!empty($filter['id_customer'])) $this->db->where('id_customer', $filter['id_customer']);
    return $this->db->get($this->table)->result();
  }

  public function get_by_id($id)
  {
    return $this->db->get_where($this->table, ['id_menu_harian' => $id])->row();
  }

  public function insert($data)
  {
    $this->db->insert('menu_harian', $data);
    return $this->db->insert_id();
  }

  public function insert_kondimen($data)
  {
    $this->db->insert('menu_harian_kondimen', $data);
  }

  public function update($id, $data)
  {
    $this->db->where('id_menu_harian', $id);
    return $this->db->update($this->table, $data);
  }

  public function delete($id)
  {
    $this->db->delete($this->table_kondimen, ['id_menu_harian' => $id]);
    return $this->db->delete($this->table, ['id_menu_harian' => $id]);
  }

  // Kondimen
  public function get_kondimen($id_menu_harian)
  {
    return $this->db->get_where($this->table_kondimen, ['id_menu_harian' => $id_menu_harian])->result();
  }

  public function delete_kondimen($id_menu_harian)
  {
    return $this->db->delete($this->table_kondimen, ['id_menu_harian' => $id_menu_harian]);
  }
}
