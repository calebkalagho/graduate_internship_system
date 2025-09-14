<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's data from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
        $graduate_uuid = $row['graduate_uuid'];

        // Get application statistics for the logged-in graduate
        $stats_stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_applications,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_applications,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications,
                SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review_applications
            FROM `applications` 
            WHERE applicant_uuid = :graduate_uuid
        ");
        $stats_stmt->execute([':graduate_uuid' => $graduate_uuid]);
        $application_stats = $stats_stmt->fetch();

        // Get recent application activity (last 30 days)
        $recent_stmt = $conn->prepare("
            SELECT COUNT(*) as recent_applications
            FROM `applications` 
            WHERE applicant_uuid = :graduate_uuid 
            AND applied_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        ");
        $recent_stmt->execute([':graduate_uuid' => $graduate_uuid]);
        $recent_activity = $recent_stmt->fetch();
    }

    include('layout/headergraduate.php');
?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">My Applications</h3>
                        <p class="text-muted">Track and manage your internship applications</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="dashboard_graduate.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Applications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                
                <!-- Application Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3><?= $application_stats['total_applications'] ?></h3>
                                <p>Total Applications</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3><?= $application_stats['pending_applications'] ?></h3>
                                <p>Pending Review</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3><?= $application_stats['accepted_applications'] ?></h3>
                                <p>Accepted</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3><?= $recent_activity['recent_applications'] ?></h3>
                                <p>Recent (30 days)</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Application Status Overview</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-success"><i class="bi bi-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Accepted</span>
                                                <span class="info-box-number"><?= $application_stats['accepted_applications'] ?></span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: <?= $application_stats['total_applications'] > 0 ? ($application_stats['accepted_applications'] / $application_stats['total_applications']) * 100 : 0 ?>%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    <?= $application_stats['total_applications'] > 0 ? round(($application_stats['accepted_applications'] / $application_stats['total_applications']) * 100, 1) : 0 ?>% of total
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-warning"><i class="bi bi-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pending</span>
                                                <span class="info-box-number"><?= $application_stats['pending_applications'] ?></span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" style="width: <?= $application_stats['total_applications'] > 0 ? ($application_stats['pending_applications'] / $application_stats['total_applications']) * 100 : 0 ?>%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    <?= $application_stats['total_applications'] > 0 ? round(($application_stats['pending_applications'] / $application_stats['total_applications']) * 100, 1) : 0 ?>% of total
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-info"><i class="bi bi-eye"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Under Review</span>
                                                <span class="info-box-number"><?= $application_stats['under_review_applications'] ?></span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" style="width: <?= $application_stats['total_applications'] > 0 ? ($application_stats['under_review_applications'] / $application_stats['total_applications']) * 100 : 0 ?>%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    <?= $application_stats['total_applications'] > 0 ? round(($application_stats['under_review_applications'] / $application_stats['total_applications']) * 100, 1) : 0 ?>% of total
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rejected</span>
                                                <span class="info-box-number"><?= $application_stats['rejected_applications'] ?></span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-danger" style="width: <?= $application_stats['total_applications'] > 0 ? ($application_stats['rejected_applications'] / $application_stats['total_applications']) * 100 : 0 ?>%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    <?= $application_stats['total_applications'] > 0 ? round(($application_stats['rejected_applications'] / $application_stats['total_applications']) * 100, 1) : 0 ?>% of total
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">My Application History</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="searchTable" class="form-control" placeholder="Search applications...">
                                        <div class="input-group-append">
                                            <div class="btn btn-primary">
                                                <i class="bi bi-search"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <?php if ($application_stats['total_applications'] > 0): ?>
                                    <table id="applicationsTable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Application ID</th>
                                             
                                                <th>Position/Program</th>
                                                <th>Applied Date</th>
                                                <th>Status</th>
                                                <th>Days Since Applied</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Enhanced query to fetch applications with more details
                                            $stmt = $conn->prepare("
                                                SELECT 
                                                    a.application_id,
                                                    a.uuid,
                                                    a.applied_date,
                                                    a.status,
                                                    cp.name  as program_name,
                                                    cp.descriptions as program_description,
                                                  
                                                
                                                    DATEDIFF(CURRENT_DATE, a.applied_date) as days_since_applied
                                                FROM `applications` a
                                                LEFT JOIN `cohort_programs` cp ON a.cohort_uuid = cp.uuid
                                              
                                                WHERE a.applicant_uuid = :graduate_uuid
                                                ORDER BY a.applied_date DESC
                                            ");
                                            $stmt->execute([':graduate_uuid' => $graduate_uuid]);
                                            $applications = $stmt->fetchAll();

                                            foreach ($applications as $app) {
                                                // Determine status badge class
                                                $status_class = '';
                                                $status_icon = '';
                                                switch(strtolower($app['status'])) {
                                                    case 'accepted':
                                                        $status_class = 'success';
                                                        $status_icon = 'check-circle';
                                                        break;
                                                    case 'rejected':
                                                        $status_class = 'danger';
                                                        $status_icon = 'x-circle';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'warning';
                                                        $status_icon = 'clock';
                                                        break;
                                                    case 'under_review':
                                                        $status_class = 'info';
                                                        $status_icon = 'eye';
                                                        break;
                                                    default:
                                                        $status_class = 'secondary';
                                                        $status_icon = 'question-circle';
                                                }
                                                
                                                // Determine urgency based on days since applied
                                                $urgency_class = '';
                                                if ($app['days_since_applied'] > 30 && $app['status'] == 'pending') {
                                                    $urgency_class = 'table-warning';
                                                } elseif ($app['days_since_applied'] > 60 && $app['status'] == 'pending') {
                                                    $urgency_class = 'table-danger';
                                                }
                                            ?>
                                                <tr class="<?= $urgency_class ?>">
                                                    <td>
                                                        <strong>#<?= str_pad($app['application_id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                                    </td>
                                                    <td>
                                                    
                                                        <?php if ($app['program_name']): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($app['program_name']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($app['vacancy_title'] ?? $app['program_name'] ?? 'Position not specified') ?>
                                                      
                                                    </td>
                                                    <td>
                                                        <?= date('M d, Y', strtotime($app['applied_date'])) ?>
                                                        <br><small class="text-muted"><?= date('H:i A', strtotime($app['applied_date'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $status_class ?>">
                                                            <i class="bi bi-<?= $status_icon ?>"></i>
                                                            <?= ucwords(str_replace('_', ' ', $app['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark"><?= $app['days_since_applied'] ?> days</span>
                                                        <?php if ($app['days_since_applied'] > 30 && $app['status'] == 'pending'): ?>
                                                            <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Long pending</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="application_details.php?id=<?= $app['application_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="View Details">
                                                                <i class="bi bi-eye"></i> View
                                                            </a>
                                                            <?php if ($app['status'] == 'pending'): ?>
                                                                <a href="edit_application.php?id=<?= $app['application_id'] ?>" 
                                                                   class="btn btn-sm btn-outline-warning"
                                                                   title="Edit Application">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </a>
                                                            <?php endif; ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                                    data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="download_application.php?id=<?= $app['application_id'] ?>">
                                                                    <i class="bi bi-download"></i> Download PDF
                                                                </a></li>
                                                                <?php if ($app['status'] == 'accepted'): ?>
                                                                    <li><a class="dropdown-item" href="internship_details.php?app_id=<?= $app['application_id'] ?>">
                                                                        <i class="bi bi-building"></i> View Internship
                                                                    </a></li>
                                                                <?php endif; ?>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item text-muted" href="#">
                                                                    <i class="bi bi-info-circle"></i> Application Status: <?= ucwords($app['status']) ?>
                                                                </a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #ccc;"></i>
                                        <h4 class="mt-3 text-muted">No Applications Yet</h4>
                                        <p class="text-muted">You haven't submitted any applications yet. Start by browsing available vacancies.</p>
                                        <a href="vacancies_list_graduate.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Browse Vacancies
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($application_stats['total_applications'] > 0): ?>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info">
                                            Showing <?= $application_stats['total_applications'] ?> applications
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="float-end">
                                            <button class="btn btn-sm btn-success" onclick="exportApplications()">
                                                <i class="bi bi-download"></i> Export to CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Search functionality
        document.getElementById('searchTable').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#applicationsTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Export to CSV function
        function exportApplications() {
            const table = document.getElementById('applicationsTable');
            if (!table) return;
            
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [];
                const cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length - 1; j++) { // Exclude action column
                    let cellText = cols[j].innerText.replace(/"/g, '""').replace(/\n/g, ' ');
                    row.push('"' + cellText + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
            const downloadLink = document.createElement('a');
            downloadLink.download = 'my_applications_<?= date('Y-m-d') ?>.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
    exit();
}
?>