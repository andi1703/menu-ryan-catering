<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Menu_Harian extends CI_Model
{
  private $table = 'menu_harian';
  private $table_kondimen = 'menu_harian_kondimen';

  public function get_all($filter = [])
  {
    $this->db->select('mh.*, c.nama_customer, k.nama_kantin, 
      COALESCE(
        GROUP_CONCAT(
          CONCAT(
            mhk.nama_kondimen, 
            " (", mhk.kategori_kondimen, ") (", mhk.qty_kondimen, ")"
          ) ORDER BY mhk.id_kondimen ASC SEPARATOR ", "
        ), "-"
      ) as kondimen');
    $this->db->from('menu_harian mh');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');
    $this->db->join('menu_harian_kondimen mhk', 'mh.id_menu_harian = mhk.id_menu_harian', 'left');
    $this->db->group_by('mh.id_menu_harian');

    // ✅ FILTER DATA HANYA 1 MINGGU TERAKHIR
    $this->db->where('mh.tanggal >=', date('Y-m-d', strtotime('-7 days')));
    $this->db->where('mh.tanggal <=', date('Y-m-d'));

    // Filter lainnya
    if (!empty($filter['shift'])) $this->db->where('mh.shift', $filter['shift']);
    if (!empty($filter['tanggal'])) $this->db->where('mh.tanggal', $filter['tanggal']);
    if (!empty($filter['id_customer'])) $this->db->where('mh.id_customer', $filter['id_customer']);

    $this->db->order_by('mh.tanggal', 'DESC');

    // ✅ PAGINATION
    if (!empty($filter['limit'])) {
      $this->db->limit($filter['limit'], !empty($filter['offset']) ? $filter['offset'] : 0);
    }

    return $this->db->get()->result_array();
  }

  public function count_all($filter = [])
  {
    $this->db->select('COUNT(DISTINCT mh.id_menu_harian) as total');
    $this->db->from('menu_harian mh');

    // ✅ FILTER 1 MINGGU
    $this->db->where('mh.tanggal >=', date('Y-m-d', strtotime('-7 days')));
    $this->db->where('mh.tanggal <=', date('Y-m-d'));

    if (!empty($filter['shift'])) $this->db->where('mh.shift', $filter['shift']);
    if (!empty($filter['tanggal'])) $this->db->where('mh.tanggal', $filter['tanggal']);
    if (!empty($filter['id_customer'])) $this->db->where('mh.id_customer', $filter['id_customer']);

    $result = $this->db->get()->row_array();
    return isset($result['total']) ? (int)$result['total'] : 0;
  }

  public function get_all_kondimen($id_menu_harian)
  {
    return $this->db->get_where('menu_harian_kondimen', ['id_menu_harian' => $id_menu_harian])->result_array();
    $this->db->select('mh.*, c.nama_customer, k.nama_kantin, GROUP_CONCAT(CONCAT(mhk.nama_kondimen, " (", mhk.qty_kondimen, ")") SEPARATOR ", ") as kondimen');
    $this->db->from('menu_harian mh');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin');
    $this->db->join('menu_harian_kondimen mhk', 'mh.id_menu_harian = mhk.id_menu_harian', 'left');
    $this->db->group_by('mh.id_menu_harian');
    return $this->db->get()->result_array();
  }

  public function get_by_id($id)
  {
    return $this->db->get_where($this->table, ['id_menu_harian' => $id])->row_array();
  }

  public function insert($data)
  {
    // ✅ TAMBAHKAN pengecekan untuk menghindari error "Undefined index"
    if (!isset($data['total_orderan_perkantin'])) {
      $data['total_orderan_perkantin'] = 0;
    }

    $insert_data = [
      'tanggal' => $data['tanggal'],
      'shift' => $data['shift'],
      'jenis_menu' => $data['jenis_menu'],
      'id_customer' => $data['id_customer'],
      'id_kantin' => $data['id_kantin'],
      'nama_menu' => $data['nama_menu'],
      'total_orderan_perkantin' => $data['total_orderan_perkantin'],
      'created_at' => date('Y-m-d H:i:s')
    ];

    $this->db->insert($this->table, $insert_data);
    return $this->db->insert_id();
  }

  public function insert_kondimen($data)
  {
    $this->db->insert($this->table_kondimen, $data);
  }

  /**
   * Method baru untuk insert kondimen secara batch (lebih efisien)
   */
  public function insert_kondimen_batch($data)
  {
    if (!empty($data)) {
      return $this->db->insert_batch($this->table_kondimen, $data);
    }
    return false;
  }

  public function update($id, $data)
  {
    $this->db->where('id_menu_harian', $id);
    $this->db->update($this->table, $data);
  }

  public function delete($id)
  {
    // Hapus kondimen terlebih dahulu
    $this->db->delete($this->table_kondimen, ['id_menu_harian' => $id]);
    // Hapus menu utama
    return $this->db->delete($this->table, ['id_menu_harian' => $id]);
  }

  // Kondimen
  public function get_kondimen($id_menu_harian)
  {
    return $this->db->get_where($this->table_kondimen, ['id_menu_harian' => $id_menu_harian])->result_array();
  }
  public function delete_kondimen($id_menu_harian)
  {
    return $this->db->delete($this->table_kondimen, ['id_menu_harian' => $id_menu_harian]);
  }

  // model laporan kondimen

  public function get_laporan_kondimen($filter = [])
  {
    $this->db->select('
        mhk.nama_kondimen as menu_kondimen,
        mhk.kategori_kondimen as kategori,
        k.nama_kantin,
        SUM(mhk.qty_kondimen) as qty
    ');
    $this->db->from('menu_harian mh');
    $this->db->join('menu_harian_kondimen mhk', 'mh.id_menu_harian = mhk.id_menu_harian', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');
    // Filter
    if (!empty($filter['shift'])) {
      $this->db->where('mh.shift', $filter['shift']);
    }
    if (!empty($filter['tanggal'])) {
      $this->db->where('mh.tanggal', $filter['tanggal']);
    }
    if (!empty($filter['id_customer'])) {
      $this->db->where('mh.id_customer', $filter['id_customer']);
    }
    $this->db->group_by('mhk.nama_kondimen, mhk.kategori_kondimen, k.nama_kantin');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function get_report_group_by_customer($filter = [])
  {
    $this->db->select('
        c.id_customer,
        c.nama_customer,
        mhk.nama_kondimen as menu_kondimen,
        mhk.kategori_kondimen as kategori,
        k.nama_kantin,
        mh.shift,
        SUM(mhk.qty_kondimen) as total_orderan
    ');
    $this->db->from('menu_harian mh');
    $this->db->join('customer c', 'mh.id_customer = c.id_customer', 'left');
    $this->db->join('kantin k', 'mh.id_kantin = k.id_kantin', 'left');
    $this->db->join('menu_harian_kondimen mhk', 'mh.id_menu_harian = mhk.id_menu_harian', 'left');
    if (!empty($filter['tanggal'])) {
      $this->db->where('mh.tanggal', $filter['tanggal']);
    }
    if (!empty($filter['shift'])) {
      $this->db->where('mh.shift', $filter['shift']);
    }
    if (!empty($filter['id_kantin'])) {
      $this->db->where('mh.id_kantin', $filter['id_kantin']);
    }
    if (!empty($filter['id_customer'])) {
      $this->db->where('mh.id_customer', $filter['id_customer']);
    }
    $this->db->group_by('c.id_customer, mhk.nama_kondimen, mhk.kategori_kondimen, k.nama_kantin, mh.shift');
    $query = $this->db->get();
    $result = $query->result_array();

    $grouped = [];
    foreach ($result as $row) {
      $cid = $row['id_customer'];
      if (!isset($grouped[$cid])) {
        $grouped[$cid] = [
          'id_customer' => $row['id_customer'],
          'nama_customer' => $row['nama_customer'],
          'menus' => []
        ];
      }
      $grouped[$cid]['menus'][] = [
        'menu_kondimen' => $row['menu_kondimen'],
        'kategori' => $row['kategori'],
        'nama_kantin' => $row['nama_kantin'],
        'shift' => $row['shift'],
        'total_orderan' => $row['total_orderan']
      ];
    }
    return array_values($grouped);
  }

  public function get_kondimen_by_menu_harian($id_menu_harian)
  {
    $this->db->select('
      mhk.*,
      mk.menu_nama,
      kk.nama_kategori as kategori_kondimen
    ');
    $this->db->from($this->table_kondimen . ' mhk');
    $this->db->join('menu mk', 'mhk.id_komponen = mk.id_komponen', 'left');
    $this->db->join('kategori_menu kk', 'mk.id_kategori = kk.id_kategori', 'left');
    $this->db->where('mhk.id_menu_harian', $id_menu_harian);
    $this->db->order_by('mhk.id_kondimen', 'ASC');

    return $this->db->get()->result();
  }

  public function get_list($start_date = null, $end_date = null)
  {
    // Jika tidak ada parameter, gunakan minggu ini
    if (!$start_date || !$end_date) {
      $today = new DateTime();
      $dayOfWeek = $today->format('N');
      $monday = clone $today;
      $monday->modify('-' . ($dayOfWeek - 1) . ' days');
      $start_date = $monday->format('Y-m-d');
      $sunday = clone $monday;
      $sunday->modify('+6 days');
      $end_date = $sunday->format('Y-m-d');
    }

    $this->db->select('
      mh.id_menu_harian,
      mh.tanggal,
      mh.shift,
      mh.jenis_menu,
      mh.nama_menu,
      mh.total_orderan_perkantin,
      c.nama_customer,
      k.nama_kantin,
      mh.id_kantin
    ');
    $this->db->from($this->table . ' mh');
    $this->db->join('customer c', 'c.id_customer = mh.id_customer', 'left');
    $this->db->join('kantin k', 'k.id_kantin = mh.id_kantin', 'left');

    // ✅ FILTER TANGGAL SENIN - MINGGU MINGGU INI
    $this->db->where('mh.tanggal >=', $start_date);
    $this->db->where('mh.tanggal <=', $end_date);

    $this->db->order_by('mh.tanggal', 'DESC');
    $this->db->order_by('mh.shift', 'ASC');

    $query = $this->db->get();
    return $query->result();
  }
}
