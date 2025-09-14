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

    // Fetch role data
    if (isset($_GET['id'])) {
        $role_id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM `roles` WHERE `uuid` = :id");
        $stmt->bindParam(':id', $role_id);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            // Role not found, redirect to the roles listing page
            header("Location: roles_list.php");
            exit();
        }
    } else {
        // No role ID provided, redirect to the roles listing page
        header("Location: roles_list.php");
        exit();
    }

    include('layout/header.php');
    ?>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Edit role</div>
                </div>
                <form action="./endpoint/roles.php" method="post">
                    <input type="hidden" name="role_id" value="<?= $role['uuid'] ?>">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $role['name'] ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" value="<?= $role['description'] ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit" name="update_role">Update role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>