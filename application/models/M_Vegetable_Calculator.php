<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Vegetable_calculator extends CI_Model
{
  private $table_bahan = 'menu_kondimen_bahan';

  public function calculate($startDate, $endDate, $customerId = null, $kantinId = null)
  {
    $this->db->select('
      mh.id AS menu_id, mh.tanggal, mh.customer_id, mh.kantin_id,
      mk.recipe_id, mk.qty_ab, mk.qty_at, mk.qty_wb,
      r.yield_porsi
    ');
    $this->db->from('menu_harian mh');
    $this->db->join('menu_harian_kondimen mk', 'mk.menu_harian_id = mh.id');
    $this->db->join('resep r', 'r.id = mk.recipe_id');
    $this->db->where('mh.tanggal >=', $startDate);
    $this->db->where('mh.tanggal <=', $endDate);
    if ($customerId) $this->db->where('mh.customer_id', $customerId);
    if ($kantinId)   $this->db->where('mh.kantin_id', $kantinId);

    $rows = $this->db->get()->result_array();
    $agg = [];

    foreach ($rows as $row) {
      foreach ([(int)($row['qty_ab'] ?? 0), (int)($row['qty_at'] ?? 0), (int)($row['qty_wb'] ?? 0)] as $qtyPorsi) {
        if ($qtyPorsi <= 0) continue;

        $yield   = max(1, (int)$row['yield_porsi']);
        $batches = (int)ceil($qtyPorsi / $yield);

        $bom = $this->db->select('rb.bahan_id, rb.qty_per_batch, rb.satuan_id, b.nama AS bahan_nama, s.nama AS satuan_nama, b.pack_size')
          ->from('resep_bahan rb')
          ->join('bahan b', 'b.id = rb.bahan_id')
          ->join('satuan s', 's.id = rb.satuan_id')
          ->where('rb.recipe_id', $row['recipe_id'])
          ->get()->result_array();

        foreach ($bom as $item) {
          $need = $batches * (float)$item['qty_per_batch'];
          if (!empty($item['pack_size']) && (float)$item['pack_size'] > 0) {
            $pack = (float)$item['pack_size'];
            $need = ceil($need / $pack) * $pack;
          }

          $key = $item['bahan_id'] . '|' . $item['satuan_id'];
          if (!isset($agg[$key])) {
            $agg[$key] = [
              'bahan_id'    => $item['bahan_id'],
              'bahan_nama'  => $item['bahan_nama'],
              'satuan_id'   => $item['satuan_id'],
              'satuan_nama' => $item['satuan_nama'],
              'pack_size'   => (float)($item['pack_size'] ?? 0),
              'qty'         => 0,
            ];
          }
          $agg[$key]['qty'] += $need;
        }
      }
    }

    foreach ($agg as &$r) {
      $pack = (float)$r['pack_size'];
      $r['total_packs'] = $pack > 0 ? (int)ceil($r['qty'] / $pack) : null;
    }
    return array_values($agg);
  }

  public function get_detail($startDate, $endDate, $customerId = null, $kantinId = null, $shift = null)
  {
    // Return per-kondimen rows with menu_harian_id for saving bahan
    // GROUP BY hanya berdasarkan kondimen (bukan per menu_harian) untuk menghindari duplikasi
    // Ambil MAX(id_menu_harian) sebagai representasi dan ambil nama_menu + jenis_menu
    $this->db->select('MAX(mhk.id_menu_harian) AS menu_harian_id, mhk.id_komponen, mhk.nama_kondimen, SUM(COALESCE(mhk.qty_kondimen,0)) AS total_order, MAX(mh.nama_menu) AS nama_menu, MAX(mh.jenis_menu) AS jenis_menu');
    $this->db->from('menu_harian_kondimen mhk');
    $this->db->join('menu_harian mh', 'mhk.id_menu_harian = mh.id_menu_harian', 'inner');
    $this->db->where('mh.tanggal >=', $startDate);
    $this->db->where('mh.tanggal <=', $endDate);
    if ($customerId) $this->db->where('mh.id_customer', $customerId);
    if ($kantinId)   $this->db->where('mh.id_kantin', $kantinId);
    if ($shift) {
      $shiftNorm = ucfirst(strtolower($shift));
      $this->db->where('mh.shift', $shiftNorm);
    }

    // GROUP BY hanya berdasarkan kondimen untuk menggabungkan semua menu_harian yang sama
    $this->db->group_by(['mhk.id_komponen', 'mhk.nama_kondimen']);

    $query = $this->db->get();
    $rows = $query->result_array();

    // Log SQL untuk debug
    log_message('debug', 'get_detail SQL: ' . $this->db->last_query());
    log_message('debug', 'get_detail: found ' . count($rows) . ' kondimen rows for shift=' . ($shift ?? 'ALL'));

    // Enrich dengan lookup id_komponen dari menu jika kosong
    foreach ($rows as &$c) {
      $idKomp = (int)($c['id_komponen'] ?? 0);
      $namaKond = trim($c['nama_kondimen'] ?? '');

      // Jika nama kosong atau "-", coba ambil dari menu
      if (($namaKond === '' || $namaKond === '-') && $idKomp > 0) {
        $menu = $this->db->select('menu_nama')->from('menu')->where('id_komponen', $idKomp)->get()->row_array();
        if ($menu) {
          $namaKond = $menu['menu_nama'];
        }
      }

      // Jika id_komponen kosong, cari dari tabel menu by nama
      if ($idKomp <= 0 && $namaKond !== '' && $namaKond !== '-') {
        $menu = $this->db->select('id_komponen, menu_nama')
          ->from('menu')
          ->where('menu_nama', $namaKond)
          ->get()->row_array();
        if ($menu) {
          $idKomp = (int)$menu['id_komponen'];
          log_message('debug', "Lookup menu '{$namaKond}' -> id_komponen={$idKomp}");
        }
      }

      $c['recipe_id'] = $idKomp;
      $c['nama_kondimen'] = $namaKond !== '' ? $namaKond : '-';
      $c['yield_porsi'] = 1;
      $c['bom'] = [];

      log_message('debug', "Row: menu_harian_id={$c['menu_harian_id']}, recipe_id={$idKomp}, nama={$namaKond}, qty={$c['total_order']}");
    }

    return $rows;
  }

  public function get_bahan_detail($id_menu_harian, $id_komponen)
  {
    $this->db->from($this->table_bahan);
    $this->db->where('id_menu_harian', (int)$id_menu_harian);
    $this->db->where('id_komponen', (int)$id_komponen);
    $this->db->order_by('id', 'ASC');
    return $this->db->get()->result_array();
  }

  public function save_bahan_detail($id_menu_harian, $id_komponen, $items)
  {
    $id_menu_harian = (int)$id_menu_harian;
    $id_komponen = (int)$id_komponen;
    $this->db->trans_start();

    // Replace strategy: delete existing then insert batch
    $this->db->where('id_menu_harian', $id_menu_harian);
    $this->db->where('id_komponen', $id_komponen);
    $this->db->delete($this->table_bahan);

    $batch = [];
    foreach ((array)$items as $it) {
      $nama = isset($it['bahan_nama']) ? trim($it['bahan_nama']) : '';
      if ($nama === '') continue;
      $batch[] = [
        'id_menu_harian' => $id_menu_harian,
        'id_komponen'    => $id_komponen,
        'bahan_nama'     => $nama,
        'qty'            => isset($it['qty']) ? (float)$it['qty'] : 0,
        'satuan'         => isset($it['satuan']) ? trim($it['satuan']) : null,
        'created_at'     => date('Y-m-d H:i:s'),
        'updated_at'     => date('Y-m-d H:i:s'),
      ];
    }
    if (!empty($batch)) {
      $this->db->insert_batch($this->table_bahan, $batch);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  // Prefill bahan from master mapping: menu -> menu_bahan_utama -> bahan (+ satuan)
  public function get_prefill_bahan_menu($id_komponen)
  {
    $id_komponen = (int)$id_komponen;
    if ($id_komponen <= 0) {
      log_message('debug', 'get_prefill_bahan_menu: id_komponen invalid: ' . $id_komponen);
      return [];
    }

    $this->db->select('b.id_bahan, b.nama_bahan, s.nama_satuan, s.id_satuan');
    $this->db->from('menu_bahan_utama mb');
    $this->db->join('bahan b', 'mb.bahan_id = b.id_bahan', 'inner');
    $this->db->join('satuan s', 's.id_satuan = b.id_satuan', 'left');
    $this->db->where('mb.menu_id', $id_komponen);
    $this->db->order_by('mb.created_at', 'ASC');

    $query = $this->db->get();
    $rows = $query->result_array();

    log_message('debug', 'get_prefill_bahan_menu for id_komponen=' . $id_komponen . ': found ' . count($rows) . ' rows');

    // map to calculator structure (qty defaults to 0)
    $out = [];
    foreach ($rows as $r) {
      $nama = !empty($r['nama_bahan']) ? $r['nama_bahan'] : '';
      $satuan = !empty($r['nama_satuan']) ? $r['nama_satuan'] : '';
      if ($nama !== '') {
        $out[] = [
          'bahan_nama' => $nama,
          'nama_bahan' => $nama,
          'qty'        => 0,
          'satuan'     => $satuan,
          'nama_satuan' => $satuan
        ];
      }
    }
    return $out;
  }

  // Prefill using menu name when id_komponen is not available
  public function get_prefill_bahan_by_menu_name($menu_nama)
  {
    $menu_nama = trim((string)$menu_nama);
    if ($menu_nama === '') return [];
    $this->db->select('b.id_bahan, b.nama_bahan AS bahan_nama, s.nama_satuan');
    $this->db->from('menu m');
    $this->db->join('menu_bahan_utama mb', 'mb.menu_id = m.id_komponen', 'inner');
    $this->db->join('bahan b', 'mb.bahan_id = b.id_bahan', 'inner');
    $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
    // Only exact match allowed (no LIKE/fuzzy)
    $this->db->where('m.menu_nama', $menu_nama);
    $rows = $this->db->get()->result_array();
    $out = [];
    foreach ($rows as $r) {
      $nama = isset($r['bahan_nama']) ? $r['bahan_nama'] : (isset($r['nama_bahan']) ? $r['nama_bahan'] : '');
      $satuan = isset($r['nama_satuan']) ? $r['nama_satuan'] : null;
      if ($nama === '') continue;
      $out[] = [
        'bahan_nama' => $nama,
        'nama_bahan' => $nama,
        'qty'        => 0,
        'satuan'     => $satuan,
        'nama_satuan' => $satuan
      ];
    }
    return $out;
  }
}
