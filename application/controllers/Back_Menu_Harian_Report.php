<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Menu_Harian_Report extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Menu_Harian');
  }

  /**
   * Halaman utama report
   */
  public function index()
  {
    // Ambil filter dari GET
    $filter = [
      'shift' => $this->input->get('shift'),
      'tanggal' => $this->input->get('tanggal'),
      'id_customer' => $this->input->get('id_customer'),
      'id_kantin' => $this->input->get('id_kantin')
    ];

    // Ambil data customer untuk dropdown
    $data['customerList'] = $this->db->get('customer')->result_array();

    // Ambil data flat dari model
    $result = $this->M_Menu_Harian->get_laporan_kondimen($filter);
    if (!is_array($result)) $result = [];

    // INISIALISASI groupedByCustomer
    $groupedByCustomer = [];

    // Jika ada data, lakukan grouping
    if (count($result) > 0) {
      foreach ($result as $row) {
        // Ambil info customer dari kantin
        $kantin_info = $this->db->select('customer.id_customer, customer.nama_customer')
          ->from('kantin')
          ->join('customer', 'kantin.id_customer = customer.id_customer')
          ->where('kantin.nama_kantin', $row['nama_kantin'])
          ->get()
          ->row_array();

        if ($kantin_info) {
          $customerId = $kantin_info['id_customer'];
          $customerName = $kantin_info['nama_customer'];

          // Inisialisasi customer jika belum ada
          if (!isset($groupedByCustomer[$customerId])) {
            $groupedByCustomer[$customerId] = [
              'customer_id' => $customerId,
              'customer_name' => $customerName,
              'kantins' => [],
              'menu_data' => []
            ];
          }

          // Tambahkan kantin jika belum ada
          if (!in_array($row['nama_kantin'], $groupedByCustomer[$customerId]['kantins'])) {
            $groupedByCustomer[$customerId]['kantins'][] = $row['nama_kantin'];
          }

          // Pivot data per menu
          $menuKey = $row['nama_menu'] . '|' . $row['jenis_menu'] . '|' . $row['nama_kondimen'] . '|' . $row['kategori'];
          if (!isset($groupedByCustomer[$customerId]['menu_data'][$menuKey])) {
            $groupedByCustomer[$customerId]['menu_data'][$menuKey] = [
              'nama_menu'      => $row['nama_menu'],
              'jenis_menu'     => $row['jenis_menu'],
              'menu_kondimen'  => $row['nama_kondimen'],
              'kategori'       => $row['kategori'],
              'qty_per_kantin' => []
            ];
          }

          $groupedByCustomer[$customerId]['menu_data'][$menuKey]['qty_per_kantin'][$row['nama_kantin']] = $row['qty_kondimen'];
        }
      }

      // Hitung total per menu di setiap customer
      foreach ($groupedByCustomer as &$customer) {
        foreach ($customer['menu_data'] as &$menu) {
          $menu['total'] = array_sum($menu['qty_per_kantin']);
        }
      }

      // Setelah proses pivot dan hitung total
      foreach ($groupedByCustomer as &$customer) {
        // Group menu kondimen menjadi menu utama dengan kondimen_list
        $menuGroup = [];
        $grandTotal = 0; // Tambahkan ini
        foreach ($customer['menu_data'] as $menu) {
          $key = $menu['nama_menu'] . '|' . $menu['jenis_menu'];
          if (!isset($menuGroup[$key])) {
            $menuGroup[$key] = [
              'nama_menu' => $menu['nama_menu'],
              'jenis_menu' => $menu['jenis_menu'],
              'kondimen_list' => []
            ];
          }
          $menuGroup[$key]['kondimen_list'][] = [
            'nama_kondimen' => $menu['menu_kondimen'],
            'kategori' => $menu['kategori'],
            'qty_per_kantin' => $menu['qty_per_kantin'],
            'total' => $menu['total']
          ];

          // Hitung grand total hanya untuk kategori Lauk Utama
          if (strtolower($menu['kategori']) == 'lauk utama') {
            $grandTotal += $menu['total'];
          }
        }
        // Replace menu_data dengan hasil group
        $customer['menu_data'] = array_values($menuGroup);
        $customer['grand_total_order'] = $grandTotal; // Tambahkan ini
      }
    }

    $data['groupedByCustomer'] = $groupedByCustomer;
    $data['filter'] = $filter;

    $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report', $data);
  }

  /**
   * Generate PDF Report
   */
  public function generate_pdf()
  {
    // Load library PDF
    $this->load->library('pdf');

    // Ambil filter dari GET
    $filter = [
      'shift' => $this->input->get('shift'),
      'tanggal' => $this->input->get('tanggal'),
      'id_customer' => $this->input->get('id_customer'),
      'id_kantin' => $this->input->get('id_kantin')
    ];

    // Ambil data flat dari model (SUDAH TERFILTER)
    $result = $this->M_Menu_Harian->get_laporan_kondimen($filter);
    if (!is_array($result)) $result = [];

    // Group data by Customer (sama seperti index)
    $groupedByCustomer = [];

    if (count($result) > 0) {
      foreach ($result as $row) {
        // Ganti ini:
        // $row['menu_kondimen']
        // $row['qty']

        // Jadi:
        $row['nama_kondimen'];
        $row['qty_kondimen'];

        $kantin_info = $this->db->select('customer.id_customer, customer.nama_customer')
          ->from('kantin')
          ->join('customer', 'kantin.id_customer = customer.id_customer')
          ->where('kantin.nama_kantin', $row['nama_kantin'])
          ->get()
          ->row_array();

        if ($kantin_info) {
          $customerId = $kantin_info['id_customer'];
          $customerName = $kantin_info['nama_customer'];

          if (!isset($groupedByCustomer[$customerId])) {
            $groupedByCustomer[$customerId] = [
              'customer_id' => $customerId,
              'customer_name' => $customerName,
              'kantins' => [],
              'menu_data' => []
            ];
          }

          if (!in_array($row['nama_kantin'], $groupedByCustomer[$customerId]['kantins'])) {
            $groupedByCustomer[$customerId]['kantins'][] = $row['nama_kantin'];
          }

          $menuKey = $row['nama_menu'] . '|' . $row['jenis_menu'] . '|' . $row['nama_kondimen'] . '|' . $row['kategori'];
          if (!isset($groupedByCustomer[$customerId]['menu_data'][$menuKey])) {
            $groupedByCustomer[$customerId]['menu_data'][$menuKey] = [
              'nama_menu'      => $row['nama_menu'],
              'jenis_menu'     => $row['jenis_menu'],
              'menu_kondimen'  => $row['nama_kondimen'],
              'kategori'       => $row['kategori'],
              'qty_per_kantin' => []
            ];
          }

          $groupedByCustomer[$customerId]['menu_data'][$menuKey]['qty_per_kantin'][$row['nama_kantin']] = $row['qty_kondimen'];
        }
      }

      foreach ($groupedByCustomer as &$customer) {
        foreach ($customer['menu_data'] as &$menu) {
          $menu['total'] = array_sum($menu['qty_per_kantin']);
        }
      }
    }

    $data['groupedByCustomer'] = $groupedByCustomer;
    $data['filter'] = $filter;

    // Load view untuk PDF
    $html = $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report_PDF', $data, true);

    // Generate PDF
    $this->pdf->loadHtml($html);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();

    // Output PDF
    $filename = 'Laporan_Menu_Harian_' . date('Y-m-d_His') . '.pdf';
    $this->pdf->stream($filename, array("Attachment" => 0));
  }
}
