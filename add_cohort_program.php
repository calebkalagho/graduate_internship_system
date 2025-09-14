<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the `graduate` table
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
    }

    include('layout/header.php');
?>

    <div class="container mt-5">
        <h2>Add Cohort Program</h2>
        <div class="card">
            <div class="card-body">
                <form action="./endpoint/cohort_programs.php" method="POST">
                    <div class="mb-3">
                        <label for="program_name" class="form-label">Program Name</label>
                        <input type="text" class="form-control" id="program_name" name="program_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                        <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Program</button>
                </form>
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