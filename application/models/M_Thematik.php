<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Thematik extends CI_Model
{
  private $table = 'thematik';
  private $primary_key = 'id_thematik';

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Get all thematik (untuk dropdown dan list)
   */
  public function get_all()
  {
    $this->db->select('*');
    $this->db->from($this->table);
    $this->db->order_by('thematik_nama', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Get all thematik (alias untuk compatibility)
   */
  public function getAllThematik()
  {
    return $this->get_all();
  }

  /**
   * Get thematik by ID
   */
  public function get_by_id($id)
  {
    $this->db->where($this->primary_key, $id);
    return $this->db->get($this->table)->row();
  }

  /**
   * Get thematik by ID (alias untuk compatibility)
   */
  public function getThematikById($id)
  {
    return $this->get_by_id($id);
  }

  /**
   * Insert thematik
   */
  public function insert($data)
  {
    $this->db->insert($this->table, $data);
    return $this->db->insert_id();
  }

  /**
   * Add thematik (alias untuk compatibility)
   */
  public function addThematik($data)
  {
    return $this->insert($data);
  }

  /**
   * Update thematik
   */
  public function update($id, $data)
  {
    $this->db->where($this->primary_key, $id);
    return $this->db->update($this->table, $data);
  }

  /**
   * Update thematik (alias untuk compatibility)
   */
  public function updateThematik($id, $data)
  {
    return $this->update($id, $data);
  }

  /**
   * Delete thematik
   */
  public function delete($id)
  {
    $this->db->where($this->primary_key, $id);
    return $this->db->delete($this->table);
  }

  /**
   * Delete thematik (alias untuk compatibility)
   */
  public function deleteThematik($id)
  {
    return $this->delete($id);
  }

  /**
   * Check if thematik name exists
   * 
   * @param string $nama Nama thematik
   * @param int|null $exclude_id ID yang dikecualikan (untuk edit)
   * @return bool
   */
  public function checkThematikNameExists($nama, $exclude_id = null)
  {
    $this->db->where('thematik_nama', $nama);

    if ($exclude_id !== null) {
      $this->db->where($this->primary_key . ' !=', $exclude_id);
    }

    $query = $this->db->get($this->table);
    return $query->num_rows() > 0;
  }

  /**
   * Get thematik count
   */
  public function count_all()
  {
    return $this->db->count_all($this->table);
  }

  /**
   * Check if thematik is used in menu
   * 
   * @param int $id ID thematik
   * @return bool
   */
  public function isUsedInMenu($id)
  {
    try {
      // Cek apakah tabel menu exists
      if (!$this->db->table_exists('menu')) {
        return false;
      }

      $this->db->where('id_thematik', $id);
      $count = $this->db->count_all_results('menu');
      return $count > 0;
    } catch (Exception $e) {
      log_message('error', 'Error in isUsedInMenu: ' . $e->getMessage());
      return false; // Jika error, anggap tidak digunakan
    }
  }

  /**
   * Get thematik with menu count
   */
  public function getThematikWithMenuCount()
  {
    $this->db->select('thematik.*, COUNT(menu.id_menu) as menu_count');
    $this->db->from($this->table);
    $this->db->join('menu', 'menu.id_thematik = thematik.id_thematik', 'left');
    $this->db->group_by('thematik.id_thematik');
    $this->db->order_by('thematik.thematik_nama', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Get active thematik only
   */
  public function getActiveThematik()
  {
    $this->db->select('*');
    $this->db->from($this->table);
    $this->db->where('active', 1);
    $this->db->order_by('urutan_tampil', 'ASC');
    $this->db->order_by('thematik_nama', 'ASC');
    return $this->db->get()->result();
  }
}
