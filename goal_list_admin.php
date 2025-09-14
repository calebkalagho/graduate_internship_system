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

    include('layout/header.php');
    ?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Goals</h3>


                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">M & E goals List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title"> All M & E Goals  </h3>

                    <a href="./add_goal_me.php" class="btn btn-primary float-sm-end">
                       Add new
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Goal Name</th>
                            <th>Goal Description</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Query to fetch goals and join with departments table
                        $stmt = $conn->prepare("
    SELECT 
        g.goal_id, 
        g.goal_name, 
        g.goal_description, 
        d.name AS department_name 
    FROM `goals` g
    JOIN `departments` d ON g.dept_id = d.uuid
");
                        $stmt->execute();
                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            $goal_id = $row['goal_id'];
                            $goal_name = $row['goal_name'];
                            $goal_description = $row['goal_description'];
                            $department_name = $row['department_name'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($goal_name) ?></td>
                                <td><?= htmlspecialchars($goal_description) ?></td>
                                <td><?= htmlspecialchars($department_name) ?></td>
                                <td>
                                    <a href="goal_details.php?goal_id=<?= htmlspecialchars($goal_id) ?>" class="btn btn-info btn-sm">Details</a>
                                    <a href="edit_goal.php?goal_id=<?= htmlspecialchars($goal_id) ?>" class="btn btn-warning">Edit</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>