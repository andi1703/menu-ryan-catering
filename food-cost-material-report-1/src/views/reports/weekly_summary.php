<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url('src/assets/css/report.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('src/assets/css/table.css'); ?>">
    <title>Weekly Material Summary Report</title>
</head>

<body>
    <?php include 'layouts/header.php'; ?>

    <div class="container">
        <h1>Weekly Material Summary Report</h1>
        <p>This report summarizes the material usage for the week.</p>

        <div class="report-content">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Material Name</th>
                        <th>Quantity Used</th>
                        <th>Unit</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weekly_summary as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($item->material_name); ?></td>
                            <td><?php echo $item->quantity_used; ?></td>
                            <td><?php echo htmlspecialchars($item->unit); ?></td>
                            <td><?php echo number_format($item->cost, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'layouts/footer.php'; ?>
</body>

</html>