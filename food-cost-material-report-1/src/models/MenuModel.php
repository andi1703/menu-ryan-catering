<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MenuModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMenuItems()
    {
        $this->db->select('*');
        $this->db->from('menu');
        $query = $this->db->get();
        return $query->result();
    }

    public function getMenuById($id)
    {
        $this->db->select('*');
        $this->db->from('menu');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
}
?>