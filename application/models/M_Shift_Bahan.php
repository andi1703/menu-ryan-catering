<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Shift_Bahan extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Get list shift data
   */
  public function get_shift_list($limit = 10)
  {
    $this->db->select('h.*, COUNT(d.id_detail) as total_bahan_items');
    $this->db->from('shift_bahan_header h');
    $this->db->join('shift_bahan_detail d', 'h.id_header = d.id_header', 'left');
    $this->db->group_by('h.id_header');
    $this->db->order_by('h.tanggal_shift', 'DESC');
    $this->db->limit($limit);

    return $this->db->get()->result_array();
  }

  /**
   * Get divisi list
   */
  public function get_divisi_list()
  {
    $this->db->select('*');
    $this->db->from('divisi');
    $this->db->where('status', 'aktif');
    $this->db->order_by('kode_divisi', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Get kategori shift list
   */
  public function get_kategori_list()
  {
    $this->db->select('*');
    $this->db->from('shift_kategori');
    $this->db->where('status', 'aktif');
    $this->db->order_by('urutan', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Get data by tanggal
   */
  public function get_data_by_tanggal($tanggal, $shift_type = 'lunch')
  {
    // Get header
    $this->db->select('*');
    $this->db->from('shift_bahan_header');
    $this->db->where('tanggal_shift', $tanggal);
    $this->db->where('shift_type', $shift_type);
    $header = $this->db->get()->row_array();

    if (!$header) {
      return null;
    }

    // Get detail data dengan struktur yang sesuai untuk tabel
    $this->db->select('
			d.*,
			b.nama_bahan,
			b.id_bahan,
			s.nama_satuan,
			div.kode_divisi,
			div.nama_divisi,
			sk.kode_kategori,
			sk.nama_kategori
		');
    $this->db->from('shift_bahan_detail d');
    $this->db->join('bahan b', 'd.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->join('divisi div', 'd.id_divisi = div.id_divisi', 'left');
    $this->db->join('shift_kategori sk', 'd.id_shift_kategori = sk.id_shift_kategori', 'left');
    $this->db->where('d.id_header', $header['id_header']);
    $this->db->order_by('b.nama_bahan', 'ASC');
    $this->db->order_by('div.kode_divisi', 'ASC');
    $this->db->order_by('sk.urutan', 'ASC');

    $details = $this->db->get()->result_array();

    return [
      'header' => $header,
      'details' => $details
    ];
  }

  /**
   * Save shift data
   */
  public function save_shift_data($tanggal_shift, $shift_type, $bahan_data)
  {
    $this->db->trans_start();

    try {
      // Check existing header
      $this->db->select('id_header');
      $this->db->from('shift_bahan_header');
      $this->db->where('tanggal_shift', $tanggal_shift);
      $this->db->where('shift_type', $shift_type);
      $existing = $this->db->get()->row_array();

      if ($existing) {
        $id_header = $existing['id_header'];

        // Update header
        $this->db->where('id_header', $id_header);
        $this->db->update('shift_bahan_header', [
          'updated_at' => date('Y-m-d H:i:s'),
          'status_input' => 'draft'
        ]);

        // Delete existing details
        $this->db->where('id_header', $id_header);
        $this->db->delete('shift_bahan_detail');
      } else {
        // Insert new header
        $header_data = [
          'tanggal_shift' => $tanggal_shift,
          'shift_type' => $shift_type,
          'status_input' => 'draft',
          'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('shift_bahan_header', $header_data);
        $id_header = $this->db->insert_id();
      }

      // Insert detail data
      $total_items = 0;
      foreach ($bahan_data as $data) {
        if (isset($data['id_bahan']) && isset($data['id_divisi']) && isset($data['id_shift_kategori'])) {
          $jumlah = floatval($data['jumlah_kebutuhan'] ?? 0);

          if ($jumlah > 0) { // Only insert if quantity > 0
            $detail_data = [
              'id_header' => $id_header,
              'id_bahan' => $data['id_bahan'],
              'id_divisi' => $data['id_divisi'],
              'id_shift_kategori' => $data['id_shift_kategori'],
              'jumlah_kebutuhan' => $jumlah,
              'satuan' => $data['satuan'] ?? null,
              'keterangan' => $data['keterangan'] ?? null,
              'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('shift_bahan_detail', $detail_data);
            $total_items++;
          }
        }
      }

      // Update total bahan in header
      $this->db->where('id_header', $id_header);
      $this->db->update('shift_bahan_header', ['total_bahan' => $total_items]);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        return ['status' => 'error', 'message' => 'Gagal menyimpan data ke database'];
      }

      return [
        'status' => 'success',
        'message' => 'Data shift bahan berhasil disimpan',
        'data' => ['id_header' => $id_header, 'total_items' => $total_items]
      ];
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Get template data
   */
  public function get_template_data()
  {
    $this->db->select('
			t.*,
			b.nama_bahan,
			s.nama_satuan,
			div.kode_divisi,
			sk.kode_kategori
		');
    $this->db->from('shift_bahan_template t');
    $this->db->join('bahan b', 't.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->join('divisi div', 't.id_divisi = div.id_divisi', 'left');
    $this->db->join('shift_kategori sk', 't.id_shift_kategori = sk.id_shift_kategori', 'left');
    $this->db->where('t.is_active', 'yes');
    $this->db->order_by('b.nama_bahan', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Save template data
   */
  public function save_template_data($template_data)
  {
    $this->db->trans_start();

    try {
      // Clear existing template
      $this->db->truncate('shift_bahan_template');

      // Insert new template data
      foreach ($template_data as $data) {
        if (isset($data['id_bahan']) && isset($data['id_divisi']) && isset($data['id_shift_kategori'])) {
          $jumlah = floatval($data['jumlah_default'] ?? 0);

          if ($jumlah > 0) {
            $template_item = [
              'id_bahan' => $data['id_bahan'],
              'id_divisi' => $data['id_divisi'],
              'id_shift_kategori' => $data['id_shift_kategori'],
              'jumlah_default' => $jumlah,
              'is_active' => 'yes',
              'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('shift_bahan_template', $template_item);
          }
        }
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        return ['status' => 'error', 'message' => 'Gagal menyimpan template'];
      }

      return ['status' => 'success', 'message' => 'Template berhasil disimpan'];
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Get export data untuk Excel
   */
  public function get_export_data($tanggal, $shift_type = 'lunch')
  {
    $data = $this->get_data_by_tanggal($tanggal, $shift_type);

    if (!$data) {
      return [];
    }

    // Reorganize data untuk export
    $export_data = [];
    $bahan_totals = [];

    foreach ($data['details'] as $detail) {
      $id_bahan = $detail['id_bahan'];
      $id_divisi = $detail['id_divisi'];

      if (!isset($export_data[$id_bahan])) {
        $export_data[$id_bahan] = [
          'nama_bahan' => $detail['nama_bahan'],
          'satuan' => $detail['nama_satuan'],
          'divisi' => []
        ];
      }

      $export_data[$id_bahan]['divisi'][$id_divisi] = $detail['jumlah_kebutuhan'];

      // Calculate totals
      if (!isset($bahan_totals[$id_bahan])) {
        $bahan_totals[$id_bahan] = 0;
      }
      $bahan_totals[$id_bahan] += $detail['jumlah_kebutuhan'];
    }

    // Add totals to export data
    foreach ($export_data as $id_bahan => &$bahan) {
      $bahan['total'] = $bahan_totals[$id_bahan];
    }

    return $export_data;
  }

  /**
   * Delete shift data
   */
  public function delete_shift_data($id_header)
  {
    $this->db->trans_start();

    // Check if data exists
    $this->db->select('tanggal_shift, shift_type');
    $this->db->from('shift_bahan_header');
    $this->db->where('id_header', $id_header);
    $header = $this->db->get()->row_array();

    if (!$header) {
      return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
    }

    // Delete details first
    $this->db->where('id_header', $id_header);
    $this->db->delete('shift_bahan_detail');

    // Delete header
    $this->db->where('id_header', $id_header);
    $this->db->delete('shift_bahan_header');

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return ['status' => 'error', 'message' => 'Gagal menghapus data'];
    }

    return [
      'status' => 'success',
      'message' => 'Data shift tanggal ' . date('d/m/Y', strtotime($header['tanggal_shift'])) . ' berhasil dihapus'
    ];
  }

  /**
   * Approve shift data
   */
  public function approve_shift_data($id_header, $user_id)
  {
    $update_data = [
      'status_input' => 'approved',
      'approved_by' => $user_id,
      'approved_at' => date('Y-m-d H:i:s')
    ];

    $this->db->where('id_header', $id_header);
    $updated = $this->db->update('shift_bahan_header', $update_data);

    if ($updated) {
      return ['status' => 'success', 'message' => 'Data shift berhasil disetujui'];
    } else {
      return ['status' => 'error', 'message' => 'Gagal menyetujui data shift'];
    }
  }

  /**
   * Get summary data untuk dashboard
   */
  public function get_summary_data($tanggal_mulai = null, $tanggal_selesai = null, $divisi_filter = null)
  {
    if (!$tanggal_mulai) $tanggal_mulai = date('Y-m-01'); // First day of current month
    if (!$tanggal_selesai) $tanggal_selesai = date('Y-m-t'); // Last day of current month

    // Total shift days
    $this->db->select('COUNT(DISTINCT tanggal_shift) as total_days');
    $this->db->from('shift_bahan_header');
    $this->db->where('tanggal_shift >=', $tanggal_mulai);
    $this->db->where('tanggal_shift <=', $tanggal_selesai);
    $total_days = $this->db->get()->row_array()['total_days'];

    // Total unique bahan
    $this->db->select('COUNT(DISTINCT d.id_bahan) as total_bahan');
    $this->db->from('shift_bahan_detail d');
    $this->db->join('shift_bahan_header h', 'd.id_header = h.id_header', 'left');
    $this->db->where('h.tanggal_shift >=', $tanggal_mulai);
    $this->db->where('h.tanggal_shift <=', $tanggal_selesai);
    if ($divisi_filter) {
      $this->db->where('d.id_divisi', $divisi_filter);
    }
    $total_bahan = $this->db->get()->row_array()['total_bahan'];

    // Top 5 bahan paling banyak digunakan
    $this->db->select('b.nama_bahan, SUM(d.jumlah_kebutuhan) as total_usage');
    $this->db->from('shift_bahan_detail d');
    $this->db->join('shift_bahan_header h', 'd.id_header = h.id_header', 'left');
    $this->db->join('bahan b', 'd.id_bahan = b.id_bahan', 'left');
    $this->db->where('h.tanggal_shift >=', $tanggal_mulai);
    $this->db->where('h.tanggal_shift <=', $tanggal_selesai);
    if ($divisi_filter) {
      $this->db->where('d.id_divisi', $divisi_filter);
    }
    $this->db->group_by('d.id_bahan');
    $this->db->order_by('total_usage', 'DESC');
    $this->db->limit(5);
    $top_bahan = $this->db->get()->result_array();

    return [
      'total_days' => $total_days,
      'total_bahan' => $total_bahan,
      'top_bahan' => $top_bahan,
      'periode' => $tanggal_mulai . ' s/d ' . $tanggal_selesai
    ];
  }

  /**
   * Get bahan usage report
   */
  public function get_bahan_usage_report($tanggal_mulai, $tanggal_selesai)
  {
    $this->db->select('
			b.nama_bahan,
			s.nama_satuan,
			SUM(d.jumlah_kebutuhan) as total_kebutuhan,
			COUNT(DISTINCT h.tanggal_shift) as days_used,
			AVG(d.jumlah_kebutuhan) as avg_daily_usage
		');
    $this->db->from('shift_bahan_detail d');
    $this->db->join('shift_bahan_header h', 'd.id_header = h.id_header', 'left');
    $this->db->join('bahan b', 'd.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->where('h.tanggal_shift >=', $tanggal_mulai);
    $this->db->where('h.tanggal_shift <=', $tanggal_selesai);
    $this->db->group_by('d.id_bahan');
    $this->db->order_by('total_kebutuhan', 'DESC');

    return $this->db->get()->result_array();
  }

  /**
   * Get template data from shift_bahan_template table
   */
  public function get_template_data()
  {
    $this->db->select('*');
    $this->db->from('shift_bahan_template');
    $this->db->where('status', 'aktif');
    $this->db->order_by('id_bahan', 'ASC');

    return $this->db->get()->result_array();
  }

  /**
   * Get shift data by header ID
   */
  public function get_shift_data_by_id($id_header)
  {
    // Get header data
    $this->db->select('*');
    $this->db->from('shift_bahan_header');
    $this->db->where('id_header', $id_header);
    $header = $this->db->get()->row_array();

    if (!$header) {
      return null;
    }

    // Get detail data
    $this->db->select('d.*, b.nama_bahan, s.nama_satuan, div.nama_divisi, kat.nama_kategori');
    $this->db->from('shift_bahan_detail d');
    $this->db->join('bahan b', 'd.id_bahan = b.id_bahan', 'left');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    $this->db->join('divisi div', 'd.id_divisi = div.id_divisi', 'left');
    $this->db->join('shift_kategori kat', 'd.id_shift_kategori = kat.id_shift_kategori', 'left');
    $this->db->where('d.id_header', $id_header);
    $this->db->order_by('b.nama_bahan', 'ASC');
    $details = $this->db->get()->result_array();

    return [
      'header' => $header,
      'details' => $details
    ];
  }
}

/* End of file M_Shift_Bahan.php */
/* Location: ./application/models/M_Shift_Bahan.php */