<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Food_Cost extends CI_Model
{
  private $table_menu_regular = 'menu_regular_food_cost';
  private $table_bahan = 'menu_regular_bahan';

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Get all menu regular with food cost calculation
   */
  public function get_all_menu_food_cost()
  {
    $this->db->select('*');
    $this->db->from($this->table_menu_regular);
    $this->db->order_by('created_at', 'DESC');
    $query = $this->db->get();
    $menus = $query->result();

    // Get bahan for each menu
    foreach ($menus as &$menu) {
      $menu->bahan_list = $this->get_bahan_by_menu_id($menu->id);
      $menu->total_food_cost = $this->calculate_food_cost($menu->id);
    }

    return $menus;
  }

  /**
   * Get menu by ID
   */
  public function get_menu_by_id($id)
  {
    $this->db->where('id', $id);
    $menu = $this->db->get($this->table_menu_regular)->row();

    if ($menu) {
      $menu->bahan_list = $this->get_bahan_by_menu_id($id);
      $menu->total_food_cost = $this->calculate_food_cost($id);
    }

    return $menu;
  }

  /**
   * Get bahan by menu ID
   */
  public function get_bahan_by_menu_id($menu_id)
  {
    $this->db->where('menu_id', $menu_id);
    $this->db->order_by('urutan', 'ASC');
    return $this->db->get($this->table_bahan)->result();
  }

  /**
   * Insert new menu regular
   */
  public function insert_menu($data)
  {
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $this->db->insert($this->table_menu_regular, $data);
    return $this->db->insert_id();
  }

  /**
   * Update menu regular
   */
  public function update_menu($id, $data)
  {
    $data['updated_at'] = date('Y-m-d H:i:s');

    $this->db->where('id', $id);
    return $this->db->update($this->table_menu_regular, $data);
  }

  /**
   * Insert bahan
   */
  public function insert_bahan($data)
  {
    return $this->db->insert($this->table_bahan, $data);
  }

  /**
   * Delete all bahan by menu ID
   */
  public function delete_bahan_by_menu_id($menu_id)
  {
    $this->db->where('menu_id', $menu_id);
    return $this->db->delete($this->table_bahan);
  }

  /**
   * Delete menu and its bahan
   */
  public function delete_menu($id)
  {
    // Delete bahan first
    $this->delete_bahan_by_menu_id($id);

    // Delete menu
    $this->db->where('id', $id);
    return $this->db->delete($this->table_menu_regular);
  }

  /**
   * Calculate food cost for a menu
   */
  public function calculate_food_cost($menu_id)
  {
    $bahan_list = $this->get_bahan_by_menu_id($menu_id);

    $total_bahan_mentah = 0;

    foreach ($bahan_list as $bahan) {
      // Harga bahan mentah (1 porsi) = (qty × harga_per_satuan) ÷ pembagian_porsi
      $harga_per_porsi = ($bahan->qty * $bahan->harga_per_satuan) / $bahan->pembagian_porsi;
      $total_bahan_mentah += $harga_per_porsi;
    }

    // Biaya produksi 20%
    $biaya_produksi = $total_bahan_mentah * 0.20;

    // Food cost = Total bahan mentah + Biaya produksi
    $food_cost = $total_bahan_mentah + $biaya_produksi;

    return [
      'total_bahan_mentah' => $total_bahan_mentah,
      'biaya_produksi' => $biaya_produksi,
      'food_cost' => $food_cost
    ];
  }

  /**
   * Get summary statistics
   */
  public function get_summary_stats()
  {
    $total_menu = $this->db->count_all($this->table_menu_regular);

    // Average food cost
    $menus = $this->get_all_menu_food_cost();
    $total_food_cost = 0;

    foreach ($menus as $menu) {
      $total_food_cost += $menu->total_food_cost['food_cost'];
    }

    $avg_food_cost = $total_menu > 0 ? $total_food_cost / $total_menu : 0;

    return [
      'total_menu' => $total_menu,
      'avg_food_cost' => $avg_food_cost,
      'total_food_cost' => $total_food_cost
    ];
  }

  /**
   * Get simple list of all menus (for dropdown/filter purposes)
   */
  public function get_all_menu_list()
  {
    $this->db->select('id as id_menu, nama_menu');
    $this->db->from($this->table_menu_regular);
    $this->db->order_by('nama_menu', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
  }
}
