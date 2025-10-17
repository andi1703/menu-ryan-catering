<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Back_login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('M_crud');
	}



	public function index()
	{
		$line = $this->M_crud->tampil_data_where('users', array('active' => 1))->result_array();
		$data = array(
			'line'      => $line,
			'title_bar' => 'Login Form',
		);
		$this->load->view('form_login', $data);
	}
}
