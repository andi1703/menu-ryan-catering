<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Vegetable_calculator extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Vegetable_calculator', 'calc');
    $this->load->model('M_Menu_Harian', 'mh');
  }

  public function index()
  {
    $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator');
  }

  // aggregated per-bahan (existing)
  public function data()
  {
    $start = $this->input->get('start');
    $end   = $this->input->get('end');
    $cust  = $this->input->get('customer_id');
    $kant  = $this->input->get('kantin_id');

    $data = $this->calc->calculate($start, $end, $cust, $kant);
    $this->output->set_content_type('application/json')
      ->set_output(json_encode(['data' => $data]));
  }

  // detail per-kondimen + BOM (for Excel-like table)
  public function table()
  {
    $start = $this->input->get('start');
    $end   = $this->input->get('end');
    $cust  = $this->input->get('customer_id');
    $kant  = $this->input->get('kantin_id');
    $shift = $this->input->get('shift');
    if ($shift) $shift = ucfirst(strtolower($shift));

    $rows = $this->calc->get_detail($start, $end, $cust, $kant, $shift);
    $this->output->set_content_type('application/json')
      ->set_output(json_encode(['data' => $rows]));
  }

  // get kondimen list by one or more menu_harian IDs
  public function kondimen()
  {
    $idsParam = $this->input->get('menu_ids'); // string: "1,2,3" or single id
    if (!$idsParam) {
      return $this->output->set_content_type('application/json')
        ->set_output(json_encode(['data' => []]));
    }
    $ids = array_filter(array_map('intval', preg_split('/[,\s]+/', $idsParam)));
    $result = [];
    foreach ($ids as $id) {
      $rows = $this->mh->get_all_kondimen($id);
      foreach ($rows as $r) {
        $nama = isset($r['nama_kondimen']) ? trim((string)$r['nama_kondimen']) : '';
        if ($nama === '' || $nama === '-') {
          $nama = isset($r['menu_menu_nama']) ? trim((string)$r['menu_menu_nama']) : '-';
        }
        $result[] = [
          'menu_harian_id' => $id,
          'recipe_id'      => (int)($r['id_komponen'] ?? 0),
          'nama_kondimen'  => $nama ?: '-',
          'kategori'       => $r['kategori_kondimen'] ?? '-',
          'total_order'    => (int)($r['qty_kondimen'] ?? 0),
          'yield_porsi'    => 1
        ];
      }
    }
    return $this->output->set_content_type('application/json')
      ->set_output(json_encode(['data' => $result]));
  }

  // GET: load saved bahan rows for a given kondimen key
  public function bahan_get()
  {
    $menu_harian_id = (int)$this->input->get('menu_harian_id');
    $id_komponen    = (int)$this->input->get('id_komponen');
    $nama_kondimen  = trim((string)$this->input->get('nama_kondimen'));

    // If id_komponen is not provided, only attempt exact name prefill
    if (!$id_komponen) {
      if ($nama_kondimen !== '') {
        $rows = $this->calc->get_prefill_bahan_by_menu_name($nama_kondimen);
        return $this->output->set_content_type('application/json')
          ->set_output(json_encode(['data' => $rows]));
      }
      return $this->output->set_content_type('application/json')
        ->set_output(json_encode(['data' => []]));
    }

    // With id_komponen: prefill dari menu_bahan_utama (skip saved rows untuk sekarang)
    $rows = [];
    if ($id_komponen > 0) {
      $rows = $this->calc->get_prefill_bahan_menu($id_komponen);
      log_message('debug', "bahan_get: prefill by id_komponen={$id_komponen}: " . count($rows));
    }
    if (empty($rows) && $nama_kondimen !== '') {
      $rows = $this->calc->get_prefill_bahan_by_menu_name($nama_kondimen);
      log_message('debug', "bahan_get: prefill by nama_kondimen={$nama_kondimen}: " . count($rows));
    }
    log_message('debug', "bahan_get final: returning " . count($rows) . " rows for id_komponen={$id_komponen}, nama={$nama_kondimen}");
    return $this->output->set_content_type('application/json')
      ->set_output(json_encode(['data' => $rows]));
  }

  // POST: save bahan rows (replace existing) for a kondimen key
  public function bahan_save()
  {
    $menu_harian_id = (int)$this->input->post('menu_harian_id');
    $id_komponen    = (int)$this->input->post('id_komponen');
    $itemsJson      = $this->input->post('items');
    $items = [];
    if (is_string($itemsJson)) {
      $decoded = json_decode($itemsJson, true);
      if (is_array($decoded)) $items = $decoded;
    } elseif (is_array($itemsJson)) {
      $items = $itemsJson;
    }

    if (!$menu_harian_id || !$id_komponen) {
      return $this->output->set_content_type('application/json')
        ->set_output(json_encode(['success' => false, 'message' => 'Missing keys']));
    }

    $ok = $this->calc->save_bahan_detail($menu_harian_id, $id_komponen, $items);
    return $this->output->set_content_type('application/json')
      ->set_output(json_encode(['success' => (bool)$ok]));
  }

  // ---- New session-based flow ----
  public function sessions()
  {
    // List saved sessions
    $rows = $this->db->select('s.*, c.nama_customer AS customer_nama')
      ->from('vegetable_calc_session s')
      ->join('customer c', 'c.id_customer = s.customer_id', 'left')
      ->order_by('s.created_at', 'DESC')
      ->get()->result_array();

    // attach counts if null
    foreach ($rows as &$r) {
      if (!isset($r['total_menu']) || !isset($r['total_bahan'])) {
        $menuIds = $this->db->select('menu_harian_id')->from('vegetable_calc_session_menu')->where('session_id', (int)$r['id'])->get()->result_array();
        $idList = array_map(function ($x) {
          return (int)$x['menu_harian_id'];
        }, $menuIds);
        $totalMenu = 0;
        $totalBahan = 0;
        if (!empty($idList)) {
          $totalMenu = (int)$this->db->where_in('id_menu_harian', $idList)->from('menu_harian_kondimen')->count_all_results();
          $totalBahan = (int)$this->db->where_in('id_menu_harian', $idList)->from('menu_kondimen_bahan')->count_all_results();
        }
        $r['total_menu'] = $totalMenu;
        $r['total_bahan'] = $totalBahan;
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode(['data' => $rows]));
  }

  public function session_create()
  {
    $tanggal = $this->input->post('tanggal');
    $customer_id = (int)$this->input->post('customer_id');
    $shift = trim((string)$this->input->post('shift'));
    $bahan_data_json = $this->input->post('bahan_data');
    $shiftNorm = $shift ? ucfirst(strtolower($shift)) : '';
    if (!$tanggal || !$customer_id || $shift === '') {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Input tidak lengkap']));
    }

    // fetch menu harian ids per filters
    $menus = $this->mh->get_all(['tanggal' => $tanggal, 'id_customer' => $customer_id, 'shift' => $shiftNorm]);
    if (empty($menus)) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Tidak ada menu harian untuk filter tersebut']));
    }
    $menuIds = array_map(function ($m) {
      return (int)($m['id_menu_harian'] ?? $m['id'] ?? 0);
    }, $menus);
    $menuIds = array_values(array_filter($menuIds));
    if (empty($menuIds)) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Data menu tidak valid']));
    }

    // Parse bahan data
    $bahanData = [];
    if ($bahan_data_json) {
      $decoded = json_decode($bahan_data_json, true);
      if (is_array($decoded)) $bahanData = $decoded;
    }

    // Start transaction
    $this->db->trans_start();

    // counts
    $totalMenu = (int)$this->db->where_in('id_menu_harian', $menuIds)->from('menu_harian_kondimen')->count_all_results();

    // Hitung total bahan dari data yang akan disimpan
    $totalBahan = 0;
    foreach ($bahanData as $bd) {
      if (isset($bd['items']) && is_array($bd['items'])) {
        $totalBahan += count($bd['items']);
      }
    }

    // insert session
    $data = [
      'tanggal' => $tanggal,
      'shift' => $shiftNorm,
      'customer_id' => $customer_id,
      'total_menu' => $totalMenu,
      'total_bahan' => $totalBahan,
      'created_at' => date('Y-m-d H:i:s')
    ];
    $this->db->insert('vegetable_calc_session', $data);
    $sid = (int)$this->db->insert_id();
    if ($sid <= 0) {
      $this->db->trans_rollback();
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Gagal membuat sesi']));
    }

    // map menus
    $batch = [];
    foreach ($menuIds as $mid) {
      $batch[] = [
        'session_id' => $sid,
        'menu_harian_id' => $mid,
        'created_at' => date('Y-m-d H:i:s')
      ];
    }
    if (!empty($batch)) $this->db->insert_batch('vegetable_calc_session_menu', $batch);

    // Save bahan data ke tabel menu_kondimen_bahan
    foreach ($bahanData as $bd) {
      $menuHarianId = (int)($bd['menu_harian_id'] ?? 0);
      $komponenId = (int)($bd['id_komponen'] ?? 0);
      $items = isset($bd['items']) && is_array($bd['items']) ? $bd['items'] : [];

      if ($menuHarianId > 0 && $komponenId > 0 && !empty($items)) {
        // Hapus existing data untuk kondimen ini
        $this->db->where('id_menu_harian', $menuHarianId);
        $this->db->where('id_komponen', $komponenId);
        $this->db->delete('menu_kondimen_bahan');

        // Insert batch bahan baru
        $bahanBatch = [];
        foreach ($items as $item) {
          $nama = isset($item['bahan_nama']) ? trim($item['bahan_nama']) : '';
          if ($nama === '') continue;

          $bahanBatch[] = [
            'id_menu_harian' => $menuHarianId,
            'id_komponen' => $komponenId,
            'bahan_nama' => $nama,
            'qty' => isset($item['qty']) ? (float)$item['qty'] : 0,
            'satuan' => isset($item['satuan']) ? trim($item['satuan']) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ];
        }

        if (!empty($bahanBatch)) {
          $this->db->insert_batch('menu_kondimen_bahan', $bahanBatch);
        }
      }
    }

    $this->db->trans_complete();
    $success = $this->db->trans_status();

    if (!$success) {
      log_message('error', 'session_create transaction failed: ' . $this->db->last_query());
    }

    return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => $success, 'id' => $sid]));
  }

  public function session_update()
  {
    $session_id = (int)$this->input->post('session_id');
    $tanggal = $this->input->post('tanggal');
    $customer_id = (int)$this->input->post('customer_id');
    $shift = trim((string)$this->input->post('shift'));
    $bahan_data_json = $this->input->post('bahan_data');
    $shiftNorm = $shift ? ucfirst(strtolower($shift)) : '';

    if (!$session_id || !$tanggal || !$customer_id || $shift === '') {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Input tidak lengkap']));
    }

    // Check if session exists
    $existingSession = $this->db->where('id', $session_id)->get('vegetable_calc_session')->row_array();
    if (!$existingSession) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Session tidak ditemukan']));
    }

    // fetch menu harian ids per filters
    $menus = $this->mh->get_all(['tanggal' => $tanggal, 'id_customer' => $customer_id, 'shift' => $shiftNorm]);
    if (empty($menus)) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Tidak ada menu harian untuk filter tersebut']));
    }
    $menuIds = array_map(function ($m) {
      return (int)($m['id_menu_harian'] ?? $m['id'] ?? 0);
    }, $menus);
    $menuIds = array_values(array_filter($menuIds));
    if (empty($menuIds)) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Data menu tidak valid']));
    }

    // Parse bahan data
    $bahanData = [];
    if ($bahan_data_json) {
      $decoded = json_decode($bahan_data_json, true);
      if (is_array($decoded)) $bahanData = $decoded;
    }

    // Start transaction
    $this->db->trans_start();

    // counts
    $totalMenu = (int)$this->db->where_in('id_menu_harian', $menuIds)->from('menu_harian_kondimen')->count_all_results();
    // Hitung total bahan berdasarkan jumlah items di setiap kelompok
    $totalBahan = 0;
    foreach ($bahanData as $bd) {
      if (isset($bd['items']) && is_array($bd['items'])) {
        $totalBahan += count($bd['items']);
      }
    }

    // Update session
    $this->db->where('id', $session_id);
    $this->db->update('vegetable_calc_session', [
      'tanggal' => $tanggal,
      'customer_id' => $customer_id,
      'shift' => $shiftNorm,
      'total_menu' => $totalMenu,
      'total_bahan' => $totalBahan
    ]);

    // Delete old menu mappings
    $this->db->where('session_id', $session_id)->delete('vegetable_calc_session_menu');

    // Insert new menu mappings
    $batch = [];
    foreach ($menuIds as $mid) {
      $batch[] = [
        'session_id' => $session_id,
        'menu_harian_id' => $mid,
        'created_at' => date('Y-m-d H:i:s')
      ];
    }
    if (!empty($batch)) $this->db->insert_batch('vegetable_calc_session_menu', $batch);

    // Save bahan data ke tabel menu_kondimen_bahan (replace existing)
    foreach ($bahanData as $bd) {
      $menuHarianId = (int)($bd['menu_harian_id'] ?? 0);
      $komponenId = (int)($bd['id_komponen'] ?? 0);
      $items = isset($bd['items']) && is_array($bd['items']) ? $bd['items'] : [];

      if ($menuHarianId > 0 && $komponenId > 0 && !empty($items)) {
        // Hapus existing data untuk kondimen ini
        $this->db->where('id_menu_harian', $menuHarianId);
        $this->db->where('id_komponen', $komponenId);
        $this->db->delete('menu_kondimen_bahan');

        // Insert batch bahan baru
        $bahanBatch = [];
        foreach ($items as $item) {
          $nama = isset($item['bahan_nama']) ? trim($item['bahan_nama']) : '';
          if ($nama === '') continue;

          $bahanBatch[] = [
            'id_menu_harian' => $menuHarianId,
            'id_komponen' => $komponenId,
            'bahan_nama' => $nama,
            'qty' => isset($item['qty']) ? (float)$item['qty'] : 0,
            'satuan' => isset($item['satuan']) ? trim($item['satuan']) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ];
        }

        if (!empty($bahanBatch)) {
          $this->db->insert_batch('menu_kondimen_bahan', $bahanBatch);
        }
      }
    }

    $this->db->trans_complete();
    $success = $this->db->trans_status();

    if (!$success) {
      log_message('error', 'session_update transaction failed: ' . $this->db->last_query());
    }

    return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => $success, 'id' => $session_id]));
  }

  public function session_delete()
  {
    $id = (int)$this->input->post('id');
    if (!$id) {
      return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'ID tidak valid']));
    }
    $this->db->trans_start();
    $this->db->where('session_id', $id)->delete('vegetable_calc_session_menu');
    $this->db->where('id', $id)->delete('vegetable_calc_session');
    $this->db->trans_complete();
    $ok = $this->db->trans_status();
    return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => (bool)$ok]));
  }

  public function detail($id)
  {
    $data = ['session_id' => (int)$id];
    $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator_detail', $data);
  }

  public function session_detail()
  {
    $id = (int)$this->input->get('id');
    if (!$id) return $this->output->set_content_type('application/json')->set_output(json_encode(['data' => null]));

    // Get session with customer name via JOIN
    $session = $this->db
      ->select('vegetable_calc_session.*, customer.nama_customer')
      ->from('vegetable_calc_session')
      ->join('customer', 'customer.id_customer = vegetable_calc_session.customer_id', 'left')
      ->where('vegetable_calc_session.id', $id)
      ->get()
      ->row_array();

    if (!$session) return $this->output->set_content_type('application/json')->set_output(json_encode(['data' => null]));

    $menuIds = $this->db->select('menu_harian_id')->from('vegetable_calc_session_menu')->where('session_id', $id)->get()->result_array();
    $idList = array_map(function ($x) {
      return (int)$x['menu_harian_id'];
    }, $menuIds);

    // Deduplikasi kondimen berdasarkan id_komponen dan nama_kondimen
    $uniqueKondimen = [];
    $items = [];

    foreach ($idList as $mid) {
      $konds = $this->mh->get_all_kondimen($mid);
      foreach ($konds as $k) {
        $komponenId = (int)($k['id_komponen'] ?? 0);
        $namaKondimen = trim($k['nama_kondimen'] ?? '');
        $key = $komponenId . '_' . $namaKondimen; // Unique key untuk deduplikasi

        if (!isset($uniqueKondimen[$key])) {
          $bahan = $this->calc->get_bahan_detail($mid, $komponenId);
          $uniqueKondimen[$key] = [
            'menu_harian_id' => $mid,
            'nama_kondimen' => $namaKondimen ?: '-',
            'id_komponen' => $komponenId,
            'qty_kondimen' => (int)($k['qty_kondimen'] ?? 0),
            'bahan' => $bahan
          ];
        } else {
          // Jika sudah ada, tambahkan qty nya
          $uniqueKondimen[$key]['qty_kondimen'] += (int)($k['qty_kondimen'] ?? 0);
        }
      }
    }

    $items = array_values($uniqueKondimen); // Convert associative array to indexed array

    return $this->output->set_content_type('application/json')->set_output(json_encode(['data' => ['session' => $session, 'items' => $items]]));
  }

  public function get_bahan_dropdown()
  {
    $this->db->select('b.id_bahan, b.nama_bahan, b.id_satuan, s.nama_satuan, b.status');
    $this->db->from('bahan b');
    $this->db->join('satuan s', 's.id_satuan = b.id_satuan', 'left');
    $this->db->where('b.status', 'aktif');
    $this->db->order_by('b.nama_bahan', 'ASC');
    $query = $this->db->get();

    $data = $query->result_array();
    return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'data' => $data]));
  }
}
