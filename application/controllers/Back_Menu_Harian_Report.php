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

    // Ambil data customer dan kantin untuk dropdown
    $data['customerList'] = $this->db->get('customer')->result_array();
    $data['kantinList'] = $this->db->select('nama_kantin')->get('kantin')->result_array();

    // Ambil data flat dari model
    $result = $this->M_Menu_Harian->get_laporan_kondimen($filter);
    if (!is_array($result)) $result = [];

    // Pivot data di controller
    $kantinList = [];
    $pivot = [];
    foreach ($result as $row) {
      if (!in_array($row['nama_kantin'], $kantinList)) {
        $kantinList[] = $row['nama_kantin'];
      }
      $key = $row['menu_kondimen'] . '|' . $row['kategori'];
      if (!isset($pivot[$key])) {
        $pivot[$key] = [
          'menu_kondimen' => $row['menu_kondimen'],
          'kategori' => $row['kategori'],
          'qty_per_kantin' => [],
          'total' => 0
        ];
      }
      $pivot[$key]['qty_per_kantin'][$row['nama_kantin']] = $row['qty'];
      $pivot[$key]['total'] += $row['qty'];
    }

    $data['kantinList'] = $kantinList;
    $data['pivot'] = $pivot;
    $this->load->view('back/Report_Daily_Menu/V_Menu_Harian_Report', $data);
  }
}
