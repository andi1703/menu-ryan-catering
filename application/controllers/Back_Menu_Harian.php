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
    $filter = [];
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
          'remark' => $item['remark'],
          'ids' => [],
          'kantins' => [],
          'kondimen_data' => [],
          'total_orderan' => 0,
          'created_at' => $item['created_at'] ?? null,
          'updated_at' => $item['updated_at'] ?? null
        ];
      }

      // Tambahkan ID dan kantin
      $grouped[$key]['ids'][] = $item['id_menu_harian'];
      $grouped[$key]['kantins'][] = $item['nama_kantin'];

      if (!empty($item['created_at'])) {
        if (empty($grouped[$key]['created_at']) || $item['created_at'] < $grouped[$key]['created_at']) {
          $grouped[$key]['created_at'] = $item['created_at'];
        }
      }

      if (!empty($item['updated_at'])) {
        if (empty($grouped[$key]['updated_at']) || $item['updated_at'] > $grouped[$key]['updated_at']) {
          $grouped[$key]['updated_at'] = $item['updated_at'];
        }
      }

      // ✅ AMBIL KONDIMEN UNTUK SETIAP MENU HARIAN
      $kondimen_list = $this->M_Menu_Harian->get_all_kondimen($item['id_menu_harian']);
      foreach ($kondimen_list as $k) {
        $kondimen_key = $k['id_komponen'];

        // Cari apakah kondimen sudah ada di grouped
        $found = false;
        foreach ($grouped[$key]['kondimen_data'] as &$existing_kondimen) {
          if ($existing_kondimen['id_komponen'] == $kondimen_key) {
            // Update qty per kantin
            if (!isset($existing_kondimen['qty_per_kantin'])) {
              $existing_kondimen['qty_per_kantin'] = [];
            }
            $existing_kondimen['qty_per_kantin'][$item['nama_kantin']] = $k['qty_kondimen'];
            $found = true;
            break;
          }
        }

        // Jika belum ada, tambahkan kondimen baru
        if (!$found) {
          $grouped[$key]['kondimen_data'][] = [
            'id_komponen' => $k['id_komponen'],
            'nama' => $k['nama_kondimen'],
            'kategori' => $k['kategori_kondimen'] ?? $k['nama_kategori'] ?? '-',
            'qty_per_kantin' => [
              $item['nama_kantin'] => $k['qty_kondimen']
            ]
          ];
        }
      }

      // ✅ PASTIKAN KANTIN UNIQUE
      $grouped[$key]['kantins'] = array_unique($grouped[$key]['kantins']);
    }

    // ✅ HITUNG TOTAL ORDERAN BERDASARKAN LAUK UTAMA - PERBAIKAN UTAMA
    foreach ($grouped as &$group) {
      // ✅ AMBIL DARI DATABASE (MANUAL INPUT)
      $first_id = isset($group['ids'][0]) ? $group['ids'][0] : null;
      if ($first_id) {
        $this->db->select('total_orderan_customer'); // ✅ SESUAIKAN NAMA FIELD
        $this->db->where('id_menu_harian', $first_id);
        $result = $this->db->get('menu_harian')->row();
        $group['total_orderan'] = $result ? (int)$result->total_orderan_customer : 0;
      } else {
        $group['total_orderan'] = 0;
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

    // ✅ CEK APAKAH INI OPERASI UPDATE ATAU INSERT
    $id_menu_harian = $this->input->post('id_menu_harian');
    $isUpdate = !empty($id_menu_harian);

    if ($isUpdate) {
      // ✅ OPERASI UPDATE
      $this->updateMenuHarian($data, $id_menu_harian);
    } else {
      // ✅ OPERASI INSERT BARU
      $this->insertMenuHarian($data);
    }
  }

  private function insertMenuHarian($data)
  {
    try {
      $this->db->trans_begin();

      $kondimen = json_decode($data['kondimen'], true);
      if (!$kondimen) {
        throw new Exception('Data kondimen tidak valid');
      }

      $id_kantins = isset($data['id_kantin']) ? (array)$data['id_kantin'] : [];
      if (empty($id_kantins)) {
        throw new Exception('Kantin harus dipilih');
      }

      // ✅ UBAH NAMA VARIABEL SESUAI DB
      $total_orderan_customer = $this->input->post('total_order_customer'); // dari form
      if (empty($total_orderan_customer)) {
        $total_orderan_customer = 0;
      }

      foreach ($id_kantins as $id_kantin) {
        $menu_data = [
          'tanggal' => $data['tanggal'],
          'shift' => $data['shift'],
          'id_customer' => $data['id_customer'],
          'id_kantin' => $id_kantin,
          'jenis_menu' => $data['jenis_menu'],
          'nama_menu' => $data['nama_menu'],
          'total_orderan_customer' => $total_orderan_customer, // ✅ SESUAIKAN DENGAN NAMA FIELD DB
          'remark' => $data['remark'] ?? null
        ];

        if (!$this->M_Menu_Harian->insert($menu_data)) {
          throw new Exception('Gagal insert menu harian');
        }

        $id_menu_harian = $this->db->insert_id();

        // Insert kondimen untuk menu harian ini
        foreach ($kondimen as $k) {
          if (empty($k['id_komponen'])) continue;

          $kondimen_data = [
            'id_menu_harian' => $id_menu_harian,
            'id_komponen' => $k['id_komponen'],
            'qty_kondimen' => isset($k['qty_per_kantin'][$id_kantin]) ? $k['qty_per_kantin'][$id_kantin] : 0
          ];

          if (!$this->M_Menu_Harian->insert_kondimen($kondimen_data)) {
            throw new Exception('Gagal insert kondimen');
          }
        }
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(['status' => 'error', 'msg' => 'Transaksi gagal']);
      } else {
        $this->db->trans_commit();
        echo json_encode(['status' => 'success', 'msg' => 'Menu harian berhasil ditambahkan!']);
      }
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Insert Menu Harian Error: ' . $e->getMessage());
      echo json_encode(['status' => 'error', 'msg' => 'Gagal menyimpan: ' . $e->getMessage()]);
    }
  }

  private function updateMenuHarian($data, $id_menu_harian)
  {
    try {
      $this->db->trans_begin();

      // ✅ AMBIL DATA LAMA
      $existing = $this->M_Menu_Harian->get_by_id($id_menu_harian);
      if (!$existing) {
        throw new Exception('Data tidak ditemukan');
      }

      $kondimen = json_decode($data['kondimen'], true);
      if (!$kondimen) {
        throw new Exception('Data kondimen tidak valid');
      }

      $id_kantins = isset($data['id_kantin']) ? (array)$data['id_kantin'] : [];
      if (empty($id_kantins)) {
        throw new Exception('Kantin harus dipilih');
      }

      // ✅ UBAH NAMA VARIABEL SESUAI DB
      $total_orderan_customer = $this->input->post('total_order_customer');
      if (empty($total_orderan_customer)) {
        $total_orderan_customer = 0;
      }

      // ✅ HAPUS DATA LAMA BERDASARKAN KOMBINASI YANG SAMA
      $this->db->select('id_menu_harian');
      $this->db->where('tanggal', $existing['tanggal']);
      $this->db->where('shift', $existing['shift']);
      $this->db->where('id_customer', $existing['id_customer']);
      $this->db->where('jenis_menu', $existing['jenis_menu']);
      $this->db->where('nama_menu', $existing['nama_menu']);
      $old_records = $this->db->get('menu_harian')->result_array();

      // Hapus kondimen lama terlebih dahulu
      foreach ($old_records as $record) {
        $this->M_Menu_Harian->delete_kondimen($record['id_menu_harian']);
      }

      // Hapus menu harian lama
      foreach ($old_records as $record) {
        $this->M_Menu_Harian->delete($record['id_menu_harian']);
      }

      // ✅ INSERT DATA BARU
      foreach ($id_kantins as $id_kantin) {
        $menu_data = [
          'tanggal' => $data['tanggal'],
          'shift' => $data['shift'],
          'id_customer' => $data['id_customer'],
          'id_kantin' => $id_kantin,
          'jenis_menu' => $data['jenis_menu'],
          'nama_menu' => $data['nama_menu'],
          'total_orderan_customer' => $total_orderan_customer, // ✅ SESUAIKAN DENGAN NAMA FIELD DB
          'remark' => $data['remark'] ?? null
        ];

        if (!$this->M_Menu_Harian->insert($menu_data)) {
          throw new Exception('Gagal insert menu harian untuk kantin ID: ' . $id_kantin);
        }

        $new_id_menu_harian = $this->db->insert_id();

        // Insert kondimen baru
        foreach ($kondimen as $k) {
          if (empty($k['id_komponen'])) continue;

          $qty_kondimen = 0;
          if (isset($k['qty_per_kantin'][$id_kantin]) && $k['qty_per_kantin'][$id_kantin] !== '') {
            $qty_kondimen = (int)$k['qty_per_kantin'][$id_kantin];
          }

          $kondimen_data = [
            'id_menu_harian' => $new_id_menu_harian,
            'id_komponen' => $k['id_komponen'],
            'qty_kondimen' => $qty_kondimen
          ];

          if (!$this->M_Menu_Harian->insert_kondimen($kondimen_data)) {
            $db_error = $this->db->error();
            throw new Exception('Gagal insert kondimen: ' . $db_error['message']);
          }
        }
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(['status' => 'error', 'msg' => 'Transaksi update gagal']);
      } else {
        $this->db->trans_commit();
        echo json_encode(['status' => 'success', 'msg' => 'Menu harian berhasil diperbarui!']);
      }
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Update Menu Harian Error: ' . $e->getMessage());
      echo json_encode(['status' => 'error', 'msg' => 'Gagal update: ' . $e->getMessage()]);
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
    header('Content-Type: application/json');

    try {
      if (!$id) {
        echo json_encode(['status' => 'error', 'msg' => 'ID tidak valid']);
        return;
      }

      $this->db->where('id_menu_harian', $id);
      $menu = $this->db->get('menu_harian')->row_array();

      if (!$menu) {
        echo json_encode(['status' => 'error', 'msg' => 'Data tidak ditemukan']);
        return;
      }

      $this->db->select('id_menu_harian, id_kantin, total_orderan_customer');
      $this->db->where('tanggal', $menu['tanggal']);
      $this->db->where('shift', $menu['shift']);
      $this->db->where('id_customer', $menu['id_customer']);
      $this->db->where('jenis_menu', $menu['jenis_menu']);
      $this->db->where('nama_menu', $menu['nama_menu']);
      $related_menus = $this->db->get('menu_harian')->result_array();

      $id_kantins = [];
      $total_orderan_customer = 0;

      foreach ($related_menus as $rm) {
        $id_kantins[] = (int)$rm['id_kantin'];

        if (empty($total_orderan_customer) && !empty($rm['total_orderan_customer'])) {
          $total_orderan_customer = (int)$rm['total_orderan_customer'];
        }
      }

      $menu['id_kantins'] = $id_kantins;
      $menu['total_orderan_customer'] = $total_orderan_customer;

      // ✅ PERBAIKI QUERY KONDIMEN - TAMBAHKAN KATEGORI
      $kondimen_grouped = [];

      foreach ($related_menus as $rm) {
        $this->db->select('mk.id_komponen, mk.qty_kondimen, m.menu_nama as nama_kondimen, km.nama_kategori as kategori_kondimen');
        $this->db->from('menu_harian_kondimen mk');
        $this->db->join('menu m', 'mk.id_komponen = m.id_komponen', 'left');
        $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori', 'left');
        $this->db->where('mk.id_menu_harian', $rm['id_menu_harian']);
        $kondimen_list = $this->db->get()->result_array();

        foreach ($kondimen_list as $k) {
          $key = $k['id_komponen'];

          if (!isset($kondimen_grouped[$key])) {
            $kondimen_grouped[$key] = [
              'id_komponen' => $k['id_komponen'],
              'nama_kondimen' => $k['nama_kondimen'] ?? '',
              'kategori_kondimen' => $k['kategori_kondimen'] ?? '', // ✅ PASTIKAN ADA
              'qty_per_kantin' => []
            ];
          }

          $kondimen_grouped[$key]['qty_per_kantin'][$rm['id_kantin']] = $k['qty_kondimen'];
        }
      }

      $kondimen_result = array_values($kondimen_grouped);

      echo json_encode([
        'status' => 'success',
        'data' => $menu,
        'kondimen' => $kondimen_result
      ]);
    } catch (Exception $e) {
      log_message('error', 'Error get_by_id: ' . $e->getMessage());
      echo json_encode([
        'status' => 'error',
        'msg' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function get_kantin_by_customer()
  {
    $id_customer = $this->input->post('id_customer');
    $kantin = $this->db->get_where('kantin', ['id_customer' => $id_customer])->result_array();
    echo json_encode($kantin);
  }
}
