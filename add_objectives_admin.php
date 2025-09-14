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
                        <h3 class="card-title">Add New Perfomance Objectives</h3>
                    </div>
                    <div class="card-body">
                        <!-- Goal Entry Form -->
                        <form action="./endpoint/process_objectives.php" method="POST">
                            <div class="form-group mb-3">
                                <label for="goal_name">Objective Name</label>
                                <input type="hidden" class="form-control" name="added_by" value=" <?php echo $user_id   ?>" >
                                <input type="text" class="form-control" name="objective_name" id="objective_name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="goal_description">Objective Description</label>
                                <textarea class="form-control" name="objective_description" id="objective_description" rows="4" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="department_uuid">Department</label>
                                <select class="form-control" name="department_uuid" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    // Fetch departments from the departments table
                                    $stmt = $conn->prepare("SELECT uuid, name FROM departments");
                                    $stmt->execute();
                                    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($departments as $department) {
                                        echo "<option value='" . $department['uuid'] . "'>" . $department['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="department_uuid">Goal</label>
                                <select class="form-control" name="goal_id" required>
                                    <option value="">Select Goal</option>
                                    <?php
                                    // Fetch departments from the departments table
                                    $stmt = $conn->prepare("SELECT goal_id, goal_name FROM goals");
                                    $stmt->execute();
                                    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($goals as $goal) {
                                        echo "<option value='" . $goal['goal_id'] . "'>" . $goal['goal_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Objective</button>
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
