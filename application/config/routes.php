<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ===== MENU ROUTES =====
$route['menu'] = 'back_menu/index';
$route['menu/tampil'] = 'back_menu/tampil';
$route['menu/get_dropdown_data'] = 'back_menu/get_dropdown_data';
$route['menu/save_data'] = 'back_menu/save_data';
$route['menu/edit_data'] = 'back_menu/edit_data';
$route['menu/delete_data'] = 'back_menu/delete_data';

// ===== KATEGORI MENU ROUTES =====
$route['kategori-menu'] = 'back_kategori_menu/index';
$route['kategori-menu/tampil'] = 'back_kategori_menu/tampil';
$route['kategori-menu/save'] = 'Back_Kategori_Menu/save_data';
$route['kategori-menu/edit'] = 'back_kategori_menu/edit_data';
$route['kategori-menu/delete'] = 'Back_Kategori_Menu/delete_data';

// ===== THEMATIK ROUTES =====
$route['thematik'] = 'Back_Thematik/index';
$route['thematik/get_data'] = 'Back_Thematik/get_data_thematik';
$route['thematik/get_by_id/(:num)'] = 'Back_Thematik/get_data_thematik_by_id/$1';
$route['thematik/save'] = 'Back_Thematik/tambah_data_thematik';
$route['thematik/edit'] = 'Back_Thematik/edit_data_thematik';
$route['thematik/delete'] = 'Back_Thematik/hapus_data_thematik';

// ===== CUSTOMER ROUTES =====
$route['customer'] = 'back_customer/index';
$route['customer/tampil'] = 'customer/tampil';
$route['customer/save'] = 'customer/save_data';
$route['customer/edit'] = 'customer/edit_data';
$route['customer/delete'] = 'customer/delete_data';

// ===== KANTIN ROUTES =====
$route['kantin'] = 'back_kantin/index';
$route['kantin/tampil'] = 'back_kantin/get_data_kantin';
$route['kantin/save'] = 'back_kantin/save_data';
$route['kantin/edit'] = 'back_kantin/edit_data';
$route['kantin/delete'] = 'back_kantin/delete_data';

// ===== SATUAN ROUTES =====
$route['satuan'] = 'Back_Satuan/index';
$route['satuan/get_data'] = 'Back_Satuan/get_data_satuan';
$route['satuan/get_by_id'] = 'Back_Satuan/get_satuan_by_id';
$route['satuan/save'] = 'Back_Satuan/save_data';
$route['satuan/delete'] = 'Back_Satuan/delete_data';

// ===== BAHAN ROUTES =====
$route['bahan'] = 'Back_Bahan/index';
$route['bahan/get_data'] = 'Back_Bahan/get_data_bahan';
$route['bahan/get_by_id'] = 'Back_Bahan/get_bahan_by_id';
$route['bahan/save'] = 'Back_Bahan/save_data';
$route['bahan/delete'] = 'Back_Bahan/delete_data';

// ===== MENU REGULAR ROUTES =====
$route['regular-menu'] = 'Back_Menu_Regular/index';
$route['regular-menu/tampil'] = 'Back_Menu_Regular/tampil';
$route['regular-menu/save_data'] = 'Back_Menu_Regular/save_data';
$route['regular-menu/get_regular_menu_by_id'] = 'Back_Menu_Regular/get_regular_menu_by_id';
$route['regular-menu/delete_data'] = 'Back_Menu_Regular/delete_data';

// ===== FOOD COST ROUTES =====
$route['food-cost'] = 'Back_Food_Cost/index';
$route['food-cost/get_data'] = 'Back_Food_Cost/get_data';
$route['food-cost/get_by_id'] = 'Back_Food_Cost/get_by_id';
$route['food-cost/save_data'] = 'Back_Food_Cost/save_data';
$route['food-cost/delete_data'] = 'Back_Food_Cost/delete_data';
$route['food-cost/calculate_cost'] = 'Back_Food_Cost/calculate_cost';
$route['food-cost/get_stats'] = 'Back_Food_Cost/get_stats';
$route['food-cost/debug'] = 'Back_Food_Cost/debug_setup';
$route['food-cost/search_bahan'] = 'Back_Food_Cost/search_bahan';
$route['food-cost/get_detail_bahan'] = 'Back_Food_Cost/get_detail_bahan';

// ===== LAPORAN BAHAN BAKU ROUTES =====
$route['laporan-bahan'] = 'Back_Laporan_Bahan/index';
$route['laporan-bahan/get_laporan'] = 'Back_Laporan_Bahan/get_laporan';
$route['laporan-bahan/get_summary_per_menu'] = 'Back_Laporan_Bahan/get_summary_per_menu';
$route['laporan-bahan/export_excel'] = 'Back_Laporan_Bahan/export_excel';
$route['laporan-bahan/export_pdf'] = 'Back_Laporan_Bahan/export_pdf';
$route['laporan-bahan/save_manual_input'] = 'Back_Laporan_Bahan/save_manual_input';
$route['laporan-bahan/get_template'] = 'Back_Laporan_Bahan/get_template';

// ===== SHIFT BAHAN ROUTES =====
$route['shift-bahan'] = 'Back_Shift_Bahan/index';
$route['shift-bahan/input/(:any)'] = 'Back_Shift_Bahan/input_data/$1';
$route['shift-bahan/input'] = 'Back_Shift_Bahan/input_data';
$route['shift-bahan/save_data'] = 'Back_Shift_Bahan/save_data';
$route['shift-bahan/get_data'] = 'Back_Shift_Bahan/get_data';
$route['shift-bahan/export_excel/(:any)'] = 'Back_Shift_Bahan/export_excel/$1';
$route['shift-bahan/delete_data'] = 'Back_Shift_Bahan/delete_data';
$route['shift-bahan/approve_data'] = 'Back_Shift_Bahan/approve_data';
$route['shift-bahan/load_template'] = 'Back_Shift_Bahan/load_template';
$route['shift-bahan/report'] = 'Back_Shift_Bahan/get_bahan_usage_report';

// ===== MENU HARIAN ROUTES =====
$route['menu-harian'] = 'Back_Menu_Harian/index';
$route['menu-harian/save'] = 'Back_Menu_Harian/save';
$route['menu-harian/delete/(:num)'] = 'Back_Menu_Harian/delete/$1';
$route['menu-harian/get_by_id/(:num)'] = 'Back_Menu_Harian/get_by_id/$1';
$route['menu-harian/get_customers'] = 'Back_Menu_Harian/get_customers';
$route['menu-harian/get_kantins'] = 'Back_Menu_Harian/get_kantins';
$route['menu-harian/get_menu_list'] = 'Back_Menu_Harian/get_menu_list';
$route['menu-harian/ajax_list'] = 'Back_Menu_Harian/ajax_list';

// menu harian report
$route['menu-harian-report'] = 'Back_Menu_Harian_Report/index';
$route['menu-harian-report/generate_pdf'] = 'Back_Menu_Harian_Report/generate_pdf';
$route['menu-harian-report/generate_excel'] = 'Back_Menu_Harian_Report/generate_excel';

// ===== DASHBOARD =====
$route['dashboard'] = 'back_dashboard/index';
