<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaterialController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BahanModel');
    }

    public function addMaterial()
    {
        // Logic to add material
        $data = $this->input->post();
        $result = $this->BahanModel->insertMaterial($data);
        echo json_encode($result);
    }

    public function updateMaterial($id)
    {
        // Logic to update material
        $data = $this->input->post();
        $result = $this->BahanModel->updateMaterial($id, $data);
        echo json_encode($result);
    }

    public function deleteMaterial($id)
    {
        // Logic to delete material
        $result = $this->BahanModel->deleteMaterial($id);
        echo json_encode($result);
    }
}
?>