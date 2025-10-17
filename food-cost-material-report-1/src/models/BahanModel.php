<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BahanModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getAllBahan()
    {
        $query = $this->db->get('bahan');
        return $query->result();
    }

    public function getBahanById($id)
    {
        $query = $this->db->get_where('bahan', ['id_bahan' => $id]);
        return $query->row();
    }
}
?>