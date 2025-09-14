<?php
session_start();
include('./conn/conn.php');

// --- 1. PROCESSING LOGIC (HANDLES THE FORM SUBMISSION) ---
// This block runs only when the form is submitted
if (isset($_POST['allocate_applicant'])) {
    try {
        // FIX: Trim whitespace from the incoming UUID
        $applications_uuid = trim($_POST['applications_uuid']);
        $department_uuid = isset($_POST['department_uuid']) && !empty($_POST['department_uuid']) ? $_POST['department_uuid'] : null;
        $reporting_date = $_POST['reporting_date'];

        // Get the graduate_uuid of the applicant using the correct column 'uuid'
        // FIX: Changed WHERE clause from 'application_id' to 'uuid'
        $stmt = $conn->prepare("SELECT `applicant_uuid` FROM `applications` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $applications_uuid);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Application not found.");
        }
        $graduate_uuid = $row['applicant_uuid'];

        // Update the application status to 'allocated'
        // FIX: Changed WHERE clause from 'application_id' to 'uuid'
        $status = 'allocated';
        $stmt = $conn->prepare("UPDATE `applications` SET `status` = :status WHERE `uuid` = :uuid");

        // FIX: Bind the correct variable to the :uuid placeholder
        // and use execute with an array for binding parameters for consistency.
        if ($stmt->execute([':status' => $status, ':uuid' => $applications_uuid])) {
            // ... (rest of your code to insert into allocate_applicants and notifications) ...

            // Get the department name for the notification message
            $department_name = "your assigned institution"; // Default message
            if ($department_uuid) {
                $stmt = $conn->prepare("SELECT `name` FROM `departments` WHERE `uuid` = :uuid");
                $stmt->bindParam(':uuid', $department_uuid); // This is fine as it's only used once
                $stmt->execute();
                $dept_row = $stmt->fetch();
                if ($dept_row) {
                    $department_name = $dept_row['name'];
                }
            }

            // Insert the allocation into the database
            $stmt = $conn->prepare("
                INSERT INTO allocate_applicants (applications_uuid, department_uuid, reporting_date, applicant_uuid)
                VALUES (:applications_uuid, :department_uuid, :reporting_date, :applicant_uuid)
            ");
            $stmt->execute([
                ':applications_uuid' => $applications_uuid,
                ':department_uuid' => $department_uuid,
                ':reporting_date' => $reporting_date,
                ':applicant_uuid' => $graduate_uuid
            ]);

            // Insert the notification for the applicant
            $notification_title = "Internship Allocation Successful";
            $notification_desc = "Congratulations! You have been allocated to the $department_name. You are expected to report on $reporting_date. Failure to report by this date will be considered a withdrawal of interest.";

            $stmt = $conn->prepare("
                INSERT INTO `notifications` (`graduate_uuid`, `title`, `description`, `status`, `date`)
                VALUES (:graduate_uuid, :title, :description, 'new', NOW())
            ");
            $stmt->execute([
                ':graduate_uuid' => $graduate_uuid,
                ':title' => $notification_title,
                ':description' => $notification_desc
            ]);

            // Success message and redirect
            echo "<script>
                    alert('Applicant allocated successfully!');
                    window.location.href = 'application_list_reviewed.php';
                  </script>";
            exit(); // Stop script execution after redirect

        } else {
            throw new Exception("Error updating application status.");
        }
    } catch (Exception $e) {
        // Handle any exceptions that occur during the allocation process
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
        exit();
    }
}

// --- 2. DISPLAY LOGIC (SHOWS THE FORM) ---
// This part runs if the page is loaded normally (not a POST request)

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/graduate_internship_system");
    exit();
}

// Check for applicant_uuid in URL
if (!isset($_GET['applicant_uuid'])) {
    echo "Error: No applicant specified.";
    exit();
}
$applicant_uuid = $_GET['applicant_uuid'];

// Fetch data for the dropdowns
$ministries = $conn->query("SELECT uuid, name FROM ministries")->fetchAll(PDO::FETCH_ASSOC);
$institutions = $conn->query("SELECT uuid, name FROM institutions")->fetchAll(PDO::FETCH_ASSOC);

include('layout/header.php');
?>

<div class="container">
    <h3>Allocate Applicant</h3>

    <form method="POST" action="">
        <input type="hidden" name="applications_uuid" value="<?= htmlspecialchars($applicant_uuid) ?>">

        <div class="form-group">
            <label for="allocation_type">Select MDA Type</label>
            <select name="allocation_type" id="allocation_type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="ministry">Ministry</option>
                <option value="other">Other Institution</option>
            </select>
        </div>

        <div class="form-group" id="ministry_institution_section">
            <label for="institution_or_ministry">Select Ministry or Institution</label>
            <select name="institution_or_ministry" id="institution_or_ministry" class="form-control" required>
                <option value="">Select Ministry or Institution</option>
            </select>
        </div>

        <div class="form-group" id="department_section" style="display: none;">
            <label for="department_uuid">Select Department</label>
            <select name="department_uuid" id="department_uuid" class="form-control">
                <option value="">Select Department</option>
            </select>
        </div>

        <div class="form-group">
            <label for="reporting_date">Reporting Date</label>
            <input type="date" name="reporting_date" id="reporting_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success" name="allocate_applicant">Allocate</button>
    </form>
</div>

<script>
    document.getElementById('allocation_type').addEventListener('change', function() {
        var allocationType = this.value;
        var ministryInstitutionSelect = document.getElementById('institution_or_ministry');
        var departmentSection = document.getElementById('department_section');
        var departmentSelect = document.getElementById('department_uuid');

        ministryInstitutionSelect.innerHTML = '<option value="">Select...</option>';
        departmentSelect.innerHTML = '<option value="">Select Department</option>';

        if (allocationType === 'ministry') {
            <?php foreach ($ministries as $ministry): ?>
                ministryInstitutionSelect.innerHTML += `<option value="<?= $ministry['uuid'] ?>"><?= htmlspecialchars($ministry['name']) ?></option>`;
            <?php endforeach; ?>
            departmentSection.style.display = 'block';
        } else if (allocationType === 'other') {
            <?php foreach ($institutions as $institution): ?>
                ministryInstitutionSelect.innerHTML += `<option value="<?= $institution['uuid'] ?>"><?= htmlspecialchars($institution['name']) ?></option>`;
            <?php endforeach; ?>
            departmentSection.style.display = 'block';
        } else {
            departmentSection.style.display = 'none';
        }
    });

    document.getElementById('institution_or_ministry').addEventListener('change', function() {
        var selectedEntity = this.value;
        var allocationType = document.getElementById('allocation_type').value;
        var departmentSelect = document.getElementById('department_uuid');
        var fetchUrl = '';

        departmentSelect.innerHTML = '<option value="">Loading...</option>';

        if (allocationType === 'ministry') {
            fetchUrl = 'load_departments.php?ministry_uuid=' + selectedEntity;
        } else if (allocationType === 'other') {
            fetchUrl = 'load_departments.php?institution_uuid=' + selectedEntity;
        }

        if (selectedEntity !== '') {
            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    if (data.length > 0) {
                        data.forEach(department => {
                            departmentSelect.innerHTML += `<option value="${department.uuid}">${department.name}</option>`;
                        });
                    } else {
                        departmentSelect.innerHTML = '<option value="">No departments found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading departments:', error);
                    departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                });
        }
    });
</script>
<?php include('layout/footer.php'); ?>