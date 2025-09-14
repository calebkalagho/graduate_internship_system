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
    $roles = $conn->query("SELECT * FROM `roles`")->fetchAll(PDO::FETCH_ASSOC);
    include('layout/header.php');
?>

    <div class="container">
        <h2>Add New Employee</h2>
        <form action="./endpoint/applicants.php" method="POST" id="employeeForm">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required pattern="[A-Za-z]+" title="First name should only contain letters.">
                <div class="invalid-feedback">Please enter a valid first name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" pattern="[A-Za-z]+" title="Middle name should only contain letters.">
                <div class="invalid-feedback">Please enter a valid middle name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required pattern="[A-Za-z]+" title="Last name should only contain letters.">
                <div class="invalid-feedback">Please enter a valid last name (only letters).</div>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div class="invalid-feedback">Please select a gender.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="text" class="form-control" id="mobile" name="mobile" required pattern="\d+" title="Mobile number should only contain numbers.">
                <div class="invalid-feedback">Please enter a valid mobile number (only digits).</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6" title="Password must be at least 6 characters long.">
                <div class="invalid-feedback">Password must be at least 6 characters long.</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>
            <div class="mb-3">
                <label for="mda_type" class="form-label">MDA Type</label>
                <select class="form-select" id="mda_type" name="mda_type" required>
                    <option value="">Select MDA Type</option>
                    <option value="Ministry">Ministry</option>
                    <option value="Other">Other</option>
                </select>
                <div class="invalid-feedback">Please select MDA Type.</div>
            </div>
            <div class="mb-3" id="ministryField" style="display:none;">
                <label for="ministry" class="form-label">Ministry</label>
                <select class="form-select" id="ministry" name="ministry"></select>
                <div class="invalid-feedback">Please select a ministry.</div>
            </div>
            <div class="mb-3" id="institutionField" style="display:none;">
                <label for="institution" class="form-label">Institution</label>
                <select class="form-select" id="institution" name="institution"></select>
                <div class="invalid-feedback">Please select an institution.</div>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" name="department" required></select>
                <div class="invalid-feedback">Please select a department.</div>
            </div>

            <h5 class="mt-4">Role </h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Choose...</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['uuid'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="add_employee">Add Employee</button>
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
        // Live validation for passwords
        const password = document.getElementById('password');
        const confirm_password = document.getElementById('confirm_password');

        confirm_password.addEventListener('input', function() {
            if (password.value !== confirm_password.value) {
                confirm_password.setCustomValidity('Passwords do not match');
            } else {
                confirm_password.setCustomValidity('');
            }
        });

        // Show or hide Ministry/Institution field based on MDA Type
        const mdaType = document.getElementById('mda_type');
        const ministryField = document.getElementById('ministryField');
        const institutionField = document.getElementById('institutionField');

        mdaType.addEventListener('change', function() {
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
        });

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
                        ministrySelect.appendChild(option);
                    });
                });
        }

        function loadInstitutions() {
            fetch('load_institutions.php')
                .then(response => response.json())
                .then(data => {
                    const institutionSelect = document.getElementById('institution');
                    institutionSelect.innerHTML = '<option value="">Select Institution</option>';
                    data.forEach(institution => {
                        const option = document.createElement('option');
                        option.value = institution.uuid;
                        option.text = institution.name;
                        institutionSelect.appendChild(option);
                    });
                });
        }

        // Load departments based on ministry or institution
        const ministrySelect = document.getElementById('ministry');
        const institutionSelect = document.getElementById('institution');
        const departmentSelect = document.getElementById('department');

        ministrySelect.addEventListener('change', function() {
            loadDepartments('ministry_uuid', ministrySelect.value);
        });

        institutionSelect.addEventListener('change', function() {
            loadDepartments('institution_uuid', institutionSelect.value);
        });

        function loadDepartments(type, uuid) {
            fetch(`load_departments.php?${type}=${uuid}`)
                .then(response => response.json())
                .then(data => {
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    data.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.uuid;
                        option.text = department.name;
                        departmentSelect.appendChild(option);
                    });
                });
        }

        // Live form validation before submission
        const form = document.getElementById('employeeForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>