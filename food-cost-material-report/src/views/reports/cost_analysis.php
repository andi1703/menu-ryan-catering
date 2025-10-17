<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CostAnalysis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MaterialReport');
    }

    public function index()
    {
        $data['title'] = 'Cost Analysis Report';
        $data['cost_data'] = $this->MaterialReport->getMaterialRequirements();
        
        $this->load->view('layouts/header', $data);
        $this->load->view('reports/cost_analysis', $data);
        $this->load->view('layouts/footer');
    }
}
?>

<div class="container">
    <h1 class="mt-4"><?php echo $title; ?></h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Material Name</th>
                <th>Quantity Used</th>
                <th>Cost per Unit</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cost_data)): ?>
                <?php foreach ($cost_data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item->material_name); ?></td>
                        <td><?php echo htmlspecialchars($item->quantity_used); ?></td>
                        <td><?php echo htmlspecialchars($item->cost_per_unit); ?></td>
                        <td><?php echo htmlspecialchars($item->total_cost); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>