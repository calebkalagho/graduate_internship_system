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

    // Check if a ministry UUID has been passed to edit
    if (isset($_GET['id'])) {
        $ministry_uuid = $_GET['id'];

        // Fetch the ministry details from the database using the UUID
        $stmt = $conn->prepare("SELECT * FROM `ministries` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $ministry_uuid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $ministry = $stmt->fetch();
            $ministry_name = $ministry['name'];
            $ministry_description = $ministry['description'];
        } else {
            echo "Ministry not found.";
            exit();
        }
    } else {
        echo "No ministry selected.";
        exit();
    }

    include('layout/header.php');
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Ministry</h3>
                    </div>
                    <div class="card-body">
                        <!-- Ministry Edit Form -->
                        <form action="update_ministry.php" method="POST">
                            <input type="hidden" name="uuid" value="<?= $ministry_uuid ?>">
                            <div class="form-group mb-3">
                                <label for="name">Ministry Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?= $ministry_name ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Ministry Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" required><?= $ministry_description ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Ministry</button>
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
