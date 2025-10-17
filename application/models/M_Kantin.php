<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Kantin extends CI_Model
{
  public function getAllKantin()
  {
    // Join customer untuk dapat nama customer
    $this->db->select('kantin.*, customer.nama_customer as customer_nama');
    $this->db->join('customer', 'customer.id_customer = kantin.id_customer', 'left');
    return $this->db->get('kantin')->result_array();
  }

  public function getKantinById($id)
  {
    $this->db->select('kantin.*, customer.nama_customer as customer_nama');
    $this->db->join('customer', 'customer.id_customer = kantin.id_customer', 'left');
    return $this->db->get_where('kantin', ['id_kantin' => $id])->row_array();
  }

  public function addKantin($data)
  {
    return $this->db->insert('kantin', $data);
  }

  public function updateKantin($id, $data)
  {
    $this->db->where('id_kantin', $id);
    return $this->db->update('kantin', $data);
  }

  public function deleteKantin($id)
  {
    $this->db->where('id_kantin', $id);
    return $this->db->delete('kantin');
  }
}
