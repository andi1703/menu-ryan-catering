<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Laporan_Bahan extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Get kebutuhan bahan baku berdasarkan filter periode, menu, dan porsi
   */
  public function get_kebutuhan_bahan($tanggal_mulai = '', $tanggal_selesai = '', $menu_ids = [], $porsi_total = 1)
  {
    $this->db->select('
			b.id_bahan,
			b.nama_bahan,
			s.nama_satuan,
			SUM(mrb.jumlah_bahan * ' . $porsi_total . ') as total_kebutuhan,
			b.harga_sekarang,
			SUM(mrb.jumlah_bahan * ' . $porsi_total . ' * b.harga_sekarang) as total_biaya
		');

    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->join('menu_regular_food_cost mfc', 'mrb.id_menu = mfc.id_menu', 'left');

    // Filter berdasarkan periode jika ada
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    // Filter berdasarkan menu yang dipilih
    if (!empty($menu_ids) && is_array($menu_ids)) {
      $this->db->where_in('mrb.id_menu', $menu_ids);
    }

    $this->db->group_by('b.id_bahan, b.nama_bahan, s.nama_satuan, b.harga_sekarang');
    $this->db->order_by('b.nama_bahan', 'ASC');

    $query = $this->db->get();
    return $query->result_array();
  }

  /**
   * Get summary kebutuhan bahan per menu
   */
  public function get_summary_per_menu($tanggal_mulai = '', $tanggal_selesai = '', $menu_ids = [], $porsi_total = 1)
  {
    $this->db->select('
			mfc.id_menu,
			mfc.nama_menu,
			mfc.porsi,
			COUNT(mrb.id_bahan) as total_bahan,
			SUM(mrb.jumlah_bahan * b.harga_sekarang * ' . $porsi_total . ') as total_biaya_bahan
		');

    $this->db->from('menu_regular_food_cost mfc');
    $this->db->join('menu_regular_bahan mrb', 'mfc.id_menu = mrb.id_menu', 'left');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');

    // Filter berdasarkan periode jika ada
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    // Filter berdasarkan menu yang dipilih
    if (!empty($menu_ids) && is_array($menu_ids)) {
      $this->db->where_in('mfc.id_menu', $menu_ids);
    }

    $this->db->group_by('mfc.id_menu, mfc.nama_menu, mfc.porsi');
    $this->db->order_by('mfc.nama_menu', 'ASC');

    $query = $this->db->get();
    return $query->result_array();
  }

  /**
   * Get detail bahan per menu untuk drill-down
   */
  public function get_detail_bahan_per_menu($id_menu, $porsi_multiplier = 1)
  {
    $this->db->select('
			b.id_bahan,
			b.nama_bahan,
			s.nama_satuan,
			mrb.jumlah_bahan,
			(mrb.jumlah_bahan * ' . $porsi_multiplier . ') as kebutuhan_total,
			b.harga_sekarang,
			(mrb.jumlah_bahan * ' . $porsi_multiplier . ' * b.harga_sekarang) as total_biaya
		');

    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->where('mrb.id_menu', $id_menu);
    $this->db->order_by('b.nama_bahan', 'ASC');

    $query = $this->db->get();
    return $query->result_array();
  }

  /**
   * Get statistik kebutuhan bahan (top 10 bahan paling banyak digunakan)
   */
  public function get_top_bahan_usage($tanggal_mulai = '', $tanggal_selesai = '', $limit = 10)
  {
    $this->db->select('
			b.id_bahan,
			b.nama_bahan,
			s.nama_satuan,
			COUNT(DISTINCT mrb.id_menu) as jumlah_menu_menggunakan,
			SUM(mrb.jumlah_bahan) as total_penggunaan,
			b.harga_sekarang,
			SUM(mrb.jumlah_bahan * b.harga_sekarang) as total_nilai
		');

    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->join('menu_regular_food_cost mfc', 'mrb.id_menu = mfc.id_menu', 'left');

    // Filter berdasarkan periode jika ada
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    $this->db->group_by('b.id_bahan, b.nama_bahan, s.nama_satuan, b.harga_sekarang');
    $this->db->order_by('total_nilai', 'DESC');
    $this->db->limit($limit);

    $query = $this->db->get();
    return $query->result_array();
  }

  /**
   * Get ringkasan total untuk dashboard
   */
  public function get_summary_totals($tanggal_mulai = '', $tanggal_selesai = '', $menu_ids = [], $porsi_total = 1)
  {
    // Total unique bahan
    $this->db->select('COUNT(DISTINCT b.id_bahan) as total_unique_bahan');
    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->join('menu_regular_food_cost mfc', 'mrb.id_menu = mfc.id_menu', 'left');

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    if (!empty($menu_ids) && is_array($menu_ids)) {
      $this->db->where_in('mrb.id_menu', $menu_ids);
    }

    $total_bahan = $this->db->get()->row_array();

    // Total biaya keseluruhan
    $this->db->select('SUM(mrb.jumlah_bahan * b.harga_sekarang * ' . $porsi_total . ') as total_biaya_keseluruhan');
    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->join('menu_regular_food_cost mfc', 'mrb.id_menu = mfc.id_menu', 'left');

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    if (!empty($menu_ids) && is_array($menu_ids)) {
      $this->db->where_in('mrb.id_menu', $menu_ids);
    }

    $total_biaya = $this->db->get()->row_array();

    // Total menu
    $this->db->select('COUNT(DISTINCT mfc.id_menu) as total_menu');
    $this->db->from('menu_regular_food_cost mfc');
    $this->db->join('menu_regular_bahan mrb', 'mfc.id_menu = mrb.id_menu', 'left');

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
      $this->db->where('DATE(mfc.created_at) >=', $tanggal_mulai);
      $this->db->where('DATE(mfc.created_at) <=', $tanggal_selesai);
    }

    if (!empty($menu_ids) && is_array($menu_ids)) {
      $this->db->where_in('mfc.id_menu', $menu_ids);
    }

    $total_menu = $this->db->get()->row_array();

    return [
      'total_unique_bahan' => $total_bahan['total_unique_bahan'] ?? 0,
      'total_biaya_keseluruhan' => $total_biaya['total_biaya_keseluruhan'] ?? 0,
      'total_menu' => $total_menu['total_menu'] ?? 0,
      'porsi_total' => $porsi_total
    ];
  }

  /**
   * Get data untuk chart - bahan paling mahal
   */
  public function get_chart_bahan_termahal($limit = 5)
  {
    $this->db->select('
			b.nama_bahan,
			SUM(mrb.jumlah_bahan * b.harga_sekarang) as total_nilai
		');

    $this->db->from('menu_regular_bahan mrb');
    $this->db->join('bahan b', 'mrb.id_bahan = b.id_bahan', 'left');
    $this->db->group_by('b.id_bahan, b.nama_bahan');
    $this->db->order_by('total_nilai', 'DESC');
    $this->db->limit($limit);

    $query = $this->db->get();
    return $query->result_array();
  }
}

/* End of file M_Laporan_Bahan.php */
/* Location: ./application/models/M_Laporan_Bahan.php */