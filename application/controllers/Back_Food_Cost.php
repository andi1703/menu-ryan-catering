<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Food_Cost extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Food_Cost');
    $this->load->model('M_Satuan');
    $this->load->model('M_Bahan');
    $this->load->library('form_validation');
    $this->load->helper('url');
    $this->load->database();
  }

  public function index()
  {
    $data['title'] = 'Food Cost Menu Regular';

    // Load bahan dari database dengan error handling
    try {
      // Coba method pertama
      $data['bahan_list'] = $this->M_Bahan->get_all_bahan_simple();
    } catch (Exception $e) {
      try {
        // Fallback ke method alternative
        $data['bahan_list'] = $this->M_Bahan->get_bahan_with_price_alternative();
      } catch (Exception $e2) {
        try {
          // Fallback terakhir - basic query
          $data['bahan_list'] = $this->M_Bahan->get_all_bahan();

          // Process harga_current manually
          foreach ($data['bahan_list'] as $bahan) {
            $bahan->harga_current = ($bahan->harga_sekarang > 0) ? $bahan->harga_sekarang : $bahan->harga_awal;
          }
        } catch (Exception $e3) {
          // Jika semua gagal, set empty array
          $data['bahan_list'] = [];
          log_message('error', 'Gagal load bahan: ' . $e3->getMessage());
        }
      }
    }

    // Load satuan
    try {
      $data['satuan_list'] = $this->M_Satuan->getAllSatuan();
    } catch (Exception $e) {
      $data['satuan_list'] = $this->get_default_satuan();
    }

    $this->load->view('back/food_cost/V_Food_Cost', $data);
  }

  /**
   * Default satuan jika table satuan tidak ada
   */
  private function get_default_satuan()
  {
    return [
      (object)['id_satuan' => 1, 'nama_satuan' => 'kg'],
      (object)['id_satuan' => 2, 'nama_satuan' => 'gram'],
      (object)['id_satuan' => 3, 'nama_satuan' => 'liter'],
      (object)['id_satuan' => 4, 'nama_satuan' => 'ml'],
      (object)['id_satuan' => 5, 'nama_satuan' => 'pcs'],
      (object)['id_satuan' => 6, 'nama_satuan' => 'buah']
    ];
  }

  /**
   * Test database connection
   */
  public function test_db()
  {
    try {
      // Test basic query
      $query = $this->db->query("SELECT COUNT(*) as total FROM bahan");
      $result = $query->row();

      echo "Database connection: OK<br>";
      echo "Total bahan: " . $result->total . "<br><br>";

      // Test join query
      $query2 = $this->db->query("
                SELECT b.*, s.nama_satuan 
                FROM bahan b 
                LEFT JOIN satuan s ON b.id_satuan = s.id_satuan 
                LIMIT 5
            ");
      $results = $query2->result();

      echo "Sample data:<br>";
      foreach ($results as $bahan) {
        echo "- " . $bahan->nama_bahan . " (" . ($bahan->nama_satuan ?? 'No satuan') . ")<br>";
      }
    } catch (Exception $e) {
      echo "Database error: " . $e->getMessage();
    }
  }

  /**
   * AJAX: Search bahan dari database
   */
  public function search_bahan()
  {
    header('Content-Type: application/json');

    $keyword = $this->input->post('keyword');

    if (strlen($keyword) < 2) {
      echo json_encode([
        'success' => false,
        'message' => 'Keyword minimal 2 karakter',
        'data' => []
      ]);
      return;
    }

    try {
      $bahan = $this->M_Bahan->search_bahan($keyword);
      echo json_encode([
        'success' => true,
        'data' => $bahan
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
      ]);
    }
  }

  /**
   * AJAX: Get detail bahan by ID
   */
  public function get_detail_bahan()
  {
    header('Content-Type: application/json');

    $id_bahan = $this->input->post('id_bahan');

    try {
      $bahan = $this->M_Bahan->get_by_id($id_bahan);

      if (!$bahan) {
        echo json_encode([
          'success' => false,
          'message' => 'Bahan tidak ditemukan'
        ]);
        return;
      }

      // Determine current price
      $harga_current = ($bahan->harga_sekarang > 0) ? $bahan->harga_sekarang : $bahan->harga_awal;

      echo json_encode([
        'success' => true,
        'data' => [
          'id_bahan' => $bahan->id_bahan,
          'nama_bahan' => $bahan->nama_bahan,
          'nama_satuan' => $bahan->nama_satuan,
          'id_satuan' => $bahan->id_satuan,
          'harga_awal' => $bahan->harga_awal,
          'harga_sekarang' => $bahan->harga_sekarang,
          'harga_current' => $harga_current
        ]
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  /**
   * Get all menu data via AJAX
   */
  public function get_data()
  {
    header('Content-Type: application/json');

    try {
      // Check if tables exist first
      if (!$this->db->table_exists('menu_regular_food_cost')) {
        echo json_encode([
          'status' => 'error',
          'message' => 'Tabel menu_regular_food_cost belum ada. Jalankan SQL setup terlebih dahulu.',
          'data' => []
        ]);
        return;
      }

      $data = $this->M_Food_Cost->get_all_menu_food_cost();
      echo json_encode([
        'status' => 'success',
        'data' => $data
      ]);
    } catch (Exception $e) {
      log_message('error', 'Food Cost get_data error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data: ' . $e->getMessage(),
        'data' => []
      ]);
    }
  }

  /**
   * Get menu by ID
   */
  public function get_by_id()
  {
    header('Content-Type: application/json');

    $id = $this->input->post('id');

    if (!$id) {
      echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
      return;
    }

    try {
      $data = $this->M_Food_Cost->get_menu_by_id($id);

      if ($data) {
        echo json_encode(['status' => 'success', 'data' => $data]);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
      }
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Save menu data
   */
  public function save_data()
  {
    header('Content-Type: application/json');

    try {
      // Validation
      $this->form_validation->set_rules('nama_menu', 'Nama Menu', 'required|trim');
      $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

      // Get bahan data
      $bahan_nama = $this->input->post('bahan_nama');
      $bahan_qty = $this->input->post('bahan_qty');
      $bahan_satuan = $this->input->post('bahan_satuan');
      $bahan_harga = $this->input->post('bahan_harga');
      $bahan_pembagian = $this->input->post('bahan_pembagian');

      if ($this->form_validation->run() == FALSE) {
        echo json_encode([
          'status' => 'error',
          'message' => validation_errors()
        ]);
        return;
      }

      // Validate bahan data
      if (empty($bahan_nama) || !is_array($bahan_nama)) {
        echo json_encode([
          'status' => 'error',
          'message' => 'Tambahkan minimal 1 bahan'
        ]);
        return;
      }

      $stat = $this->input->post('stat');
      $id = $this->input->post('id');

      // Prepare menu data
      $menu_data = [
        'nama_menu' => $this->input->post('nama_menu'),
        'deskripsi' => $this->input->post('deskripsi')
      ];

      if ($stat == 'edit' && $id) {
        // Update mode
        $this->M_Food_Cost->update_menu($id, $menu_data);
        $menu_id = $id;

        // Delete existing bahan
        $this->M_Food_Cost->delete_bahan_by_menu_id($menu_id);

        $message = 'Menu food cost berhasil diperbarui!';
      } else {
        // Insert mode
        $menu_id = $this->M_Food_Cost->insert_menu($menu_data);
        $message = 'Menu food cost berhasil ditambahkan!';
      }

      // Insert bahan data
      $bahan_inserted = 0;
      for ($i = 0; $i < count($bahan_nama); $i++) {
        if (!empty($bahan_nama[$i])) {
          $bahan_data = [
            'menu_id' => $menu_id,
            'nama_bahan' => $bahan_nama[$i],
            'qty' => floatval($bahan_qty[$i]),
            'satuan' => $bahan_satuan[$i],
            'harga_per_satuan' => floatval($bahan_harga[$i]),
            'pembagian_porsi' => intval($bahan_pembagian[$i]),
            'urutan' => $i + 1
          ];

          $this->M_Food_Cost->insert_bahan($bahan_data);
          $bahan_inserted++;
        }
      }

      echo json_encode([
        'status' => 'success',
        'message' => $message . " ($bahan_inserted bahan)"
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Delete menu
   */
  public function delete_data()
  {
    header('Content-Type: application/json');

    $id = $this->input->post('id');

    if (!$id) {
      echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
      return;
    }

    try {
      $this->M_Food_Cost->delete_menu($id);
      echo json_encode([
        'status' => 'success',
        'message' => 'Menu food cost berhasil dihapus'
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus data: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Calculate food cost (real-time calculation)
   */
  public function calculate_cost()
  {
    header('Content-Type: application/json');

    $bahan_qty = $this->input->post('qty');
    $bahan_harga = $this->input->post('harga');
    $bahan_pembagian = $this->input->post('pembagian');

    if (!$bahan_qty || !$bahan_harga || !$bahan_pembagian) {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
      return;
    }

    $total_bahan_mentah = 0;

    for ($i = 0; $i < count($bahan_qty); $i++) {
      if (!empty($bahan_qty[$i]) && !empty($bahan_harga[$i]) && !empty($bahan_pembagian[$i])) {
        $qty = floatval($bahan_qty[$i]);
        $harga = floatval($bahan_harga[$i]);
        $pembagian = intval($bahan_pembagian[$i]);

        // Calculate per portion cost
        $harga_per_porsi = ($qty * $harga) / $pembagian;
        $total_bahan_mentah += $harga_per_porsi;
      }
    }

    $biaya_produksi = $total_bahan_mentah * 0.20;
    $food_cost = $total_bahan_mentah + $biaya_produksi;

    echo json_encode([
      'status' => 'success',
      'data' => [
        'total_bahan_mentah' => $total_bahan_mentah,
        'biaya_produksi' => $biaya_produksi,
        'food_cost' => $food_cost
      ]
    ]);
  }

  /**
   * Get dashboard statistics
   */
  public function get_stats()
  {
    header('Content-Type: application/json');

    try {
      // Check if tables exist first
      if (!$this->db->table_exists('menu_regular_food_cost')) {
        echo json_encode([
          'status' => 'success',
          'data' => [
            'total_menu' => 0,
            'avg_food_cost' => 0,
            'total_food_cost' => 0
          ]
        ]);
        return;
      }

      $stats = $this->M_Food_Cost->get_summary_stats();
      echo json_encode([
        'status' => 'success',
        'data' => $stats
      ]);
    } catch (Exception $e) {
      log_message('error', 'Food Cost get_stats error: ' . $e->getMessage());
      echo json_encode([
        'status' => 'success',
        'data' => [
          'total_menu' => 0,
          'avg_food_cost' => 0,
          'total_food_cost' => 0
        ]
      ]);
    }
  }

  /**
   * Debug method to check database setup
   */
  public function debug_setup()
  {
    if (ENVIRONMENT !== 'development') {
      show_404();
      return;
    }

    echo "<h2>🔍 Debug Food Cost Setup</h2>";

    // Check database connection
    echo "<h3>📂 Database Connection:</h3>";
    if ($this->db->conn_id) {
      echo "✅ Database connected<br>";
    } else {
      echo "❌ Database not connected<br>";
    }

    // Check tables
    echo "<h3>📋 Table Check:</h3>";
    $tables = ['menu_regular_food_cost', 'menu_regular_bahan', 'satuan'];

    foreach ($tables as $table) {
      if ($this->db->table_exists($table)) {
        $count = $this->db->count_all($table);
        echo "✅ Table '$table' exists ($count records)<br>";

        // Show columns
        $fields = $this->db->list_fields($table);
        echo "&nbsp;&nbsp;&nbsp;Columns: " . implode(', ', $fields) . "<br>";
      } else {
        echo "❌ Table '$table' does not exist<br>";
      }
    }

    // Check models
    echo "<h3>🔧 Models Check:</h3>";
    try {
      $this->load->model('M_Food_Cost');
      echo "✅ M_Food_Cost loaded<br>";
    } catch (Exception $e) {
      echo "❌ M_Food_Cost error: " . $e->getMessage() . "<br>";
    }

    try {
      $this->load->model('M_Satuan');
      echo "✅ M_Satuan loaded<br>";
    } catch (Exception $e) {
      echo "❌ M_Satuan error: " . $e->getMessage() . "<br>";
    }

    // Test basic queries
    echo "<h3>🔍 Basic Query Test:</h3>";
    try {
      if ($this->db->table_exists('satuan')) {
        $satuan = $this->M_Satuan->getAllSatuan();
        echo "✅ Satuan query OK (" . count($satuan) . " records)<br>";
      }
    } catch (Exception $e) {
      echo "❌ Satuan query error: " . $e->getMessage() . "<br>";
    }

    echo "<hr>";
    echo "<h3>📝 SQL Setup:</h3>";
    echo "Jika ada table yang missing, jalankan SQL berikut:<br>";
    echo "<textarea rows='10' cols='80' readonly>";
    echo file_get_contents(FCPATH . '../sql/food_cost_setup.sql');
    echo "</textarea>";
  }
}
