<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Satuan extends CI_Model
{

    public function getAllSatuan()
    {
        $this->db->select('*');
        $this->db->from('satuan');
        $this->db->order_by('nama_satuan', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getSatuanById($id)
    {
        $this->db->where('id_satuan', $id);
        $query = $this->db->get('satuan');
        return $query->row_array();
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
