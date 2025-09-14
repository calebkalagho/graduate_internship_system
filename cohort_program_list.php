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
                    <h3 class="mb-0">All Cohort Programs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Cohort Programs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Cohort Programs</h3>
                    <a href="./add_cohort_program.php" class="btn btn-primary float-sm-end">
                        Add New Cohort program
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Terms & Conditions</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch cohort programs from the database
                            $stmt = $conn->prepare("SELECT * FROM `cohort_programs` ORDER BY `created_at` DESC");
                            $stmt->execute();
                            $result = $stmt->fetchAll();

                            if (count($result) > 0) {
                                foreach ($result as $row) {
                                    $uuid = $row['uuid'];
                                    $reference = $row['references'];
                                    $name = $row['name'];
                                    $description = $row['descriptions'];
                                    $start_date = $row['start_date'];
                                    $end_date = $row['end_date'];
                                    $terms_conditions = $row['terms_conditions'];
                                    $status = $row['status'];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($reference) ?></td>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td><?= htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '') ?></td>
                                    <td><?= date('M d, Y', strtotime($start_date)) ?></td>
                                    <td><?= date('M d, Y', strtotime($end_date)) ?></td>
                                    <td><?= htmlspecialchars(substr($terms_conditions, 0, 50)) . (strlen($terms_conditions) > 50 ? '...' : '') ?></td>
                                    <td>
                                        <span class="badge <?= $status == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_cohort_program.php?id=<?= $uuid ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="view_cohort_program.php?id=<?= $uuid ?>" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php if ($status == 'inactive'): ?>
                                                <button type="button" class="btn btn-sm btn-success" onclick="changeStatus('<?= $uuid ?>', 'active')" title="Activate">
                                                    <i class="fas fa-check"></i> Activate
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="changeStatus('<?= $uuid ?>', 'inactive')" title="Deactivate">
                                                    <i class="fas fa-times"></i> Deactivate
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center">No cohort programs found</td>
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

    <!-- Status Change Modal -->
    <div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusChangeModalLabel">Confirm Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the status of this cohort program?</p>
                    <div class="alert alert-warning">
                        <strong>Note:</strong> Activating a cohort program will automatically deactivate all other active cohort programs.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentUuid = '';
        let newStatus = '';

        function changeStatus(uuid, status) {
            currentUuid = uuid;
            newStatus = status;
            
            // Show the confirmation modal
            const modal = new bootstrap.Modal(document.getElementById('statusChangeModal'));
            modal.show();
        }

        document.getElementById('confirmStatusChange').addEventListener('click', function() {
            // Create a form to submit the status change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = './endpoint/cohort_programs.php';
            
            const uuidInput = document.createElement('input');
            uuidInput.type = 'hidden';
            uuidInput.name = 'uuid';
            uuidInput.value = currentUuid;
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'change_status';
            actionInput.value = '1';
            
            form.appendChild(uuidInput);
            form.appendChild(statusInput);
            form.appendChild(actionInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    </script>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>