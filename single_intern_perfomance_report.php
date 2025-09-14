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

    include('layout/headergraduate.php');
    ?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Intern Performance List</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Intern Performance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Performance Records</h3>
                 
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Intern Name</th>
                            <th>KPI</th>
                            <th>Score</th>
                            <th>Comments</th>
                            <th>Evaluator</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT ip.performance_id, g1.name AS intern_name, k.kpi_name, ip.score, ip.comments, g2.name AS evaluator_name, ip.created_at 
                                FROM `intern_performance` ip
                                INNER JOIN `graduate` g1 ON ip.intern_id = g1.id
                                INNER JOIN `graduate` g2 ON ip.evaluator_id = g2.id
                                INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
                                        where  g1.id=  $user_id");
                        $stmt->execute();
                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['intern_name']) ?></td>
                                <td><?= htmlspecialchars($row['kpi_name']) ?></td>
                                <td><?= htmlspecialchars($row['score']) ?></td>
                                <td><?= htmlspecialchars($row['comments']) ?></td>
                                <td><?= htmlspecialchars($row['evaluator_name']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>

                                    <a href='edit_intern_performance.php?id=<?= $row['performance_id'] ?>' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='details_intern_performance.php?id=<?= $row['performance_id'] ?>' class='btn btn-info btn-sm'>Details</a>

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
    exit();
}
?>