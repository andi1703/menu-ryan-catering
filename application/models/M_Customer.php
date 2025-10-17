<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Customer extends CI_Model
{
  public function getAllCustomer()
  {
    return $this->db->get('customer')->result_array();
  }

  public function getCustomerById($id)
  {
    return $this->db->get_where('customer', ['id_customer' => $id])->row_array();
  }

  public function addCustomer($data)
  {
    return $this->db->insert('customer', $data);
  }

  public function updateCustomer($id, $data)
  {
    $this->db->where('id_customer', $id);
    return $this->db->update('customer', $data);
  }

  public function deleteCustomer($id)
  {
    $this->db->where('id_customer', $id);
    return $this->db->delete('customer');
  }
}
