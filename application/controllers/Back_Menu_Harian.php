<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Menu_Harian extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    // Pastikan nama model sudah benar 'M_Menu_Harian'
    $this->load->model('M_Menu_Harian');
    $this->load->library('form_validation');
  }

  /**
   * Menampilkan halaman utama (HTML)
   */
  public function index()
  {
    if ($this->input->is_ajax_request()) {
      // Panggil ajax_list untuk mengambil data
      $this->ajax_list();
    } else {
      $this->load->view('back/menu_harian/V_Menu_Harian');
    }
  }

  /**
   * Mengambil data untuk DataTables (AJAX)
   */
  public function ajax_list()
  {
    $filter = [
      'shift' => $this->input->get('shift'),
      'tanggal' => $this->input->get('tanggal'),
      'id_customer' => $this->input->get('id_customer')
    ];

    $list = $this->M_Menu_Harian->get_all($filter);

    // ✅ GROUP DATA BY TANGGAL, SHIFT, CUSTOMER, JENIS_MENU, NAMA_MENU
    $grouped = [];
    foreach ($list as $item) {
      $key = $item['tanggal'] . '|' . $item['shift'] . '|' . $item['id_customer'] . '|' . $item['jenis_menu'] . '|' . $item['nama_menu'];

      if (!isset($grouped[$key])) {
        $grouped[$key] = [
          'tanggal' => $item['tanggal'],
          'shift' => $item['shift'],
          'nama_customer' => $item['nama_customer'],
          'jenis_menu' => $item['jenis_menu'],
          'nama_menu' => $item['nama_menu'],
          'kantins' => [],
          'kondimen_data' => [], // Array untuk nested table
          'total_orderan' => 0,
          'ids' => [] // Untuk edit/delete
        ];
      }

      // Tambahkan kantin
      $grouped[$key]['kantins'][] = $item['nama_kantin'];
      $grouped[$key]['total_orderan'] += (int)$item['total_orderan_perkantin'];
      $grouped[$key]['ids'][] = $item['id_menu_harian'];

      // ✅ PARSE KONDIMEN UNTUK NESTED TABLE
      $kondimen_items = explode(', ', $item['kondimen']);
      foreach ($kondimen_items as $k) {
        if (preg_match('/^(.*?) \((.*?)\) \((.*?)\)$/', $k, $matches)) {
          $nama = $matches[1];
          $kategori = $matches[2];
          $qty = $matches[3];

          // Cari kondimen dengan nama yang sama
          $found = false;
          foreach ($grouped[$key]['kondimen_data'] as &$kond) {
            if ($kond['nama'] === $nama && $kond['kategori'] === $kategori) {
              $kond['qty'][$item['nama_kantin']] = $qty;
              $found = true;
              break;
            }
          }

          if (!$found) {
            $grouped[$key]['kondimen_data'][] = [
              'nama' => $nama,
              'kategori' => $kategori,
              'qty' => [$item['nama_kantin'] => $qty]
            ];
          }
        }
      }
    }

    // Convert ke array biasa
    $result = array_values($grouped);

    echo json_encode(['show_data' => $result]);
  }

  /**
   * Menyimpan data baru atau update (AJAX)
   */
  public function save()
  {
    $data = $this->input->post();
    $id_menu_harian = !empty($data['id_menu_harian']) ? $data['id_menu_harian'] : null;
    $kondimen = json_decode($data['kondimen'], true);
    unset($data['kondimen']);
    unset($data['id_menu_harian']);

    // id_kantin may be an array (multiple selected kantins) or a single value
    $selected_kantins = [];
    if (isset($data['id_kantin'])) {
      if (is_array($data['id_kantin'])) $selected_kantins = $data['id_kantin'];
      else $selected_kantins = [$data['id_kantin']];
    }
    // Remove from $data so model->insert receives clean scalar fields
    unset($data['id_kantin']);

    // Validasi kondimen tidak kosong
    if (empty($kondimen) || count($kondimen) == 0) {
      echo json_encode(['status' => 'error', 'msg' => 'Menu kondimen wajib diisi!']);
      return;
    }

    // If updating: remove existing record(s) and re-insert per selected kantin
    if ($id_menu_harian) {
      // Remove original menu_harian and its kondimen (we will re-create entries per selected kantin)
      try {
        $this->M_Menu_Harian->delete($id_menu_harian);
      } catch (Exception $e) {
        // continue, we'll attempt re-insert
        log_message('error', 'Error deleting old menu_harian before update: ' . $e->getMessage());
      }
    }

    // Now insert one menu_harian per selected kantin
    if (empty($selected_kantins)) {
      echo json_encode(['status' => 'error', 'msg' => 'Pilih minimal 1 kantin']);
      return;
    }

    $inserted = [];
    foreach ($selected_kantins as $id_kantin) {
      $menuData = $data;
      $menuData['id_kantin'] = $id_kantin;

      // ✅ HITUNG TOTAL LAUK UTAMA untuk kantin ini
      $total_lauk_utama = 0;
      foreach ($kondimen as $k) {
        // Cek apakah kategori = "Lauk Utama"
        if (strtolower(trim($k['kategori'])) == 'lauk utama') {
          if (!empty($k['qty_per_kantin']) && isset($k['qty_per_kantin'][$id_kantin])) {
            $total_lauk_utama += intval($k['qty_per_kantin'][$id_kantin]);
          }
        }
      }

      // ✅ ISI total_orderan_perkantin dengan total lauk utama
      $menuData['total_orderan_perkantin'] = $total_lauk_utama;

      $new_id = $this->M_Menu_Harian->insert($menuData);
      if ($new_id) {
        // insert kondimen for this kantin
        foreach ($kondimen as $k) {
          $menu = $this->db->select('menu_nama')->where('id_komponen', $k['id_komponen'])->get('menu')->row();
          $nama_kondimen = $menu ? $menu->menu_nama : '';

          // ✅ PERBAIKAN: qty_per_kantin expected as associative array { id_kantin: qty }
          $qty = 0;
          if (!empty($k['qty_per_kantin']) && isset($k['qty_per_kantin'][$id_kantin])) {
            $qty = $k['qty_per_kantin'][$id_kantin];
          }

          // ✅ PERBAIKAN: Gunakan array() bukan short array syntax dengan dynamic key
          $kondimen_data = array(
            'id_menu_harian' => $new_id,
            'id_komponen'    => $k['id_komponen'],
            'nama_kondimen'  => $nama_kondimen,
            'kategori_kondimen' => $k['kategori'],
            'qty_kondimen'   => $qty
          );

          $this->db->insert('menu_harian_kondimen', $kondimen_data);
        }
        $inserted[] = $new_id;
      }
    }

    if (!empty($inserted)) {
      echo json_encode(['status' => 'success', 'msg' => 'Menu harian berhasil disimpan']);
    } else {
      echo json_encode(['status' => 'error', 'msg' => 'Gagal menyimpan data']);
    }
  }

  /**
   * Menghapus data (AJAX)
   */
  public function delete($id)
  {
    if (empty($id)) {
      echo json_encode(['status' => 'error', 'msg' => 'ID tidak valid']);
      return;
    }

    try {
      $this->M_Menu_Harian->delete($id);
      echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } catch (Exception $e) {
      log_message('error', 'Error delete menu harian: ' . $e->getMessage());
      echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus data.']);
    }
  }

  /**
   * Mengambil data customer (AJAX)
   */
  public function get_customers()
  {
    $data = $this->db->get('customer')->result_array();
    echo json_encode($data);
  }

  /**
   * Mengambil data kantin (AJAX)
   */
  public function get_kantins()
  {
    $data = $this->db->get('kantin')->result_array();
    echo json_encode($data);
  }

  /**
   * Mengambil data menu/komponen (AJAX)
   */
  public function get_menu_list()
  {
    // ✅ PERBAIKAN: Hapus duplikasi query
    $this->db->select('id_komponen, menu_nama');
    $this->db->from('menu');
    $this->db->where('status_aktif', 1); // Hanya menu aktif
    $data = $this->db->get()->result_array();

    echo json_encode($data);
  }

  public function get_kategori_by_menu()
  {
    $id_komponen = $this->input->post('id_komponen');
    if (!$id_komponen) {
      echo json_encode(['nama_kategori' => '']);
      return;
    }
    $this->db->select('km.nama_kategori');
    $this->db->from('menu m');
    $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori');
    $this->db->where('m.id_komponen', $id_komponen);
    $result = $this->db->get()->row();
    echo json_encode(['nama_kategori' => $result ? $result->nama_kategori : '']);
  }

  public function get_by_id($id)
  {
    if (empty($id)) {
      echo json_encode(['status' => 'error', 'msg' => 'ID tidak valid']);
      return;
    }

    // Ambil data menu harian
    $menu = $this->M_Menu_Harian->get_by_id($id);

    // Ambil data kondimen terkait
    $kondimen = $this->M_Menu_Harian->get_all_kondimen($id);

    if ($menu) {
      echo json_encode(['status' => 'success', 'data' => $menu, 'kondimen' => $kondimen]);
    } else {
      echo json_encode(['status' => 'error', 'msg' => 'Data tidak ditemukan']);
    }
  }

  public function get_kantin_by_customer()
  {
    $id_customer = $this->input->post('id_customer');
    $kantin = $this->db->get_where('kantin', ['id_customer' => $id_customer])->result_array();
    echo json_encode($kantin);
  }
}
