<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Menu_Regular extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu_Regular');
    $this->load->model('M_Menu');
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data['title'] = 'Regular Menu Management';
    $data['menu_list'] = $this->M_Menu->get_all_menu();
    $data['menu_komponen'] = $this->M_Menu->get_all_menu();

    $show_data = $this->M_Menu_Regular->get_all_regular_menu_with_komponen();
    foreach ($show_data as &$menu) {
      $komponen = $this->M_Menu_Regular->get_komponen_with_kategori($menu->id);
      $menu->komponen = is_array($komponen) || is_object($komponen) ? $komponen : [];
    }
    $data['show_data'] = $show_data;
    $this->load->view('back/menu_regular/V_Menu_Regular', $data);
  }

  public function tampil()
  {
    header('Content-Type: application/json');

    try {
      $data = $this->M_Menu_Regular->get_all_regular_menu_with_komponen();

      foreach ($data as &$item) {
        if (!isset($item->harga)) {
          $item->harga = 0;
        }
      }

      echo json_encode([
        'status' => 'success',
        'show_data' => $data
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data: ' . $e->getMessage(),
        'show_data' => []
      ]);
    }
  }

  public function save_data()
  {
    header('Content-Type: application/json');

    try {
      // Validasi form
      $this->form_validation->set_rules('nama_menu_reg', 'Nama Menu Regular', 'required|trim');
      $this->form_validation->set_rules('harga', 'Harga', 'required|numeric');

      $komponen = $this->input->post('komponen_menu');
      $stat = $this->input->post('stat');
      $id = $this->input->post('id');

      if ($this->form_validation->run() == FALSE || empty($komponen)) {
        echo json_encode([
          'status' => 'error',
          'message' => validation_errors() ?: 'Pilih minimal satu komponen menu!'
        ]);
        return;
      }

      // Hilangkan duplikasi komponen
      $komponen = array_unique($komponen);
      $komponen = array_values($komponen);

      $data_regular = [
        'nama_menu_reg' => $this->input->post('nama_menu_reg'),
        'harga' => $this->input->post('harga')
      ];

      if ($stat == 'edit' && $id) {
        // Mode Edit
        $this->M_Menu_Regular->update_regular_menu($id, $data_regular);
        $this->M_Menu_Regular->delete_regular_menu_komponen($id);

        // Tunggu sebentar untuk memastikan delete selesai
        usleep(100000);

        $inserted_count = $this->_insert_komponen($id, $komponen);
        $message = 'Menu regular berhasil diupdate! (' . $inserted_count . ' komponen)';
      } else {
        // Mode Add
        $regular_id = $this->M_Menu_Regular->insert_regular_menu($data_regular);

        if (!$regular_id) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan menu regular'
          ]);
          return;
        }

        $inserted_count = $this->_insert_komponen($regular_id, $komponen);
        $message = 'Menu regular berhasil ditambahkan! (' . $inserted_count . ' komponen)';
      }

      echo json_encode([
        'status' => 'success',
        'message' => $message
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function delete_data()
  {
    header('Content-Type: application/json');

    try {
      $id = $this->input->post('id');

      if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
      }

      $this->M_Menu_Regular->delete_regular_menu($id);
      echo json_encode(['status' => 'success', 'message' => 'Menu regular berhasil dihapus']);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
    }
  }

  public function get_regular_menu_by_id()
  {
    header('Content-Type: application/json');

    try {
      $id = $this->input->post('id');

      if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
      }

      $menu = $this->M_Menu_Regular->get_by_id($id);

      if (!$menu) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        return;
      }

      $komponen = $this->M_Menu_Regular->get_komponen_by_menu_id($id);

      $data = [
        'id' => $menu->id,
        'nama_menu_reg' => $menu->nama_menu_reg,
        'deskripsi' => $menu->deskripsi,
        'harga' => $menu->harga,
        'komponen' => $komponen
      ];

      echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
  }

  public function get_komponen_details()
  {
    header('Content-Type: application/json');

    try {
      $komponen_ids = $this->input->post('komponen_ids');

      if (!$komponen_ids || !is_array($komponen_ids)) {
        echo json_encode(['status' => 'error', 'message' => 'ID komponen tidak valid']);
        return;
      }

      $komponen_details = [];
      foreach ($komponen_ids as $id_komponen) {
        $detail = $this->M_Menu->get_menu_with_kategori($id_komponen);
        if ($detail) {
          $komponen_details[] = $detail;
        }
      }

      echo json_encode([
        'status' => 'success',
        'data' => $komponen_details
      ]);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
  }

  // FUNGSI HELPER PRIVATE
  private function _insert_komponen($menu_id, $komponen_array)
  {
    $inserted_count = 0;

    foreach ($komponen_array as $id_komponen) {
      // Cek duplikasi sebelum insert
      $exists = $this->db->get_where('regular_menu_komponen', [
        'regular_menu_id' => $menu_id,
        'id_komponen' => $id_komponen
      ])->num_rows();

      if ($exists == 0) {
        $insert_result = $this->M_Menu_Regular->insert_regular_menu_komponen([
          'regular_menu_id' => $menu_id,
          'id_komponen' => $id_komponen
        ]);

        if ($insert_result) {
          $inserted_count++;
        }
      }
    }

    return $inserted_count;
  }

  // FUNGSI DEBUGGING (Opsional - bisa dihapus di production)
  public function debug_menu($id)
  {
    if (ENVIRONMENT !== 'development') {
      show_404();
      return;
    }

    header('Content-Type: text/html');
    echo "<h1>Debug Menu ID: $id</h1><pre>";

    try {
      echo "Test get_by_id:\n";
      $menu = $this->M_Menu_Regular->get_by_id($id);
      var_dump($menu);

      echo "\n\nStruktur tabel regular_menu:\n";
      $fields = $this->db->list_fields('regular_menu');
      print_r($fields);

      echo "\n\nTest komponen:\n";
      $this->db->select('a.id_komponen, b.menu_nama');
      $this->db->from('regular_menu_komponen a');
      $this->db->join('menu b', 'a.id_komponen = b.id_komponen', 'left');
      $this->db->where('a.regular_menu_id', $id);
      $query = $this->db->get();

      echo "SQL: " . $this->db->last_query() . "\n";
      print_r($query->result());
    } catch (Exception $e) {
      echo "ERROR: " . $e->getMessage();
    }

    echo "</pre>";
  }

  public function check_data_count()
  {
    if (ENVIRONMENT !== 'development') {
      show_404();
      return;
    }

    echo "<h2>📊 Analisis Jumlah Data Menu Regular</h2>";

    $total_regular = $this->db->count_all('regular_menu');
    echo "<p><strong>Total data di tabel regular_menu:</strong> $total_regular</p>";

    $show_data = $this->M_Menu_Regular->get_all_regular_menu_with_komponen();
    $count_from_model = count($show_data);
    echo "<p><strong>Data dari model:</strong> $count_from_model</p>";

    echo "<h3>📋 Daftar ID Menu Regular:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>No</th><th>ID</th><th>Nama Menu</th><th>Harga</th><th>Jumlah Komponen</th></tr>";

    foreach ($show_data as $index => $menu) {
      $komponen_count = is_array($menu->komponen) ? count($menu->komponen) : 0;
      echo "<tr>";
      echo "<td>" . ($index + 1) . "</td>";
      echo "<td>{$menu->id}</td>";
      echo "<td>{$menu->nama_menu_reg}</td>";
      echo "<td>Rp " . number_format($menu->harga) . "</td>";
      echo "<td>$komponen_count</td>";
      echo "</tr>";
    }
    echo "</table>";

    if ($total_regular > $count_from_model) {
      echo "<p style='color:red;'><strong>⚠️ Warning:</strong> Ada " . ($total_regular - $count_from_model) . " data yang tidak dikembalikan oleh model!</p>";

      $all_ids = $this->db->select('id')->get('regular_menu')->result();
      $model_ids = array_column($show_data, 'id');

      echo "<h3>🔍 Data yang Hilang:</h3>";
      foreach ($all_ids as $row) {
        if (!in_array($row->id, $model_ids)) {
          $missing = $this->db->get_where('regular_menu', ['id' => $row->id])->row();
          echo "<p style='color:red;'>ID: {$missing->id} - {$missing->nama_menu_reg}</p>";
        }
      }
    }
  }
}
