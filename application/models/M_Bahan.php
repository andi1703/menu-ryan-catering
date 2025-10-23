<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Bahan extends CI_Model
{
    protected $table = 'bahan';
    protected $primary_key = 'id_bahan';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all bahan
     */
    public function get_all_bahan()
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get bahan by ID
     */
    public function get_bahan_by_id($id)
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->where('b.id_bahan', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Insert bahan baru
     */
    public function insert_bahan($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update bahan
     */
    public function update_bahan($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete bahan
     */
    public function delete_bahan($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Search bahan by keyword
     */
    public function search_bahan($keyword)
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->like('b.nama_bahan', $keyword);
        $this->db->order_by('b.nama_bahan', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all bahan sederhana untuk food cost
     */
    public function get_all_bahan_simple()
    {
        $this->db->select('b.id_bahan, b.nama_bahan, b.harga_awal, b.harga_sekarang, s.id_satuan, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result();
            // Tambahkan field harga_current untuk kompatibilitas dengan view
            foreach ($result as $row) {
                $row->harga_current = $row->harga_sekarang > 0 ? $row->harga_sekarang : $row->harga_awal;
            }
            return $result;
        }

        return array();
    }

    /**
     * Get total bahan count
     */
    public function count_all_bahan()
    {
        return $this->db->count_all_results($this->table);
    }

    /**
     * Check if bahan name exists
     */
    public function check_bahan_exists($nama_bahan, $id_exclude = null)
    {
        $this->db->where('nama_bahan', $nama_bahan);
        if ($id_exclude) {
            $this->db->where('id_bahan !=', $id_exclude);
        }
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }
}
