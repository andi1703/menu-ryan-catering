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
   * Normalise id_kantin filter into an int array.
   */
  private function getKantinFilter(): array
  {
    $raw = $this->input->get('id_kantin');

    if (is_array($raw)) {
      $values = array_map('intval', $raw);
    } elseif ($raw !== null && $raw !== '') {
      $values = [(int) $raw];
    } else {
      return [];
    }

    return array_values(array_filter($values, function ($id) {
      return $id > 0;
    }));
  }

  /**
   * Build grouped report data shared by HTML/PDF/Excel views.
   */
  private function buildGroupedData(array $filter): array
  {
    $result = $this->M_Menu_Harian->get_laporan_kondimen($filter);
    if (!is_array($result) || count($result) === 0) {
      return [];
    }

    $groupedByCustomer = [];

    foreach ($result as $row) {
      $kantinInfo = $this->db->select('customer.id_customer, customer.nama_customer')
        ->from('kantin')
        ->join('customer', 'kantin.id_customer = customer.id_customer')
        ->where('kantin.nama_kantin', $row['nama_kantin'])
        ->get()
        ->row_array();

      if (!$kantinInfo) {
        continue;
      }

      $customerId = (int) $kantinInfo['id_customer'];
      $customerName = $kantinInfo['nama_customer'];

      if (!isset($groupedByCustomer[$customerId])) {
        $groupedByCustomer[$customerId] = [
          'customer_id' => $customerId,
          'customer_name' => $customerName,
          'kantins' => [],
          'menu_data' => []
        ];
      }

      if (!in_array($row['nama_kantin'], $groupedByCustomer[$customerId]['kantins'], true)) {
        $groupedByCustomer[$customerId]['kantins'][] = $row['nama_kantin'];
      }

      $menuKey = implode('|', [
        $row['nama_menu'],
        $row['jenis_menu'],
        $row['shift'],
        $row['nama_kondimen'],
        $row['kategori']
      ]);

      if (!isset($groupedByCustomer[$customerId]['menu_data'][$menuKey])) {
        $groupedByCustomer[$customerId]['menu_data'][$menuKey] = [
          'nama_menu' => $row['nama_menu'],
          'jenis_menu' => $row['jenis_menu'],
          'menu_kondimen' => $row['nama_kondimen'],
          'kategori' => $row['kategori'],
          'shift' => $row['shift'],
          'qty_per_kantin' => []
        ];
      }

      $groupedByCustomer[$customerId]['menu_data'][$menuKey]['qty_per_kantin'][$row['nama_kantin']] = (int) $row['qty_kondimen'];
    }

    foreach ($groupedByCustomer as &$customer) {
      foreach ($customer['menu_data'] as &$menu) {
        $menu['total'] = array_sum($menu['qty_per_kantin']);
      }
      unset($menu);
    }
    unset($customer);

    foreach ($groupedByCustomer as $customerId => &$customer) {
      $menuGroup = [];

      foreach ($customer['menu_data'] as $menu) {
        $currentShift = isset($menu['shift']) ? $menu['shift'] : '';
        $key = implode('|', [$menu['nama_menu'], $menu['jenis_menu'], $currentShift]);

        if (!isset($menuGroup[$key])) {
          $menuGroup[$key] = [
            'nama_menu' => $menu['nama_menu'],
            'jenis_menu' => $menu['jenis_menu'],
            'shift' => $currentShift,
            'kondimen_list' => [],
            'total_order_customer' => 0
          ];
        }

        $menuGroup[$key]['kondimen_list'][] = [
          'nama_kondimen' => $menu['menu_kondimen'],
          'kategori' => $menu['kategori'],
          'qty_per_kantin' => $menu['qty_per_kantin'],
          'total' => $menu['total']
        ];
      }

      foreach ($menuGroup as $key => &$menuData) {
        $this->db->select('total_orderan_customer');
        $this->db->where('id_customer', $customerId);
        $this->db->where('nama_menu', $menuData['nama_menu']);
        $this->db->where('jenis_menu', $menuData['jenis_menu']);
        if (!empty($filter['tanggal'])) {
          $this->db->where('tanggal', $filter['tanggal']);
        }
        if (!empty($filter['shift']) && strtoupper($filter['shift']) !== 'SEMUA') {
          $this->db->where('shift', $filter['shift']);
        } elseif (!empty($menuData['shift'])) {
          $this->db->where('shift', $menuData['shift']);
        }
        $this->db->limit(1);
        $resultTotal = $this->db->get('menu_harian')->row_array();

        $orderValue = !empty($resultTotal['total_orderan_customer'])
          ? (int) $resultTotal['total_orderan_customer']
          : 0;

        $menuData['total_order_customer'] = $orderValue;
        $menuData['total_orderan'] = $orderValue;
      }
      unset($menuData);

      $customer['menu_data'] = array_values($menuGroup);
      $customer['grand_total_order'] = array_sum(array_column($customer['menu_data'], 'total_order_customer'));
    }
    unset($customer);

    return $groupedByCustomer;
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
      'id_kantin' => $this->getKantinFilter()
    ];

    $hasFilter = $this->input->get('apply_filter') === '1';

    // Ambil data customer untuk dropdown
    $data['customerList'] = $this->db->get('customer')->result_array();

    // Ambil data flat dari model
    $groupedByCustomer = $hasFilter ? $this->buildGroupedData($filter) : [];

    // Hitung grand total order keseluruhan
    $grandTotalOrderAll = 0;
    foreach ($groupedByCustomer as $customer) {
      $grandTotalOrderAll += isset($customer['grand_total_order']) ? (int)$customer['grand_total_order'] : 0;
    }

    $data['groupedByCustomer'] = $groupedByCustomer;
    $data['filter'] = $filter;
    $data['hasFilter'] = $hasFilter;
    $data['grandTotalOrderAll'] = $grandTotalOrderAll;

    $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report', $data);
  }

  /**
   * Generate PDF Report - UPDATE LOGIC
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
      'id_kantin' => $this->getKantinFilter()
    ];

    $groupedByCustomer = $this->buildGroupedData($filter);

    // Hitung grand total order keseluruhan
    $grandTotalOrderAll = 0;
    foreach ($groupedByCustomer as $customer) {
      $grandTotalOrderAll += isset($customer['grand_total_order']) ? (int)$customer['grand_total_order'] : 0;
    }

    $data['groupedByCustomer'] = $groupedByCustomer;
    $data['filter'] = $filter;
    $data['grandTotalOrderAll'] = $grandTotalOrderAll;

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

  /**
   * Export report to Excel.
   */
  public function generate_excel()
  {
    $filter = [
      'shift' => $this->input->get('shift'),
      'tanggal' => $this->input->get('tanggal'),
      'id_customer' => $this->input->get('id_customer'),
      'id_kantin' => $this->getKantinFilter()
    ];

    $groupedByCustomer = $this->buildGroupedData($filter);

    // Hitung grand total order keseluruhan
    $grandTotalOrderAll = 0;
    foreach ($groupedByCustomer as $customer) {
      $grandTotalOrderAll += isset($customer['grand_total_order']) ? (int)$customer['grand_total_order'] : 0;
    }

    $data = [
      'groupedByCustomer' => $groupedByCustomer,
      'filter' => $filter,
      'grandTotalOrderAll' => $grandTotalOrderAll
    ];

    $html = $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report_Excel', $data, true);

    $filename = 'Laporan_Menu_Harian_' . date('Y-m-d_His') . '.xls';

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    echo $html;
    exit;
  }
}
