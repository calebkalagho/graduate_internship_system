<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if objective_id is provided
if (!isset($_GET['objective_id'])) {
    echo "Invalid request!";
    exit();
}

$objective_id = $_GET['objective_id'];

// Fetch objective details
$stmt = $conn->prepare("
    SELECT po.*, d.name AS department_name 
    FROM performance_objectives po
    JOIN departments d ON po.dept_id = d.uuid
    WHERE po.objective_id = :objective_id
");
$stmt->bindParam(':objective_id', $objective_id);
$stmt->execute();
$objective = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$objective) {
    echo "Objective not found!";
    exit();
}

include('layout/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Objective Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Objective Name:</strong> <?= htmlspecialchars($objective['objective_name']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($objective['objective_description'])) ?></p>
                    <p><strong>Department:</strong> <?= htmlspecialchars($objective['department_name']) ?></p>
                    <a href="edit_objective.php?objective_id=<?= $objective_id ?>" class="btn btn-warning">Edit</a>
                    <a href="objectives_admin_list.php" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>
