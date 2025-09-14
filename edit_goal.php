<?php
session_start();
include('./conn/conn.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if goal ID is provided
if (!isset($_GET['goal_id'])) {
    echo "<script>alert('Goal ID is missing!'); window.history.back();</script>";
    exit();
}

$goal_id = $_GET['goal_id'];

// Fetch goal details
$stmt = $conn->prepare("SELECT * FROM `goals` WHERE `goal_id` = :goal_id");
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
                    <h3 class="card-title">Edit Goal</h3>
                </div>
                <div class="card-body">
                    <form action="./endpoint/process_add_goal.php" method="POST">
                        <input type="hidden" name="goal_id" value="<?= htmlspecialchars($goal['goal_id']) ?>">

                        <div class="form-group mb-3">
                            <label for="goal_name">Goal Name</label>
                            <input type="text" class="form-control" name="goal_name" id="goal_name"
                                   value="<?= htmlspecialchars($goal['goal_name']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="goal_description">Goal Description</label>
                            <textarea class="form-control" name="goal_description" id="goal_description" rows="4" required><?= htmlspecialchars($goal['goal_description']) ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department_uuid">Department</label>
                            <select class="form-control" name="department_uuid" required>
                                <option value="">Select Department</option>
                                <?php
                                $stmt = $conn->prepare("SELECT uuid, name FROM departments");
                                $stmt->execute();
                                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($departments as $department) {
                                    $selected = ($goal['dept_id'] === $department['uuid']) ? "selected" : "";
                                    echo "<option value='" . $department['uuid'] . "' $selected>" . $department['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="edit_button" class="btn btn-success">Update Goal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>
