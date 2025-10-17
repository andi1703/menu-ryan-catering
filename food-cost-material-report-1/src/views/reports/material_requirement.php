<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaterialRequirement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MaterialReport');
        $this->load->model('BahanModel');
    }

    public function index()
    {
        $data['material_requirements'] = $this->MaterialReport->getMaterialRequirements();
        $this->load->view('layouts/header');
        $this->load->view('reports/material_requirement', $data);
        $this->load->view('layouts/footer');
    }
}

?>

<div class="container">
    <h1 class="mt-4">Material Requirements Report</h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Material Name</th>
                <th>Required Quantity</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($material_requirements)): ?>
                <?php foreach ($material_requirements as $index => $material): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($material->nama_bahan); ?></td>
                        <td><?php echo htmlspecialchars($material->required_quantity); ?></td>
                        <td><?php echo htmlspecialchars($material->unit); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No material requirements found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>