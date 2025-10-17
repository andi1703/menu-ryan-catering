<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Country extends CI_Model
{
  private $table = 'country';

  public function getAllCountry()
  {
    $this->db->order_by('urutan_tampil', 'ASC');
    $this->db->order_by('country_nama', 'ASC');
    return $this->db->get($this->table)->result_array();
  }

  public function getCountryById($id)
  {
    $this->db->where('id_country', $id);
    return $this->db->get($this->table)->row_array();
  }

  public function addCountry($data)
  {
    return $this->db->insert($this->table, $data);
  }

  public function updateCountry($id, $data)
  {
    $this->db->where('id_country', $id);
    return $this->db->update($this->table, $data);
  }

  public function deleteCountry($id)
  {
    $this->db->where('id_country', $id);
    return $this->db->delete($this->table);
  }

  public function checkCountryNameExists($country_nama, $id = null)
  {
    $this->db->where('country_nama', $country_nama);
    if ($id != null) {
      $this->db->where('id_country !=', $id);
    }
    $query = $this->db->get($this->table);
    return $query->num_rows() > 0;
  }

  public function getMaxUrutan()
  {
    $this->db->select_max('urutan_tampil');
    $query = $this->db->get($this->table);
    $result = $query->row();
    return $result->urutan_tampil ? $result->urutan_tampil : 0;
  }
}
