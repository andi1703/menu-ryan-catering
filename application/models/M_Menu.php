<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu extends CI_Model
{
  public function get_all_menu()
  {
    $this->db->select('m.*, k.nama_kategori, t.thematik_nama, GROUP_CONCAT(DISTINCT b.nama_bahan ORDER BY b.nama_bahan SEPARATOR "||") AS bahan_utama_nama');
    $this->db->from('menu m');
    $this->db->join('kategori_menu k', 'm.id_kategori = k.id_kategori', 'left');
    $this->db->join('thematik t', 'm.id_thematik = t.id_thematik', 'left');
    $this->db->join('menu_bahan_utama mb', 'mb.menu_id = m.id_komponen', 'left');
    $this->db->join('bahan b', 'mb.bahan_id = b.id_bahan', 'left');
    $this->db->where('m.status_aktif', 1);
    $this->db->group_by('m.id_komponen');
    $this->db->order_by('m.id_komponen', 'DESC');

    $query = $this->db->get();
    $result = $query->result_array();

    foreach ($result as &$row) {
      $row['bahan_utama'] = [];
      if (!empty($row['bahan_utama_nama'])) {
        $row['bahan_utama'] = array_filter(explode('||', $row['bahan_utama_nama']));
      }
      unset($row['bahan_utama_nama']);
    }

    return $result;
  }

  public function get_all_bahan()
  {
    $this->db->select('id_bahan, nama_bahan, harga_sekarang, status');
    $this->db->from('bahan');
    $this->db->where('status', 'aktif');
    $this->db->order_by('nama_bahan', 'ASC');
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
    $menu = $this->db->get_where('menu', ['id_komponen' => $id])->row_array();
    if ($menu) {
      $menu['bahan_utama_ids'] = $this->get_menu_bahan_ids($id);
    }
    return $menu;
  }

  public function insert_menu($data)
  {
    $this->db->insert('menu', $data);
    if ($this->db->affected_rows() > 0) {
      return (int)$this->db->insert_id();
    }
    return false;
  }

  public function update_menu($id, $data)
  {
    $this->db->where('id_komponen', $id);
    return $this->db->update('menu', $data);
  }

  public function delete_menu($id)
  {
    $this->db->where('menu_id', $id);
    $this->db->delete('menu_bahan_utama');
    $this->db->where('id_komponen', $id);
    return $this->db->delete('menu');
  }

  public function get_menu_bahan_ids($menu_id)
  {
    $this->db->select('bahan_id');
    $this->db->from('menu_bahan_utama');
    $this->db->where('menu_id', $menu_id);
    $this->db->order_by('created_at', 'ASC');
    $rows = $this->db->get()->result_array();
    return array_map(function ($row) {
      return (string)$row['bahan_id'];
    }, $rows);
  }

  public function sync_menu_bahan($menu_id, array $bahan_ids)
  {
    $uniqueIds = array_values(array_filter(array_unique(array_map('intval', $bahan_ids))));

    $this->db->trans_start();
    $this->db->where('menu_id', $menu_id);
    $this->db->delete('menu_bahan_utama');

    if (!empty($uniqueIds)) {
      $timestamp = date('Y-m-d H:i:s');
      $batchData = [];
      foreach ($uniqueIds as $id_bahan) {
        if ($id_bahan > 0) {
          $batchData[] = [
            'menu_id' => $menu_id,
            'bahan_id' => $id_bahan,
            'created_at' => $timestamp
          ];
        }
      }

      if (!empty($batchData)) {
        $this->db->insert_batch('menu_bahan_utama', $batchData);
      }
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
      log_message('error', 'Gagal menyimpan bahan utama untuk menu ID: ' . $menu_id);
      return false;
    }

    return true;
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
