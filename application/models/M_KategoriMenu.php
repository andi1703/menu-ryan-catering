<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_KategoriMenu extends CI_Model
{
  var $table = 'kategori_menu';
  var $column_order = array(null, 'nama_kategori', 'deskripsi_kategori', 'active', null);
  var $column_search = array('nama_kategori', 'deskripsi_kategori');
  var $order = array('id_kategori' => 'asc'); // PERBAIKI: gunakan id_kategori

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // Fungsi untuk DataTables
  private function _get_datatables_query()
  {
    $this->db->from('kategori_menu');

    $i = 0;
    foreach ($this->column_search as $item) {
      if (isset($_POST['search']['value']) && $_POST['search']['value']) {
        if ($i === 0) {
          $this->db->group_start();
          $this->db->like($item, $_POST['search']['value']);
        } else {
          $this->db->or_like($item, $_POST['search']['value']);
        }

        if (count($this->column_search) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_POST['order'])) {
      $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if (isset($_POST['length']) && $_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);
    $query = $this->db->get();
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function count_all()
  {
    $this->db->from('kategori_menu');
    return $this->db->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->db->from($this->table);
    $this->db->where('id_kategori', $id); // PERBAIKI: gunakan id_kategori
    $query = $this->db->get();
    return $query->row();
  }

  public function tambah($data)
  {
    $this->db->insert($this->table, $data);
    return $this->db->insert_id();
  }

  public function update($where, $data)
  {
    $this->db->update($this->table, $data, $where);
    return $this->db->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->db->where('id_kategori', $id); // PERBAIKI: gunakan id_kategori
    $this->db->delete($this->table);
    return $this->db->affected_rows();
  }

  /**
   * Get all kategori (untuk dropdown)
   * Kompatibel dengan get_all_categories()
   */
  public function get_all()
  {
    $this->db->select('id_kategori, nama_kategori, deskripsi_kategori, active');
    $this->db->from($this->table);
    $this->db->where('active', 1); // Hanya ambil yang aktif
    $this->db->order_by('nama_kategori', 'ASC');
    return $this->db->get()->result();
  }

  public function get_all_categories()
  {
    return $this->db->order_by('nama_kategori', 'ASC')->get($this->table)->result_array();
  }

  /**
   * Get all kategori menu (alias untuk get_all)
   */
  public function get_all_kategori_menu()
  {
    $this->db->select('id_kategori, nama_kategori, deskripsi_kategori, active');
    $this->db->from($this->table);
    $this->db->order_by('nama_kategori', 'ASC');
    return $this->db->get()->result();
  }
}
