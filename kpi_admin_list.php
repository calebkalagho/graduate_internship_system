<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
    }

    include('layout/header.php');
    ?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Key Perfomance Indicators</h3>


                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">KPI List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title"> All key Perfomance Indicators  </h3>

                    <a href="./add_kpi_admin.php" class="btn btn-primary float-sm-end">
                        Add new
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>KPI Name</th>
                            <th>KPI Description</th>
                            <th>Weightage</th>
                            <th>Min Target</th>
                            <th>Max Target</th>
                            <th>Measurement Unit</th>
                            <th>Objective</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Query to fetch KPI metrics and join with objectives and departments
                        $stmt = $conn->prepare("
            SELECT 
                k.kpi_id, 
                k.kpi_name, 
                k.kpi_description, 
                k.kpi_weightage, 
                k.min_target, 
                k.max_target, 
                k.measurement_unit, 
                po.objective_name, 
                d.name AS department_name 
            FROM `kpi_metrics` k
            JOIN `performance_objectives` po ON k.objective_id = po.objective_id
            JOIN `departments` d ON po.dept_id = d.uuid
        ");
                        $stmt->execute();
                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            $kpi_id = $row['kpi_id'];
                            $kpi_name = $row['kpi_name'];
                            $kpi_description = $row['kpi_description'];
                            $kpi_weightage = $row['kpi_weightage'];
                            $min_target = $row['min_target'];
                            $max_target = $row['max_target'];
                            $measurement_unit = $row['measurement_unit'];
                            $objective_name = $row['objective_name'];
                            $department_name = $row['department_name'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($kpi_name) ?></td>
                                <td><?= htmlspecialchars($kpi_description) ?></td>
                                <td><?= htmlspecialchars($kpi_weightage) ?></td>
                                <td><?= htmlspecialchars($min_target) ?></td>
                                <td><?= htmlspecialchars($max_target) ?></td>
                                <td><?= htmlspecialchars($measurement_unit) ?></td>
                                <td><?= htmlspecialchars($objective_name) ?></td>
                                <td><?= htmlspecialchars($department_name) ?></td>
                                <td>
                                    <a href="edit_kpi.php?kpi_id=<?= $kpi_id ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="view_kpi.php?kpi_id=<?= $kpi_id ?>" class="btn btn-sm btn-info">Details</a>

                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>