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
                        <h3 class="card-title">Add Intern Performance</h3>
                    </div>
                    <div class="card-body">
                        <form action="./endpoint/process_intern_performance.php" method="POST">
                            <input type="hidden" class="form-control" name="evaluator_id" value="<?php echo $user_id; ?>">

                            <div class="form-group mb-3">
                                <label for="intern_id">Select Intern</label>
                                <select class="form-control" name="intern_id" required>
                                    <option value="">Select Intern</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT id, name FROM graduate where  role='graduate' ");
                                    $stmt->execute();
                                    $interns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($interns as $intern) {
                                        echo "<option value='" . $intern['id'] . "'>" . $intern['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kpi_id">Select KPI</label>
                                <select class="form-control" name="kpi_id" required>
                                    <option value="">Select KPI</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT kpi_id, kpi_name FROM kpi_metrics");
                                    $stmt->execute();
                                    $kpis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($kpis as $kpi) {
                                        echo "<option value='" . $kpi['kpi_id'] . "'>" . $kpi['kpi_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="score">Score</label>
                                <input type="number" class="form-control" name="score" id="score" min="0" max="100" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="comments">Comments</label>
                                <textarea class="form-control" name="comments" id="comments" rows="4" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Performance</button>
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
