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
    $districts = $conn->query("SELECT * FROM `districts`")->fetchAll(PDO::FETCH_ASSOC);
    include('layout/header.php');
?>

    <div class="container">
        <h2>Add New Department</h2>
        <form action="./endpoint/departments.php" method="POST" id="departmentForm">
            <div class="mb-3">
                <label for="name" class="form-label">Name </label>
                <input type="text" class="form-control" id="name" name="name" required>

            </div>

            <div class="mb-3">
                <label for="desc" class="form-label">Descriptions</label>
                <textarea type="text" class="form-control" id="desc" name="desc" rows="3"></textarea>

            </div>
            <div class="mb-3">
                <label for="desc" class="form-label">post_address</label>
                <textarea type="text" class="form-control" id="post_address" name="post_address" rows="3"></textarea>

            </div>
            <div class="mb-3">
                <label for="physical_addres" class="form-label">physical address</label>
                <textarea type="text" class="form-control" id="physical_addres" name="physical_addres" rows="3"></textarea>

            </div>

            <div class="mb-3">
                <label for="email_address" class="form-label">email_address</label>
                <input type="email_address" class="form-control" id="email_address" name="email_address" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
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

            <h5 class="mt-4">Districts</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="district_id" class="form-label"> District</label>
                    <select class="form-select" id="district_id" name="district_id" required>
                        <option value="">Choose...</option>
                        <?php foreach ($districts as $district): ?>
                            <option value="<?= $district['id'] ?>"><?= $district['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
    </div>
    <br /> <br /> <br />
    <button type="submit" class="btn btn-primary" name="add_department">Add Department</button>
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
        const form = document.getElementById('departmentForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>