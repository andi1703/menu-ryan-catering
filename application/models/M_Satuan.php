<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Satuan extends CI_Model
{
  public function getAllSatuan()
  {
    return $this->db->get('satuan')->result_array();
  }

  public function getSatuanById($id)
  {
    return $this->db->get_where('satuan', ['id_satuan' => $id])->row_array();
  }

  public function addSatuan($data)
  {
    return $this->db->insert('satuan', $data);
  }

  public function updateSatuan($id, $data)
  {
    $this->db->where('id_satuan', $id);
    return $this->db->update('satuan', $data);
  }

  public function deleteSatuan($id)
  {
    $this->db->where('id_satuan', $id);
    return $this->db->delete('satuan');
  }
}
