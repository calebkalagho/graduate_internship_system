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

    // Fetch employee data
    if (isset($_GET['id'])) {
        $employee_id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id AND `role_type` = 'employee'");
        $stmt->bindParam(':id', $employee_id);
        $stmt->execute();
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            // Employee not found, redirect to the employee listing page
            header("Location: employees.php");
            exit();
        }
    } else {
        // No employee ID provided, redirect to the employee listing page
        header("Location: employees.php");
        exit();
    }

    $roles = $conn->query("SELECT * FROM `roles`")->fetchAll(PDO::FETCH_ASSOC);
    include('layout/header.php');
    ?>

    <div class="container">
        <h2>Edit Employee</h2>
        <form action="./endpoint/Applicants.php" method="POST" id="editEmployeeForm">
            <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required pattern="[A-Za-z]+" title="First name should only contain letters." value="<?= $employee['first_name'] ?>">
                <div class="invalid-feedback">Please enter a valid first name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" pattern="[A-Za-z]+" title="Middle name should only contain letters." value="<?= $employee['middle_name'] ?>">
                <div class="invalid-feedback">Please enter a valid middle name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required pattern="[A-Za-z]+" title="Last name should only contain letters." value="<?= $employee['last_name'] ?>">
                <div class="invalid-feedback">Please enter a valid last name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= $employee['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $employee['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
                <div class="invalid-feedback">Please select a gender.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= $employee['email'] ?>">
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="text" class="form-control" id="mobile" name="mobile" required pattern="\d+" title="Mobile number should only contain numbers." value="<?= $employee['mobile'] ?>">
                <div class="invalid-feedback">Please enter a valid mobile number (only digits).</div>
            </div>


        
            <button type="submit" class="btn btn-primary" name="update_employee">Update Employee</button>
        </form>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show or hide Ministry/Institution field based on MDA Type
        const mdaType = document.getElementById('mda_type');
        const ministryField = document.getElementById('ministryField');
        const institutionField = document.getElementById('institutionField');

        function toggleMdaFields() {
            if (mdaType.value === 'Ministry') {
                ministryField.style.display = 'block';
                institutionField.style.display = 'none';
                loadMinistries();
            } else if (mdaType.value === 'Other') {
                ministryField.style.display = 'none';
                institutionField.style.display = 'block';
                loadInstitutions();
            } else {
                ministryField.style.display = 'none';
                institutionField.style.display = 'none';
            }
        }

        mdaType.addEventListener('change', toggleMdaFields);
        toggleMdaFields(); // Call on page load

        // Load Ministries and Institutions
        function loadMinistries() {
            fetch('load_ministries.php')
                .then(response => response.json())
                .then(data => {
                    const ministrySelect = document.getElementById('ministry');
                    ministrySelect.innerHTML = '<option value="">Select Ministry</option>';
                    data.forEach(ministry => {
                        const option = document.createElement('option');
                        option.value = ministry.uuid;
                        option.text = ministry.name;
                        if (ministry.uuid === '<?= $employee['ministry_uuid'] ?>') {
                            option.selected = true;
                        }
                        ministrySelect.appendChild(option);
                    });
                    loadDepartments('ministry_uuid', ministrySelect.value);
                });
        }

        function loadInstitutions() {
            fetch('load_institutions.php')
                .then(response => response.json())
                .then(