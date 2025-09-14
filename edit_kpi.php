<?php
session_start();
include('./conn/conn.php');

// Check if KPI ID is provided
if (!isset($_GET['kpi_id'])) {
    die("Invalid request!");
}

$kpi_id = $_GET['kpi_id'];

// Fetch KPI details
$stmt = $conn->prepare("SELECT * FROM kpi_metrics WHERE kpi_id = :kpi_id");
$stmt->bindParam(':kpi_id', $kpi_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    die("KPI not found!");
}

$kpi = $stmt->fetch();
include('layout/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit KPI</h3>
                </div>
                <div class="card-body">
                    <form action="./endpoint/process_kpi_metrics.php" method="POST">
                        <input type="hidden" name="kpi_id" value="<?php echo $kpi['kpi_id']; ?>">

                        <div class="form-group mb-3">
                            <label for="kpi_name">KPI Name</label>
                            <input type="text" class="form-control" name="kpi_name" value="<?php echo $kpi['kpi_name']; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kpi_description">KPI Description</label>
                            <textarea class="form-control" name="kpi_description" rows="4" required><?php echo $kpi['kpi_description']; ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kpi_weightage">KPI Weightage (%)</label>
                            <input type="number" class="form-control" name="kpi_weightage" value="<?php echo $kpi['kpi_weightage']; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="min_target">Minimum Target</label>
                            <input type="number" class="form-control" name="min_target" value="<?php echo $kpi['min_target']; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_target">Maximum Target</label>
                            <input type="number" class="form-control" name="max_target" value="<?php echo $kpi['max_target']; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="measurement_unit">Measurement Unit</label>
                            <input type="text" class="form-control" name="measurement_unit" value="<?php echo $kpi['measurement_unit']; ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success" name="edit_button">Update KPI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('layout/footer.php');
?>
