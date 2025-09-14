<?php
session_start();
include('./conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['goal_id'])) {
    echo "<script>alert('Goal ID is missing!'); window.history.back();</script>";
    exit();
}

$goal_id = $_GET['goal_id'];

$stmt = $conn->prepare("
    SELECT 
        g.goal_name, 
        g.goal_description, 
        d.name AS department_name, 
        u.name AS added_by,
        g.created_at 
    FROM `goals` g
    JOIN `departments` d ON g.dept_id = d.uuid
    JOIN `graduate` u ON g.added_by = u.id
    WHERE g.goal_id = :goal_id
");
$stmt->bindParam(':goal_id', $goal_id);
$stmt->execute();
$goal = $stmt->fetch();

if (!$goal) {
    echo "<script>alert('Goal not found!'); window.history.back();</script>";
    exit();
}

include('layout/header.php');
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Goal Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Goal Name:</strong> <?= htmlspecialchars($goal['goal_name']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($goal['goal_description']) ?></p>
                    <p><strong>Department:</strong> <?= htmlspecialchars($goal['department_name']) ?></p>
                    <p><strong>Added By:</strong> <?= htmlspecialchars($goal['added_by']) ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($goal['created_at']) ?></p>

                    <a href="edit_goal.php?goal_id=<?= htmlspecialchars($goal_id) ?>" class="btn btn-warning">Edit</a>
                    <a href="goal_list_admin.php" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>
