<?php
class M_crud extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function tampil_data($table)
	{
		return $this->db->get($table);
	}

	function tampil_data_where($table, $where)
	{
		return $this->db->get_where($table, $where);
	}

	function tampil_data_page($table, $number, $offset)
	{
		$this->db->limit($number, $offset);
		return $this->db->get($table);
	}

	function ins_data($tabel, $data)
	{
		return $this->db->insert($tabel, $data);
	}

	function upd_data($tabel, $data, $where)
	{
		return $this->db->update($tabel, $data, $where);
	}

	function del_data($tabel, $where)
	{
		return $this->db->delete($tabel, $where);
	}

	function _select($colom)
	{
		return $this->db->select($colom);
	}

	// function _get($table)
	// {
	// 	return $this->db->get($table);
	// }

	// Add 09/05/2024
	function _get($table, $where = array(), $limit = null, $offset = null, $order_by = null)
	{
		if (!empty($where)) {
			$this->db->where($where);
		}

		if ($limit !== null) {
			$this->db->limit($limit, $offset);
		}

		if ($order_by !== null) {
			$this->db->order_by($order_by);
		}

		return $this->db->get($table);
	}

	function _where($colom, $param)
	{
		return $this->db->where($colom, $param);
	}

	function _group_start()
	{
		return $this->db->group_start();
	}


	function _or_group_start()
	{
		return $this->db->or_group_start();
	}

	function _group_end()
	{
		return $this->db->group_end();
	}

	function _or_group_end()
	{
		return $this->db->or_group_end();
	}

	function _or_where($colom, $param)
	{
		return $this->db->or_where($colom, $param);
	}

	function _where_in($where_in)
	{
		return $this->db->where_in($where_in);
	}

	function _group_by($param)
	{
		return $this->db->group_by($param);
	}

	function _query($query)
	{
		return $this->db->query($query);
	}

	function _order_by($order, $value)
	{
		return $this->db->order_by($order, $value);
	}

	function _limit($limit)
	{
		return $this->db->limit($limit);
	}

	function _select_max($colom, $table)
	{
		return $this->db->select_max($colom)->get($table);
	}

	function compile_sql($query)
	{
		return $this->db->last_query($query);
	}

	function id($table, $id, $kode)
	{
		$this->db->select_max('substr(' . $id . ', 3,3)', 'kd_max');
		$q = $this->db->get($table);
		$kd = "";
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int)$k->kd_max) + 1;
				$kd = sprintf("%03s", $tmp);
			}
		} else {
			$kd = "001";
		}
		return $kode . $kd;
	}

	function id_5_numeric_day($table, $id, $colom)
	{
		// AND DATE(`a`.`confirm_date`) = '$today'
		$date  = date('Y-m-d');
		// $this->db->select_max(''.$id.'','kd_max');
		$this->db->select_max('substr(' . $id . ', 7,5)', 'kd_max');
		$this->db->where('date(' . $colom . ')=', $date);
		$q = $this->db->get($table);

		$kd = "";
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int)$k->kd_max) + 1;
				$kd = sprintf("%05s", $tmp);
			}
		} else {
			$kd = "00001";
		}
		date_default_timezone_set('Asia/Jakarta');
		return date('ymd') . $kd;
	}

	function id_num_year($table, $id, $colom)
	{
		$year = date('Y');
		$this->db->select_max('substr(' . $id . ', 5,4)', 'kd_max');
		$this->db->where('extract(year from ' . $colom . ')=', $year);
		$q = $this->db->get($table);
		$kd = "";
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int)$k->kd_max) + 1;
				$kd = sprintf("%03s", $tmp);
			}
		} else {
			$kd = "0001";
		}
		return $year . $kd;
	}

	function id_num_month_year($table, $id, $colom)
	{
		$year = date('Y');
		$month = date('m');
		$this->db->select_max('substr(' . $id . ', 7,4)', 'kd_max');
		$this->db->where('extract(year from ' . $colom . ')=', $year);
		$this->db->where('extract(month from ' . $colom . ')=', $month);
		$q = $this->db->get($table);
		$kd = "";
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int)$k->kd_max) + 1;
				$kd = sprintf("%04s", $tmp);
			}
		} else {
			$kd = "0001";
		}
		return $year . $month . $kd;
	}
}
