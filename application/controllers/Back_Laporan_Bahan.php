<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Back_Laporan_Bahan extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_Laporan_Bahan');
  }

  /**
   * Halaman utama laporan kebutuhan bahan baku
   */
  public function index()
  {
    $this->load->view('back/laporan_bahan/V_Laporan_Bahan');
  }

  /**
   * Ambil data rekap kebutuhan bahan baku (AJAX)
   */
  public function ajax_list()
  {
    $filter = [
      'tanggal' => $this->input->get('tanggal'),
      'shift' => $this->input->get('shift'),
      'id_kantin' => $this->input->get('id_kantin'),
      'id_customer' => $this->input->get('id_customer')
    ];
    $data = $this->M_Laporan_Bahan->get_rekap_bahan($filter);
    echo json_encode(['show_data' => $data]);
  }
}
