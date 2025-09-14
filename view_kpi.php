<?php
session_start();
include('./conn/conn.php');

// Check if KPI ID is provided in the URL
if (isset($_GET['kpi_id'])) {
    $kpi_id = $_GET['kpi_id'];

    // Fetch KPI details from the database
    $stmt = $conn->prepare("SELECT 
                                k.kpi_name, 
                                k.kpi_description, 
                                k.kpi_weightage, 
                                k.min_target, 
                                k.max_target, 
                                k.measurement_unit, 
                                po.objective_name, 
                                d.name AS department_name 
                            FROM kpi_metrics k
                            JOIN performance_objectives po ON k.objective_id = po.objective_id
                            JOIN departments d ON po.dept_id = d.uuid
                            WHERE k.kpi_id = :kpi_id");
    $stmt->bindParam(':kpi_id', $kpi_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $kpi = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<script>alert('KPI not found!'); window.location.href='kpi_list.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='kpi_list.php';</script>";
    exit();
}

include('layout/header.php');
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">KPI Details</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>KPI Name</th>
                    <td><?= htmlspecialchars($kpi['kpi_name']) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= htmlspecialchars($kpi['kpi_description']) ?></td>
                </tr>
                <tr>
                    <th>Weightage (%)</th>
                    <td><?= htmlspecialchars($kpi['kpi_weightage']) ?></td>
                </tr>
                <tr>
                    <th>Minimum Target</th>
                    <td><?= htmlspecialchars($kpi['min_target']) ?></td>
                </tr>
                <tr>
                    <th>Maximum Target</th>
                    <td><?= htmlspecialchars($kpi['max_target']) ?></td>
                </tr>
                <tr>
                    <th>Measurement Unit</th>
                    <td><?= htmlspecialchars($kpi['measurement_unit']) ?></td>
                </tr>
                <tr>
                    <th>Objective</th>
                    <td><?= htmlspecialchars($kpi['objective_name']) ?></td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td><?= htmlspecialchars($kpi['department_name']) ?></td>
                </tr>
            </table>
            <a href="edit_kpi.php?kpi_id=<?= $kpi_id ?>" class="btn btn-primary">Edit KPI</a>
            <a href="kpi_admin_list.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>
