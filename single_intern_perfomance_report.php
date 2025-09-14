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
        
        // Get performance analytics for the logged-in graduate
        
        // 1. Overall performance statistics
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total_evaluations,
                AVG(score) as average_score,
                MIN(score) as lowest_score,
                MAX(score) as highest_score
            FROM `intern_performance` ip
            INNER JOIN `graduate` g ON ip.intern_id = g.id
            WHERE g.id = ?
        ");
        $stmt->execute([$user_id]);
        $performanceStats = $stmt->fetch();

        // 2. Performance by KPI category
        $stmt = $conn->prepare("
            SELECT 
                k.kpi_name,
                COUNT(*) as evaluation_count,
                AVG(ip.score) as avg_score,
                MAX(ip.score) as max_score,
                MIN(ip.score) as min_score
            FROM `intern_performance` ip
            INNER JOIN `graduate` g ON ip.intern_id = g.id
            INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
            WHERE g.id = ?
            GROUP BY k.kpi_id, k.kpi_name
            ORDER BY avg_score DESC
        ");
        $stmt->execute([$user_id]);
        $kpiPerformance = $stmt->fetchAll();

        // 3. Performance trend over time
        $stmt = $conn->prepare("
            SELECT 
                DATE_FORMAT(ip.created_at, '%Y-%m') as month,
                AVG(ip.score) as avg_monthly_score,
                COUNT(*) as evaluations_count
            FROM `intern_performance` ip
            INNER JOIN `graduate` g ON ip.intern_id = g.id
            WHERE g.id = ?
            GROUP BY DATE_FORMAT(ip.created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $stmt->execute([$user_id]);
        $monthlyTrend = $stmt->fetchAll();

        // 4. Recent detailed performance records
        $stmt = $conn->prepare("
            SELECT 
                ip.performance_id, 
                k.kpi_name, 
                ip.score, 
                ip.comments, 
                g2.name AS evaluator_name, 
                ip.created_at,
                d.name
            FROM `intern_performance` ip
            INNER JOIN `graduate` g1 ON ip.intern_id = g1.id
            LEFT JOIN `graduate` g2 ON ip.evaluator_id = g2.id
            INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
            LEFT JOIN `allocate_applicants` aa ON g1.graduate_uuid = aa.applicant_uuid
            LEFT JOIN `departments` d ON aa.department_uuid = d.uuid
            WHERE g1.id = ?
            ORDER BY ip.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $detailedPerformance = $stmt->fetchAll();

        // 5. Performance grade calculation
        function getPerformanceGrade($score) {
            if ($score >= 90) return ['A+', 'success'];
            if ($score >= 80) return ['A', 'success'];
            if ($score >= 70) return ['B+', 'info'];
            if ($score >= 60) return ['B', 'info'];
            if ($score >= 50) return ['C', 'warning'];
            return ['F', 'danger'];
        }
    }

    include('layout/headergraduate.php');
?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">My Performance Report</h3>
                        <p class="text-muted">Performance analytics and evaluation history</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="graduate.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Performance Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                
                <?php if ($performanceStats['total_evaluations'] > 0): ?>
                    
                    <!-- Performance Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box text-bg-primary">
                                <div class="inner">
                                    <h3><?= round($performanceStats['average_score'], 1) ?>%</h3>
                                    <p>Average Score</p>
                                </div>
                                <div class="icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box text-bg-success">
                                <div class="inner">
                                    <h3><?= $performanceStats['highest_score'] ?>%</h3>
                                    <p>Highest Score</p>
                                </div>
                                <div class="icon">
                                    <i class="bi bi-trophy"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box text-bg-info">
                                <div class="inner">
                                    <h3><?= $performanceStats['total_evaluations'] ?></h3>
                                    <p>Total Evaluations</p>
                                </div>
                                <div class="icon">
                                    <i class="bi bi-clipboard-data"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box text-bg-warning">
                                <div class="inner">
                                    <?php 
                                    $grade = getPerformanceGrade($performanceStats['average_score']);
                                    ?>
                                    <h3><?= $grade[0] ?></h3>
                                    <p>Overall Grade</p>
                                </div>
                                <div class="icon">
                                    <i class="bi bi-award"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPI Performance Breakdown -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Performance by KPI Category</h3>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($kpiPerformance as $kpi): ?>
                                        <div class="progress-group mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="progress-text"><?= htmlspecialchars($kpi['kpi_name']) ?></span>
                                                <span class="float-end">
                                                    <b><?= round($kpi['avg_score'], 1) ?>%</b> 
                                                    <small class="text-muted">(<?= $kpi['evaluation_count'] ?> evaluations)</small>
                                                </span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <?php
                                                $progressClass = 'bg-primary';
                                                if ($kpi['avg_score'] >= 80) $progressClass = 'bg-success';
                                                elseif ($kpi['avg_score'] >= 60) $progressClass = 'bg-info';
                                                elseif ($kpi['avg_score'] >= 50) $progressClass = 'bg-warning';
                                                else $progressClass = 'bg-danger';
                                                ?>
                                                <div class="progress-bar <?= $progressClass ?>" style="width: <?= $kpi['avg_score'] ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Performance Summary</h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-box bg-light mb-3">
                                        <span class="info-box-icon bg-success"><i class="bi bi-arrow-up"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Best Performance</span>
                                            <span class="info-box-number"><?= $performanceStats['highest_score'] ?>%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-box bg-light mb-3">
                                        <span class="info-box-icon bg-warning"><i class="bi bi-arrow-down"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Lowest Score</span>
                                            <span class="info-box-number"><?= $performanceStats['lowest_score'] ?>%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Improvement</span>
                                            <span class="info-box-number">
                                                <?= $performanceStats['highest_score'] - $performanceStats['lowest_score'] ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Trend Chart -->
                    <?php if (count($monthlyTrend) > 1): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Performance Trend Over Time</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceTrendChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Detailed Performance Records -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detailed Performance History</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm btn-success" onclick="exportToCSV()">
                                            <i class="bi bi-download"></i> Export CSV
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table id="performanceTable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>KPI Category</th>
                                                <th>Score</th>
                                                <th>Grade</th>
                                                <th>Evaluator</th>
                                                <th>Department</th>
                                                <th>Comments</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detailedPerformance as $record): ?>
                                                <?php $grade = getPerformanceGrade($record['score']); ?>
                                                <tr>
                                                    <td><?= date('M d, Y', strtotime($record['created_at'])) ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($record['kpi_name']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $grade[1] ?>"><?= $record['score'] ?>%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $grade[1] ?>"><?= $grade[0] ?></span>
                                                    </td>
                                                    <td><?= htmlspecialchars($record['evaluator_name'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($record['department_name'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if (!empty($record['comments'])): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                    data-bs-toggle="tooltip" 
                                                                    title="<?= htmlspecialchars($record['comments']) ?>">
                                                                <i class="bi bi-chat-text"></i> View
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted">No comments</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href='performance_details.php?id=<?= $record['performance_id'] ?>' 
                                                           class='btn btn-info btn-sm'>
                                                            <i class="bi bi-eye"></i> Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- No Performance Data -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-graph-up" style="font-size: 4rem; color: #ccc;"></i>
                                    <h4 class="mt-3 text-muted">No Performance Data Available</h4>
                                    <p class="text-muted">You haven't been evaluated yet. Performance data will appear here once evaluations are conducted.</p>
                                    <a href="graduate.php" class="btn btn-primary">
                                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <?php if ($performanceStats['total_evaluations'] > 0): ?>
    <script>
        // Performance Trend Chart
        <?php if (count($monthlyTrend) > 1): ?>
        const ctx = document.getElementById('performanceTrendChart').getContext('2d');
        const trendData = <?= json_encode($monthlyTrend) ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Average Score (%)',
                    data: trendData.map(item => parseFloat(item.avg_monthly_score)),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Export to CSV function
        function exportToCSV() {
            const table = document.getElementById('performanceTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [];
                const cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length - 1; j++) { // Exclude action column
                    let cellText = cols[j].innerText.replace(/"/g, '""');
                    row.push('"' + cellText + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
            const downloadLink = document.createElement('a');
            downloadLink.download = 'performance_report_<?= date('Y-m-d') ?>.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
    <?php endif; ?>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
    exit();
}
?>