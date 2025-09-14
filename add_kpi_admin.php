<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    include('layout/header.php');
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New KPI Metric</h3>
                    </div>
                    <div class="card-body">
                        <!-- KPI Metrics Entry Form -->
                        <form action="./endpoint/process_kpi_metrics.php" method="POST">
                            <input type="hidden" class="form-control" name="added_by" value="<?php echo $user_id; ?>" >

                            <div class="form-group mb-3">
                                <label for="objective_id">Objective</label>
                                <select class="form-control" name="objective_id" required>
                                    <option value="">Select Objective</option>
                                    <?php
                                    // Fetch objectives from the objectives table
                                    $stmt = $conn->prepare("SELECT objective_id, objective_name FROM performance_objectives");
                                    $stmt->execute();
                                    $objectives = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($objectives as $objective) {
                                        echo "<option value='" . $objective['objective_id'] . "'>" . $objective['objective_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kpi_name">KPI Name</label>
                                <input type="text" class="form-control" name="kpi_name" id="kpi_name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kpi_description">KPI Description</label>
                                <textarea class="form-control" name="kpi_description" id="kpi_description" rows="4" required></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kpi_weightage">KPI Weightage (%)</label>
                                <input type="number" class="form-control" name="kpi_weightage" id="kpi_weightage" min="1" max="100" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="min_target">Minimum Target</label>
                                <input type="number" class="form-control" name="min_target" id="min_target" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="max_target">Maximum Target</label>
                                <input type="number" class="form-control" name="max_target" id="max_target" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="measurement_unit">Measurement Unit</label>
                                <input type="text" class="form-control" name="measurement_unit" id="measurement_unit" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Add KPI Metric</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>
