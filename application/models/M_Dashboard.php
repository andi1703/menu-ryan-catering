<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Dashboard extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // ✅ A. COUNT FUNCTIONS
    public function getTotalMenu()
    {
        if ($this->db->table_exists('menu')) {
            return $this->db->count_all('menu');
        }
        return 0;
    }

    public function getMenuByCategory($category_name)
    {
        if ($this->db->table_exists('menu') && $this->db->table_exists('kategori_menu')) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('menu m');
            $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori');
            $this->db->where('km.nama_kategori', $category_name);
            $query = $this->db->get();
            $result = $query->row();
            return $result ? $result->total : 0;
        }
        return 0;
    }

    // ✅ B. TRENDING FUNCTIONS
    public function getTrendingMenu()
    {
        if ($this->db->table_exists('menu') && $this->db->table_exists('kategori_menu')) {
            $this->db->select('m.menu_nama, km.nama_kategori, m.created_at, COUNT(*) as popularity');
            $this->db->from('menu m');
            $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori');
            $this->db->group_by(['m.menu_nama', 'km.nama_kategori', 'm.created_at']); // Tambahkan semua kolom non-aggregate
            $this->db->order_by('popularity', 'DESC');
            $this->db->limit(5);
            $query = $this->db->get();
            return $query->result_array();
        }

        // Fallback
        $this->db->select('nama_kategori as menu_nama, deskripsi_kategori as nama_kategori, "0", updated_at as created_at, "1" as popularity');
        $this->db->from('kategori_menu');
        $this->db->order_by('updated_at', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMenuByCategories()
    {
        $categories = ['Indonesian Food', 'Western Food', 'Chinese Food', 'Japanese Food'];
        $result = [];

        foreach ($categories as $category) {
            $result[] = [
                'nama_kategori' => $category,
                'total' => $this->getMenuByCategory($category),
                'percentage' => 0 // Will be calculated in view
            ];
        }

        return $result;
    }

    public function getRecentMenu()
    {
        if ($this->db->table_exists('menu') && $this->db->table_exists('kategori_menu')) {
            $this->db->select('m.menu_nama, km.nama_kategori, m.created_at');
            $this->db->from('menu m');
            $this->db->join('kategori_menu km', 'm.id_kategori = km.id_kategori');
            $this->db->order_by('m.created_at', 'DESC');
            $this->db->limit(5);
            $query = $this->db->get();
            return $query->result_array();
        }

        // Fallback
        $this->db->select('nama_kategori as menu_nama, "General" as nama_kategori, "0", updated_at as created_at');
        $this->db->from('kategori_menu');
        $this->db->order_by('updated_at', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCategoryChart()
    {
        $categories = ['Indonesian Food', 'Western Food', 'Chinese Food', 'Japanese Food'];
        $result = [];

        foreach ($categories as $category) {
            $total = $this->getMenuByCategory($category);
            if ($total > 0) {
                $result[] = [
                    'nama_kategori' => $category,
                    'total' => $total
                ];
            }
        }

        return $result;
    }

    // ✅ FIX POPULAR INGREDIENTS - CEK COLUMN NAME YANG BENAR
    public function getPopularIngredients()
    {
        if ($this->db->table_exists('komponen_menu')) {
            // ✅ CEK COLUMN NAMES YANG ADA
            $columns = $this->db->list_fields('komponen_menu');

            // ✅ PILIH COLUMN NAME YANG BENAR
            $name_column = '';
            if (in_array('nama_komponen', $columns)) {
                $name_column = 'nama_komponen';
            } elseif (in_array('nama_komponen_menu', $columns)) {
                $name_column = 'nama_komponen_menu';
            } elseif (in_array('komponen_menu', $columns)) {
                $name_column = 'komponen_menu';
            } elseif (in_array('nama', $columns)) {
                $name_column = 'nama';
            } else {
                // ✅ JIKA TIDAK ADA COLUMN NAME, RETURN EMPTY
                return [];
            }

            $this->db->select($name_column . ' as nama_komponen, COUNT(*) as usage_count');
            $this->db->from('komponen_menu');
            $this->db->group_by($name_column);
            $this->db->order_by('usage_count', 'DESC');
            $this->db->limit(5);
            $query = $this->db->get();
            return $query->result_array();
        }

        return [];
    }

    // ✅ DEBUG FUNCTION - CEK STRUKTUR TABLE
    public function getTableStructure($table_name)
    {
        if ($this->db->table_exists($table_name)) {
            return $this->db->list_fields($table_name);
        }
        return [];
    }

    // Menambahkan fungsi get_popular_menu
    public function get_popular_menu()
    {
        return $this->db->select('menu_nama, created_at')
            ->from('menu')
            ->order_by('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result_array();
    }
}
