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
        
        // Get statistics for this cohort
        $stats_stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total_applications,
                COUNT(CASE WHEN status = 'submitted' THEN 1 END) as submitted_count,
                COUNT(CASE WHEN status = 'reviewed' THEN 1 END) as reviewed_count,
                COUNT(CASE WHEN status = 'allocated' THEN 1 END) as allocated_count,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
            FROM applications 
            WHERE cohort_uuid = :cohort_uuid
        ");
        $stats_stmt->bindParam(':cohort_uuid', $cohort_uuid);
        $stats_stmt->execute();
        $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
        
    } else {
        // No cohort ID provided, redirect to the listing page
        header("Location: cohort_program_list.php");
        exit();
    }
    
    include('layout/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Cohort Program Details</h2>
                <div>
                    <a href="edit_cohort_program.php?id=<?= $cohort['uuid'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Program
                    </a>
                    <a href="cohort_program_list.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($cohort['name']) ?></h5>
                        <span class="badge <?= $cohort['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?> fs-6">
                            <?= ucfirst($cohort['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Program Reference</h6>
                            <p class="lead"><?= htmlspecialchars($cohort['references']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Current Status</h6>
                            <p class="lead">
                                <span class="badge <?= $cohort['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?> p-2">
                                    <?= ucfirst($cohort['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Start Date</h6>
                            <p><?= date('F d, Y', strtotime($cohort['start_date'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">End Date</h6>
                            <p><?= date('F d, Y', strtotime($cohort['end_date'])) ?></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Created Date</h6>
                            <p><?= date('F d, Y g:i A', strtotime($cohort['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Last Updated</h6>
                            <p><?= date('F d, Y g:i A', strtotime($cohort['updated_at'])) ?></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Program Description</h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($cohort['descriptions'])) ?></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Terms & Conditions</h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($cohort['terms_conditions'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1"><?= $stats['total_applications'] ?></h4>
                                <small class="text-muted">Total Applications</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1"><?= $stats['submitted_count'] ?></h4>
                                <small class="text-muted">Submitted</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-warning mb-1"><?= $stats['reviewed_count'] ?></h4>
                                <small class="text-muted">Under Review</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1"><?= $stats['allocated_count'] ?></h4>
                                <small class="text-muted">Allocated</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-danger mb-1"><?= $stats['rejected_count'] ?></h4>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary mb-1">
                                    <?= $stats['total_applications'] > 0 ? round(($stats['allocated_count'] / $stats['total_applications']) * 100, 1) : 0 ?>%
                                </h4>
                                <small class="text-muted">Success Rate</small>
                            </div>
                        </div>
                    </div>

                    <?php if ($stats['total_applications'] > 0): ?>
                        <div class="mt-4">
                            <h6>Application Status Distribution</h6>
                            <div class="progress" style="height: 25px;">
                                <?php if ($stats['submitted_count'] > 0): ?>
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: <?= ($stats['submitted_count'] / $stats['total_applications']) * 100 ?>%" 
                                         title="Submitted: <?= $stats['submitted_count'] ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if ($stats['reviewed_count'] > 0): ?>
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: <?= ($stats['reviewed_count'] / $stats['total_applications']) * 100 ?>%" 
                                         title="Under Review: <?= $stats['reviewed_count'] ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if ($stats['allocated_count'] > 0): ?>
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= ($stats['allocated_count'] / $stats['total_applications']) * 100 ?>%" 
                                         title="Allocated: <?= $stats['allocated_count'] ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if ($stats['rejected_count'] > 0): ?>
                                    <div class="progress-bar bg-danger" role="progressbar" 
                                         style="width: <?= ($stats['rejected_count'] / $stats['total_applications']) * 100 ?>%" 
                                         title="Rejected: <?= $stats['rejected_count'] ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <span class="badge bg-info">Submitted</span>
                                    <span class="badge bg-warning">Under Review</span>
                                    <span class="badge bg-success">Allocated</span>
                                    <span class="badge bg-danger">Rejected</span>
                                </small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 text-center">
                            <p class="text-muted">No applications received for this cohort program yet.</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 text-center">
                        <?php if ($stats['total_applications'] > 0): ?>
                            <a href="application_list.php?cohort_uuid=<?= $cohort['uuid'] ?>" class="btn btn-primary">
                                <i class="fas fa-list"></i> View All Applications for This Cohort
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
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