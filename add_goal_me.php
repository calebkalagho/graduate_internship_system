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
                        <h3 class="card-title">Add New Goal</h3>
                    </div>
                    <div class="card-body">
                        <!-- Goal Entry Form -->
                        <form action="./endpoint/process_add_goal.php" method="POST">
                            <div class="form-group mb-3">
                                <label for="goal_name">Goal Name</label>
                                <input type="hidden" class="form-control" name="added_by" value=" <?php echo $user_id   ?>" >
                                <input type="text" class="form-control" name="goal_name" id="goal_name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="goal_description">Goal Description</label>
                                <textarea class="form-control" name="goal_description" id="goal_description" rows="4" required></textarea>
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
                            <button type="submit" class="btn btn-primary">Add Goal</button>
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
