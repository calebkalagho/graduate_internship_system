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
                    <h3 class="mb-0">All Institutions</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Institutions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Institutions</h3>
                    <a href="./add_institution.php" class="btn btn-primary float-sm-end">
                        Add New Institution
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Post Address</th>
                                <th>Physical Address</th>
                                <th>Contacts</th>
                                <th>Email Address</th>
                                <th>District</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Join institutions with districts to get district name
                            $stmt = $conn->prepare("
                                SELECT institutions.uuid, institutions.name, institutions.description, institutions.post_address, institutions.physical_address, institutions.contacts, institutions.email_address, districts.name AS district_name 
                                FROM `institutions` 
                                LEFT JOIN `districts` ON institutions.district_id = districts.id
                            ");
                            $stmt->execute();
                            $result = $stmt->fetchAll();

                            foreach ($result as $row) {
                                $uuid = $row['uuid'];
                                $name = $row['name'];
                                $description = $row['description'];
                                $post_address = $row['post_address'];
                                $physical_address = $row['physical_address'];
                                $contacts = $row['contacts'];
                                $email_address = $row['email_address'];
                                $district_name = $row['district_name'];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td><?= htmlspecialchars($description) ?></td>
                                    <td><?= htmlspecialchars($post_address) ?></td>
                                    <td><?= htmlspecialchars($physical_address) ?></td>
                                    <td><?= htmlspecialchars($contacts) ?></td>
                                    <td><?= htmlspecialchars($email_address) ?></td>
                                    <td><?= htmlspecialchars($district_name) ?></td>
                                    <td>
                                        <a href='edit_institution.php?id=<?= $uuid ?>' class='btn btn-warning btn-sm'>Edit</a>

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