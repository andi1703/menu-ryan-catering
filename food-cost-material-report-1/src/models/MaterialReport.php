<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaterialReport
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getMaterialRequirements($menuId)
    {
        $query = $this->db->query("SELECT b.nama_bahan, mb.qty, s.nama_satuan 
                                     FROM menu_regular_bahan mb 
                                     JOIN bahan b ON mb.id_bahan = b.id_bahan 
                                     JOIN satuan s ON b.id_satuan = s.id_satuan 
                                     WHERE mb.menu_id = ?", [$menuId]);
        return $query->result();
    }

    public function getWeeklySummary($startDate, $endDate)
    {
        $query = $this->db->query("SELECT b.nama_bahan, SUM(mb.qty) as total_qty, s.nama_satuan 
                                     FROM menu_regular_bahan mb 
                                     JOIN bahan b ON mb.id_bahan = b.id_bahan 
                                     JOIN satuan s ON b.id_satuan = s.id_satuan 
                                     WHERE mb.created_at BETWEEN ? AND ? 
                                     GROUP BY b.nama_bahan, s.nama_satuan", [$startDate, $endDate]);
        return $query->result();
    }
}
?>