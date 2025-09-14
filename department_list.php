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
                    <h3 class="mb-0">All Departments</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Departments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Departments</h3>
                    <a href="./add_department.php" class="btn btn-primary float-sm-end">
                        Add New Department
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Description</th>
                                <th>Post Address</th>
                                <th>Physical Address</th>
                                <th>Contacts</th>
                                <th>Email Address</th>
                                <th>Institution Name</th>
                                <th>Ministry Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // SQL query to retrieve active departments along with their institutions and ministries
                                $sql = "
                                SELECT 
                                    d.uuid AS department_uuid,
                                    d.name AS department_name,
                                    d.description AS department_description,
                                    d.post_address AS department_post_address,
                                    d.physical_address AS department_physical_address,
                                    d.contacts AS department_contacts,
                                    d.email_address AS department_email,
                                    d.status AS department_status,
                                    i.uuid AS institution_uuid,
                                    i.name AS institution_name,
                                    m.uuid AS ministry_uuid,
                                    m.name AS ministry_name
                                FROM 
                                    departments d
                                LEFT JOIN 
                                    institutions i ON d.da_uuid = i.uuid
                                LEFT JOIN 
                                    ministries m ON d.ministry_uuid = m.uuid
                                "; // Only active departments

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                // Fetch all departments
                                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Check if any departments are found
                                if (count($departments) > 0) {
                                    foreach ($departments as $department) {
                                        echo "<tr>
                                            <td>{$department['department_name']}</td>
                                            <td>{$department['department_description']}</td>
                                            <td>{$department['department_post_address']}</td>
                                            <td>{$department['department_physical_address']}</td>
                                            <td>{$department['department_contacts']}</td>
                                            <td>{$department['department_email']}</td>
                                            <td>{$department['institution_name']}</td>
                                            <td>{$department['ministry_name']}</td>
                                            <td>
                                                <a href='edit_department.php?id={$department['department_uuid']}' class='btn btn-warning btn-sm'>Edit</a>
                                               
                                            </td>
                                          </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No active departments found.</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='9'>Error: " . $e->getMessage() . "</td></tr>";
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