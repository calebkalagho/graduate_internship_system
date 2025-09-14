<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the intern performance based on the id
    if (isset($_GET['id'])) {
        $performance_id = $_GET['id'];

        // Fetch intern performance details
        $stmt = $conn->prepare("SELECT ip.performance_id, ip.intern_id, ip.kpi_id, ip.score, ip.comments, ip.evaluator_id, ip.created_at, g1.name AS intern_name, k.kpi_name, g2.name AS evaluator_name
                                FROM `intern_performance` ip
                                INNER JOIN `graduate` g1 ON ip.intern_id = g1.id
                                INNER JOIN `graduate` g2 ON ip.evaluator_id = g2.id
                                INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
                                WHERE ip.performance_id = :performance_id");
        $stmt->bindParam(':performance_id', $performance_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $performance = $stmt->fetch();
        } else {
            echo "Performance not found!";
            exit();
        }
    }

    include('layout/header.php');
    ?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Intern Performance</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Intern Performance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Performance Edit</h3>
                </div>
                <div class="card-body">
                    <form action="./endpoint/process_intern_performance.php" method="POST">
                        <input type="hidden" name="performance_id" value="<?= $performance['performance_id'] ?>">

                        <div class="form-group mb-3">
                            <label for="intern_id">Intern Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($performance['intern_name']) ?>" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kpi_id">KPI</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($performance['kpi_name']) ?>" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label for="score">Score</label>
                            <input type="number" class="form-control" name="score" id="score" min="0" max="100" value="<?= htmlspecialchars($performance['score']) ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" name="comments" id="comments" rows="4" required><?= htmlspecialchars($performance['comments']) ?></textarea>
                        </div>

                        <button type="submit" name="edit_button" class="btn btn-primary">Update Performance</button>
                    </form>
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
