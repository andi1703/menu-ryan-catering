<?php
// File: application/models/M_Bahan.php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Bahan extends CI_Model
{

    private $table = 'bahan';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get semua bahan dengan join satuan
     */
    public function get_all_bahan()
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get bahan by ID
     */
    public function get_by_id($id)
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->where('b.id_bahan', $id);
        return $this->db->get()->row();
    }

    /**
     * Search bahan
     */
    public function search_bahan($keyword)
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->like('b.nama_bahan', $keyword);
        $this->db->order_by('b.nama_bahan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get bahan dengan harga terbaru - PERBAIKAN SYNTAX
     */
    public function get_bahan_with_current_price()
    {
        // Gunakan query builder yang benar
        $this->db->select('b.id_bahan, b.nama_bahan, b.id_satuan, b.harga_awal, b.harga_sekarang, b.created_at, s.nama_satuan');
        $this->db->select('(CASE WHEN b.harga_sekarang > 0 THEN b.harga_sekarang ELSE b.harga_awal END) as harga_current');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Alternative method - Gunakan raw query jika perlu
     */
    public function get_bahan_with_price_alternative()
    {
        $sql = "SELECT b.*, s.nama_satuan,
                CASE 
                    WHEN b.harga_sekarang > 0 THEN b.harga_sekarang 
                    ELSE b.harga_awal 
                END as harga_current
                FROM bahan b 
                LEFT JOIN satuan s ON b.id_satuan = s.id_satuan 
                ORDER BY b.nama_bahan ASC";

        return $this->db->query($sql)->result();
    }

    /**
     * Simplest method - Process harga_current in PHP
     */
    public function get_all_bahan_simple()
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        $result = $this->db->get()->result();

        // Process harga_current di PHP
        foreach ($result as $bahan) {
            $bahan->harga_current = ($bahan->harga_sekarang > 0) ? $bahan->harga_sekarang : $bahan->harga_awal;
        }

        return $result;
    }

    /**
     * Legacy methods for compatibility
     */
    public function getAllBahan()
    {
        return $this->get_all_bahan_as_array();
    }

    public function get_all_bahan_as_array()
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getBahanById($id)
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->where('b.id_bahan', $id);
        return $this->db->get()->row_array();
    }

    /**
     * Check duplicate name for validation
     */
    public function checkDuplicateName($nama_bahan, $exclude_id = null)
    {
        $this->db->where('nama_bahan', $nama_bahan);
        if ($exclude_id) {
            $this->db->where('id_bahan !=', $exclude_id);
        }
        return $this->db->get($this->table)->row();
    }

    /**
     * Add new bahan
     */
    public function addBahan($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update bahan
     */
    public function updateBahan($id, $data)
    {
        $this->db->where('id_bahan', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete bahan
     */
    public function deleteBahan($id)
    {
        // Check if bahan is used in menu_regular_bahan
        $this->db->where('id_bahan', $id);
        $usage_check = $this->db->get('menu_regular_bahan')->num_rows();

        if ($usage_check > 0) {
            return ['status' => 'error', 'message' => 'Bahan tidak dapat dihapus karena masih digunakan dalam menu'];
        }

        $this->db->where('id_bahan', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get bahan statistics
     */
    public function getBahanStats()
    {
        // Total bahan
        $total_bahan = $this->db->count_all($this->table);

        // Bahan dengan harga naik
        $this->db->where('harga_sekarang > harga_awal');
        $harga_naik = $this->db->count_all_results($this->table);

        // Average price
        $this->db->select_avg('harga_sekarang');
        $avg_price = $this->db->get($this->table)->row()->harga_sekarang;

        return [
            'total_bahan' => $total_bahan,
            'harga_naik' => $harga_naik,
            'avg_price' => $avg_price ?: 0
        ];
    }

    /**
     * Get bahan with satuan for shift management
     */
    public function get_bahan_with_satuan()
    {
        $this->db->select('b.*, s.nama_satuan');
        $this->db->from($this->table . ' b');
        $this->db->join('satuan s', 'b.id_satuan = s.id_satuan', 'left');
        $this->db->order_by('b.nama_bahan', 'ASC');
        return $this->db->get()->result_array();
    }
}
