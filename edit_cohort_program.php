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

    // Fetch cohort program data
    if (isset($_GET['id'])) {
        $cohort_uuid = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM `cohort_programs` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $cohort_uuid);
        $stmt->execute();
        $cohort = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cohort) {
            // Cohort program not found, redirect to the listing page
            header("Location: cohort_program_list.php");
            exit();
        }
    } else {
        // No cohort ID provided, redirect to the listing page
        header("Location: cohort_program_list.php");
        exit();
    }

    include('layout/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2>Edit Cohort Program</h2>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Program Details</h5>
                </div>
                <div class="card-body">
                    <form action="./endpoint/cohort_programs.php" method="POST" id="editCohortForm">
                        <input type="hidden" name="cohort_uuid" value="<?= $cohort['uuid'] ?>">
                        
                        <div class="mb-3">
                            <label for="reference" class="form-label">Program Reference</label>
                            <input type="text" class="form-control" id="reference" value="<?= htmlspecialchars($cohort['references']) ?>" readonly>
                            <small class="form-text text-muted">Reference number is auto-generated and cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="program_name" class="form-label">Program Name</label>
                            <input type="text" class="form-control" id="program_name" name="program_name" 
                                   value="<?= htmlspecialchars($cohort['name']) ?>" required>
                            <div class="invalid-feedback">Please provide a valid program name.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= $cohort['start_date'] ?>" required>
                                    <div class="invalid-feedback">Please provide a valid start date.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= $cohort['end_date'] ?>" required>
                                    <div class="invalid-feedback">Please provide a valid end date.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($cohort['descriptions']) ?></textarea>
                            <div class="invalid-feedback">Please provide a program description.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="4" required><?= htmlspecialchars($cohort['terms_conditions']) ?></textarea>
                            <div class="invalid-feedback">Please provide terms and conditions.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?= $cohort['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $cohort['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                            <small class="form-text text-muted">
                                Note: Setting status to 'Active' will automatically deactivate other active cohort programs.
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="cohort_program_list.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" name="update_cohort_program">Update Program</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('editCohortForm');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Validate end date is after start date
    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && endDate <= startDate) {
            endDateInput.setCustomValidity('End date must be after start date');
            endDateInput.classList.add('is-invalid');
            return false;
        } else {
            endDateInput.setCustomValidity('');
            endDateInput.classList.remove('is-invalid');
            return true;
        }
    }
    
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
    
    // Form submission validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity() || !validateDates()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>

<?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>