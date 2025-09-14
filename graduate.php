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
        $graduate_uuid = $row['graduate_uuid'];

        // 1. Fetch new vacancies count
        $query = $conn->query("SELECT COUNT(*) FROM vacancies WHERE status = 'open'");
        $newVacancies = $query->fetchColumn();

        // 2. Fetch total applications for the current user
        $query = $conn->prepare("SELECT COUNT(*) FROM applications WHERE applicant_uuid = ?");
        $query->execute([$graduate_uuid]);
        $totalApplications = $query->fetchColumn();

        // 3. Fetch application status breakdown for current user
        $query = $conn->prepare("SELECT status, COUNT(*) as count FROM applications WHERE applicant_uuid = ? GROUP BY status");
        $query->execute([$graduate_uuid]);
        $applicationStatusBreakdown = $query->fetchAll(PDO::FETCH_ASSOC);

        // Convert to associative array for easier access
        $statusCounts = [];
        foreach ($applicationStatusBreakdown as $status) {
            $statusCounts[$status['status']] = $status['count'];
        }

        // 4. Fetch allocation details for current user
        $query = $conn->prepare("
            SELECT aa.*, a.status as application_status, a.applied_date 
            FROM allocate_applicants aa 
            INNER JOIN applications a ON aa.applications_uuid = a.uuid 
            WHERE aa.applicant_uuid = ? 
            ORDER BY aa.reporting_date DESC
        ");
        $query->execute([$graduate_uuid]);
        $allocations = $query->fetchAll(PDO::FETCH_ASSOC);

        // 5. Fetch current allocation status
        $query = $conn->prepare("SELECT COUNT(*) FROM allocate_applicants WHERE applicant_uuid = ? AND allocation_status = 'allocated'");
        $query->execute([$graduate_uuid]);
        $activeAllocations = $query->fetchColumn();

        // 6. Fetch performance data for current user
        $query = $conn->prepare("
            SELECT ip.*, ip.created_at as evaluation_date 
            FROM intern_performance ip 
            WHERE ip.intern_id = ? 
            ORDER BY ip.created_at DESC
        ");
        $query->execute([$graduate_uuid]);
        $performanceRecords = $query->fetchAll(PDO::FETCH_ASSOC);

        // 7. Calculate average performance score
        $avgPerformanceScore = 0;
        if (!empty($performanceRecords)) {
            $totalScore = array_sum(array_column($performanceRecords, 'score'));
            $avgPerformanceScore = round($totalScore / count($performanceRecords), 2);
        }

        // 8. Get recent applications (last 5)
        $query = $conn->prepare("
            SELECT a.*
            FROM applications a 
           
            WHERE a.applicant_uuid = ? 
            ORDER BY a.applied_date DESC 
            LIMIT 5
        ");
        $query->execute([$graduate_uuid]);
        $recentApplications = $query->fetchAll(PDO::FETCH_ASSOC);

        // 9. Monthly application trend (last 12 months)
        $query = $conn->prepare("
            SELECT 
                DATE_FORMAT(applied_date, '%Y-%m') as month,
                COUNT(*) as applications_count
            FROM applications 
            WHERE applicant_uuid = ? 
            AND applied_date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(applied_date, '%Y-%m')
            ORDER BY month ASC
        ");
        $query->execute([$graduate_uuid]);
        $monthlyTrend = $query->fetchAll(PDO::FETCH_ASSOC);

        // 10. Get completion status for allocated internships
        $completedInternships = 0;
        $ongoingInternships = 0;
        foreach ($allocations as $allocation) {
            if ($allocation['allocation_status'] == 'completed') {
                $completedInternships++;
            } elseif ($allocation['allocation_status'] == 'active') {
                $ongoingInternships++;
            }
        }
    }

    include('layout/headergraduate.php');
?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                        <p class="text-muted">Welcome back, <?= htmlspecialchars($user_name) ?>!</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <!-- Main Statistics Cards -->
                <div class="row mb-4">
                    <!-- New Vacancies -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3><?= $newVacancies ?></h3>
                                <p>Available Vacancies</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <a href="./vacancies_list_graduate.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Total Applications -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3><?= $totalApplications ?></h3>
                                <p>Total Applications</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <a href="./application_graduate_list.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Active Allocations -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3><?= $activeAllocations ?></h3>
                                <p>Active Internships</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <a href="./internship_status.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Average Performance -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3><?= $avgPerformanceScore ?><small>%</small></h3>
                                <p>Avg Performance Score</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <a href="./performance_report.php" class="small-box-footer link-dark">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Application Status Breakdown -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Application Status Breakdown</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-success"><i class="bi bi-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Accepted</span>
                                                <span class="info-box-number"><?= $statusCounts['allocated'] ?? 0 ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-warning"><i class="bi bi-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pending</span>
                                                <span class="info-box-number"><?= $statusCounts['submitted'] ?? 0 ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rejected</span>
                                                <span class="info-box-number"><?= $statusCounts['rejected'] ?? 0 ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-info"><i class="bi bi-eye"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Under Review</span>
                                                <span class="info-box-number"><?= $statusCounts['reviewed'] ?? 0 ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Internship Progress -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Internship Progress</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="progress-group">
                                            <span class="progress-text">Completed Internships</span>
                                            <span class="float-end"><?= $completedInternships ?></span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" style="width: <?= $totalApplications > 0 ? ($completedInternships / $totalApplications) * 100 : 0 ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="progress-group">
                                            <span class="progress-text">Ongoing Internships</span>
                                            <span class="float-end"><?= $ongoingInternships ?></span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary" style="width: <?= $totalApplications > 0 ? ($ongoingInternships / $totalApplications) * 100 : 0 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Applications</h3>
                                <div class="card-tools">
                                    <a href="./application_graduate_list.php" class="btn btn-sm btn-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                          
                                            <th>Application Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentApplications)): ?>
                                            <?php foreach ($recentApplications as $application): ?>
                                                <tr>
                                                
                                                    <td><?= date('M d, Y', strtotime($application['applied_date'])) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        switch($application['status']) {
                                                            case 'accepted': $statusClass = 'success'; break;
                                                            case 'rejected': $statusClass = 'danger'; break;
                                                            case 'pending': $statusClass = 'warning'; break;
                                                            default: $statusClass = 'info';
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($application['status']) ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="application_details.php?id=<?= $application['application_id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No applications found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Analytics -->
                <?php if (!empty($performanceRecords)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Performance Evaluations</h3>
                                <div class="card-tools">
                                    <a href="./performance_report.php" class="btn btn-sm btn-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (array_slice($performanceRecords, 0, 3) as $performance): ?>
                                        <div class="col-md-4">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <h5 class="card-title">Evaluation Score</h5>
                                                    <h2 class="text-primary"><?= $performance['score'] ?>%</h2>
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            Evaluated on <?= date('M d, Y', strtotime($performance['evaluation_date'])) ?>
                                                        </small>
                                                    </p>
                                                    <?php if (!empty($performance['comments'])): ?>
                                                        <p class="card-text">
                                                            <em>"<?= htmlspecialchars(substr($performance['comments'], 0, 100)) ?>..."</em>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Application Trend Chart (if you have Chart.js included) -->
                <?php if (!empty($monthlyTrend)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Application Trend (Last 12 Months)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="applicationTrendChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                // Application Trend Chart
                const ctx = document.getElementById('applicationTrendChart').getContext('2d');
                const trendData = <?= json_encode($monthlyTrend) ?>;
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(item => item.month),
                        datasets: [{
                            label: 'Applications Submitted',
                            data: trendData.map(item => item.applications_count),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                </script>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>