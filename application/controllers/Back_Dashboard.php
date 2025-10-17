<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ✅ UBAH NAMA CLASS SESUAI NAMA FILE
class Back_Dashboard extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->model('M_Dashboard');
  }

  public function index()
  {
    $data['title'] = 'Dashboard';

    // ✅ DEBUG - CEK STRUKTUR TABLE
    if ($this->input->get('debug')) {
      echo "<h3>Table Structure Debug:</h3>";
      echo "<h4>komponen_menu columns:</h4>";
      $columns = $this->M_Dashboard->getTableStructure('komponen_menu');
      echo "<pre>";
      print_r($columns);
      echo "</pre>";

      echo "<h4>kategori_menu columns:</h4>";
      $columns = $this->M_Dashboard->getTableStructure('kategori_menu');
      echo "<pre>";
      print_r($columns);
      echo "</pre>";

      echo "<h4>menu columns:</h4>";
      $columns = $this->M_Dashboard->getTableStructure('menu');
      echo "<pre>";
      print_r($columns);
      echo "</pre>";

      die();
    }

    // ✅ A. COUNT DATA
    $data['total_menu'] = $this->M_Dashboard->getTotalMenu();
    $data['total_indonesian'] = $this->M_Dashboard->getMenuByCategory('Indonesian Food');
    $data['total_western'] = $this->M_Dashboard->getMenuByCategory('Western Food');
    $data['total_chinese'] = $this->M_Dashboard->getMenuByCategory('Chinese Food');
    $data['total_japanese'] = $this->M_Dashboard->getMenuByCategory('Japanese Food');

    // ✅ B. TREN MENU TERPOPULER
    $data['trending_menu'] = $this->M_Dashboard->getTrendingMenu();
    $data['menu_by_category'] = $this->M_Dashboard->getMenuByCategories();
    $data['recent_menu'] = $this->M_Dashboard->getRecentMenu();

    // ✅ DATA GRAFIK
    $data['chart_categories'] = $this->M_Dashboard->getCategoryChart();
    $data['popular_ingredients'] = $this->M_Dashboard->getPopularIngredients();

    // ✅ LOAD VIEW
    $this->load->view('back/dashboard/V_Dashboard', $data);
  }
}
