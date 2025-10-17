<?php
// Configuration settings for report generation

return [
    'report_formats' => [
        'pdf' => [
            'enabled' => true,
            'options' => [
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'margin' => [
                    'top' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'right' => 10,
                ],
            ],
        ],
        'excel' => [
            'enabled' => true,
            'options' => [
                'include_headers' => true,
                'auto_size_columns' => true,
            ],
        ],
        'csv' => [
            'enabled' => true,
            'options' => [
                'delimiter' => ',',
                'enclosure' => '"',
            ],
        ],
    ],
    'default_report' => 'material_requirement',
    'report_titles' => [
        'material_requirement' => 'Material Requirements Report',
        'weekly_summary' => 'Weekly Material Usage Summary',
        'cost_analysis' => 'Cost Analysis Report',
    ],
];
?>