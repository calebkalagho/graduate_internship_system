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

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Employees</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Employees</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Employees</h3>
                    <!-- Button to Add Employee -->
                    <a href="./add_employee.php" class="btn btn-primary float-sm-end">
                        Add Employee
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Residential Address</th>
                                <th>Postal Address</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query the `graduate` table for employees (role_type = 'employee')
                            $stmt = $conn->prepare("
                                SELECT graduate.id, graduate.first_name, graduate.middle_name, graduate.last_name, graduate.gender, graduate.email, graduate.mobile, graduate.residential_address, graduate.post_address, graduate.role, graduate.department_uuid, departments.name AS department_name 
                                FROM `graduate`
                                LEFT JOIN `departments` ON graduate.department_uuid = departments.uuid
                                WHERE graduate.role_type = 'employee' AND graduate.role != 'graduate'
                            ");

                            $stmt->execute();
                            $result = $stmt->fetchAll();

                            foreach ($result as $row) {
                                $employee_id = $row['id'];
                                $first_name = $row['first_name'];
                                $middle_name = $row['middle_name'];
                                $last_name = $row['last_name'];
                                $gender = $row['gender'];
                                $email = $row['email'];
                                $mobile = $row['mobile'];
                                $residential_address = $row['residential_address'];
                                $post_address = $row['post_address'];
                                $role = $row['role'];
                                $department_name = $row['department_name'];
                            ?>
                                <tr>
                                    <td><?= $first_name ?></td>
                                    <td><?= $middle_name ?></td>
                                    <td><?= $last_name ?></td>
                                    <td><?= $gender ?></td>
                                    <td><?= $email ?></td>
                                    <td><?= $mobile ?></td>
                                    <td><?= $residential_address ?></td>
                                    <td><?= $post_address ?></td>
                                    <td><?= $role ?></td>
                                    <td><?= $department_name ?></td>
                                    <td>
                                        <!-- Edit and Delete Buttons -->
                                        <a href="edit_employee.php?id=<?= $employee_id ?>" class="btn btn-primary btn-sm">Edit</a>            </td>
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
    header("Location: login.php");
    exit();
}
?>

<script>
    // Pass data to the Edit Employee modal
    var editEmployeeModal = document.getElementById('editEmployeeModal');
    editEmployeeModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var employeeId = button.getAttribute('data-id');

        // Fetch employee data and populate the form
        var first_name = button.parentElement.parentElement.children[0].textContent;
        var middle_name = button.parentElement.parentElement.children[1].textContent;
        var last_name = button.parentElement.parentElement.children[2].textContent;
        var gender = button.parentElement.parentElement.children[3].textContent;
        var email = button.parentElement.parentElement.children[4].textContent;
        var mobile = button.parentElement.parentElement.children[5].textContent;
        var residential_address = button.parentElement.parentElement.children[6].textContent;
        var post_address = button.parentElement.parentElement.children[7].textContent;
        var role = button.parentElement.parentElement.children[8].textContent;
        var department_uuid = button.parentElement.parentElement.children[9].textContent;

        // Set form values
        document.getElementById('editEmployeeId').value = employeeId;
        document.getElementById('first_name_edit').value = first_name;
        document.getElementById('middle_name_edit').value = middle_name;
        document.getElementById('last_name_edit').value = last_name;
        document.getElementById('gender_edit').value = gender;
        document.getElementById('email_edit').value = email;
        document.getElementById('mobile_edit').value = mobile;
        document.getElementById('residential_address_edit').value = residential_address;
        document.getElementById('post_address_edit').value = post_address;
        document.getElementById('role_edit').value = role;
        document.getElementById('department_uuid_edit').value = department_uuid;
    });
</script>