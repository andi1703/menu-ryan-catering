<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu_Harian extends CI_Model
{
  private $table = 'menu_harian';
  private $table_kondimen = 'menu_harian_kondimen';

  public function get_all($filter = [])
  {
    $this->db->select('mh.*, c.nama_customer, k.nama_kantin');
    $this->db->from($this->table . ' mh');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');

    if (!empty($filter['tanggal'])) {
      $this->db->where('mh.tanggal', $filter['tanggal']);
    }
    if (!empty($filter['shift'])) {
      $this->db->where('mh.shift', $filter['shift']);
    }
    if (!empty($filter['id_customer'])) {
      $this->db->where('mh.id_customer', $filter['id_customer']);
    }

    $this->db->order_by('mh.tanggal', 'DESC');
    $this->db->order_by('mh.shift', 'ASC');

    return $this->db->get()->result_array();
  }

  public function count_all($filter = [])
  {
    $this->db->from($this->table);

    if (!empty($filter['tanggal'])) {
      $this->db->where('tanggal', $filter['tanggal']);
    }
    if (!empty($filter['shift'])) {
      $this->db->where('shift', $filter['shift']);
    }
    if (!empty($filter['id_customer'])) {
      $this->db->where('id_customer', $filter['id_customer']);
    }

    return $this->db->count_all_results();
  }

  public function get_all_kondimen($id_menu_harian)
  {
    $this->db->select('mhk.*, m.menu_nama as nama_kondimen, km.nama_kategori as kategori_kondimen');
    $this->db->from($this->table_kondimen . ' mhk');
    $this->db->join('menu m', 'mhk.id_komponen = m.id_komponen', 'left');
    $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori', 'left');
    $this->db->where('mhk.id_menu_harian', $id_menu_harian);
    $result = $this->db->get()->result_array();

    // ✅ FALLBACK JIKA JOIN GAGAL
    foreach ($result as &$row) {
      if (empty($row['nama_kondimen'])) {
        // Ambil langsung dari tabel menu
        $this->db->select('menu_nama, id_kategori');
        $this->db->where('id_komponen', $row['id_komponen']);
        $menu_data = $this->db->get('menu')->row_array();

        if ($menu_data) {
          $row['nama_kondimen'] = $menu_data['menu_nama'];

          // Ambil nama kategori
          if ($menu_data['id_kategori']) {
            $this->db->select('nama_kategori');
            $this->db->where('id_kategori', $menu_data['id_kategori']);
            $kategori_data = $this->db->get('kategori_menu')->row_array();
            $row['kategori_kondimen'] = $kategori_data ? $kategori_data['nama_kategori'] : '-';
          }
        }
      }
    }

    return $result;
  }

  public function get_by_id($id)
  {
    $this->db->select('mh.*, c.nama_customer, k.nama_kantin');
    $this->db->from($this->table . ' mh');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');
    $this->db->where('mh.id_menu_harian', $id);

    return $this->db->get()->row_array();
  }

  public function insert($data)
  {
    return $this->db->insert($this->table, $data);
  }

  public function insert_kondimen($data)
  {
    return $this->db->insert($this->table_kondimen, $data);
  }

  public function insert_kondimen_batch($data)
  {
    return $this->db->insert_batch($this->table_kondimen, $data);
  }

  public function update($id, $data)
  {
    $this->db->where('id_menu_harian', $id);
    return $this->db->update($this->table, $data);
  }

  // ✅ METHOD YANG HILANG - INI YANG MENYEBABKAN WARNING IDE
  public function delete($id)
  {
    // Hapus kondimen terlebih dahulu (foreign key constraint)
    $this->delete_kondimen($id);

    // Hapus menu harian
    $this->db->where('id_menu_harian', $id);
    return $this->db->delete($this->table);
  }

  // ✅ METHOD YANG HILANG - INI YANG MENYEBABKAN WARNING IDE
  public function delete_kondimen($id_menu_harian)
  {
    $this->db->where('id_menu_harian', $id_menu_harian);
    return $this->db->delete($this->table_kondimen);
  }

  // ✅ METHOD TAMBAHAN UNTUK DELETE BATCH
  public function delete_kondimen_batch($id_menu_harian_array)
  {
    if (!empty($id_menu_harian_array)) {
      $this->db->where_in('id_menu_harian', $id_menu_harian_array);
      return $this->db->delete($this->table_kondimen);
    }
    return false;
  }

  // ✅ METHOD TAMBAHAN UNTUK DELETE MENU HARIAN BATCH
  public function delete_batch($id_array)
  {
    if (!empty($id_array)) {
      // Hapus kondimen dulu
      $this->delete_kondimen_batch($id_array);

      // Hapus menu harian
      $this->db->where_in('id_menu_harian', $id_array);
      return $this->db->delete($this->table);
    }
    return false;
  }

  public function get_laporan_kondimen($filter = [])
  {
    $this->db->select('
        m.menu_nama AS nama_kondimen,
        km.nama_kategori AS kategori,
        mhk.qty_kondimen,
        k.nama_kantin,
        c.nama_customer,
        mh.shift,
        mh.tanggal
    ');
    $this->db->from('menu_harian_kondimen mhk');
    $this->db->join('menu_harian mh', 'mhk.id_menu_harian = mh.id_menu_harian', 'left');
    $this->db->join('menu m', 'mhk.id_komponen = m.id_komponen', 'left');
    $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer', 'left');

    if (!empty($filter['tanggal'])) {
      $this->db->where('mh.tanggal', $filter['tanggal']);
    }
    if (!empty($filter['id_customer'])) {
      $this->db->where('mh.id_customer', $filter['id_customer']);
    }
    // Tambah filter lain sesuai kebutuhan

    $query = $this->db->get();
    return $query->result_array();
  }
}
