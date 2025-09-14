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
    $program_generals = $conn->query("SELECT `uuid`, `name` FROM `education_programs_generals`")->fetchAll(PDO::FETCH_ASSOC);

    include('layout/header.php');
?>

    <div class="container">
        <h2>Add New Assignment</h2>
        <form action="./endpoint/cohort_programs_assignment.php" method="POST" id="departmentForm">

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

            <h2>Details</h2>

            <div class="mb-3">
                <label for="program_general" class="form-label">Program General</label>
                <select class="form-select" id="program_general" name="general_uuid" required>
                    <option value="">Select Program General</option>
                    <?php foreach ($program_generals as $program): ?>
                        <option value="<?= $program['uuid'] ?>"><?= $program['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a program general.</div>
            </div>

            <div class="mb-3" id="majorfield" style="display:none;">
                <label for="major" class="form-label">Program Major</label>
                <select class="form-select" id="major" name="major_uuid"></select>
                <div class="invalid-feedback">Please select a program major.</div>
            </div>

            <div class="mb-3">
                <label for="total_recruits" class="form-label">Total Recruits</label>
                <input type="number" class="form-control" id="total_recruits" name="total_recruits" required>
                <div class="invalid-feedback">Please enter a valid number for total recruits.</div>
            </div>

            <div class="mb-3">
                <label for="gender_preference" class="form-label">Gender Preference</label>
                <select class="form-select" id="gender_preference" name="gender_preference" required>
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
                <div class="invalid-feedback">Please select a gender preference.</div>
            </div>

            <div class="mb-3" id="total_male_field" style="display:none;">
                <label for="total_male" class="form-label">Total Male</label>
                <input type="number" class="form-control" id="total_male" name="total_male" required>
                <div class="invalid-feedback">Please enter a valid number for total male.</div>
            </div>

            <div class="mb-3" id="total_female_field" style="display:none;">
                <label for="total_female" class="form-label">Total Female</label>
                <input type="number" class="form-control" id="total_female" name="total_female" required readonly>
                <div class="invalid-feedback">Total female will be calculated automatically.</div>
            </div>

            <button type="submit" class="btn btn-primary" name="add_cohort_assignment">Add Cohort Assignments</button>
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
        const majorSelect = document.getElementById('major');

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

        // Load majors based on selected program general
        const programGeneralSelect = document.getElementById('program_general');
        programGeneralSelect.addEventListener('change', function() {
            const general_uuid = programGeneralSelect.value;
            loadMajors(general_uuid);
        });

        function loadMajors(general_uuid) {
            fetch(`load_majors.php?general_uuid=${general_uuid}`)
                .then(response => response.json())
                .then(data => {
                    majorSelect.innerHTML = '<option value="">Select Major</option>';
                    data.forEach(major => {
                        const option = document.createElement('option');
                        option.value = major.uuid;
                        option.text = major.name;
                        majorSelect.appendChild(option);
                    });
                    document.getElementById('majorfield').style.display = 'block'; // Show major field
                });
        }

        // Calculate female recruits based on user input
        const totalRecruitsInput = document.getElementById('total_recruits');
        const totalMaleInput = document.getElementById('total_male');
        const totalFemaleInput = document.getElementById('total_female');
        const genderPreferenceSelect = document.getElementById('gender_preference');
        const totalMaleField = document.getElementById('total_male_field');
        const totalFemaleField = document.getElementById('total_female_field');

        genderPreferenceSelect.addEventListener('change', function() {
            if (genderPreferenceSelect.value === 'yes') {
                totalMaleField.style.display = 'block';
                totalFemaleField.style.display = 'block';
            } else {
                totalMaleField.style.display = 'none';
                totalFemaleField.style.display = 'none';
            }
        });

        totalRecruitsInput.addEventListener('input', updateFemaleCount);
        totalMaleInput.addEventListener('input', updateFemaleCount);

        function updateFemaleCount() {
            const totalRecruits = parseInt(totalRecruitsInput.value) || 0;
            const totalMale = parseInt(totalMaleInput.value) || 0;
            const totalFemale = totalRecruits - totalMale;
            totalFemaleInput.value = totalFemale < 0 ? 0 : totalFemale; // Prevent negative numbers
        }
    });
</script>