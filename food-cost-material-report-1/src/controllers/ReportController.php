<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MaterialReport');
    }

    public function generateMaterialReport()
    {
        $data['material_requirements'] = $this->MaterialReport->getMaterialRequirements();
        $this->load->view('reports/material_requirement', $data);
    }

    public function generateWeeklySummary()
    {
        $data['weekly_summary'] = $this->MaterialReport->getWeeklySummary();
        $this->load->view('reports/weekly_summary', $data);
    }

    public function generateCostAnalysis()
    {
        // Implement cost analysis report generation
        $data['cost_analysis'] = $this->MaterialReport->getCostAnalysis();
        $this->load->view('reports/cost_analysis', $data);
    }
}
?>