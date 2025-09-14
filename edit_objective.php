<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if objective_id is provided
if (!isset($_GET['objective_id'])) {
    echo "Invalid request!";
    exit();
}

$objective_id = $_GET['objective_id'];

// Fetch existing objective details
$stmt = $conn->prepare("SELECT * FROM performance_objectives WHERE objective_id = :objective_id");
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
                    <h3 class="card-title">Edit Performance Objective</h3>
                </div>
                <div class="card-body">
                    <form action="./endpoint/process_objectives.php" method="POST">
                        <input type="hidden" name="objective_id" value="<?= $objective_id ?>">
                        <input type="hidden" class="form-control" name="added_by" value=" <?php echo $user_id   ?>" >
                        <div class="form-group mb-3">
                            <label for="objective_name">Objective Name</label>
                            <input type="text" class="form-control" name="objective_name" value="<?= htmlspecialchars($objective['objective_name']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="objective_description">Objective Description</label>
                            <textarea class="form-control" name="objective_description" rows="4" required><?= htmlspecialchars($objective['objective_description']) ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department_uuid">Department</label>
                            <select class="form-control" name="department_uuid" required>
                                <?php
                                $stmt = $conn->prepare("SELECT uuid, name FROM departments");
                                $stmt->execute();
                                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($departments as $department) {
                                    $selected = ($department['uuid'] == $objective['dept_id']) ? "selected" : "";
                                    echo "<option value='{$department['uuid']}' $selected>{$department['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="edit_button" class="btn btn-success">Update Objective</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>
