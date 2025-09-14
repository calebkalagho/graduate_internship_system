<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");  // Use SELECT * without quotes
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
    }

    include('layout/header.php');

?>






    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Add new role</div>
                </div>
                <form action="./endpoint/roles.php" method="post" enctype="multipart/form-data">

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-5">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>

                            </div>
                            <div class="col-md-5">
                                <label for="descriptions" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" required>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" type="submit" name="add_role">Add new role</button>
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