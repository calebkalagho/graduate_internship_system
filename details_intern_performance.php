<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the intern performance details based on the id
    if (isset($_GET['id'])) {
        $performance_id = $_GET['id'];

        // Fetch intern performance details
        $stmt = $conn->prepare("SELECT ip.performance_id, ip.intern_id, ip.kpi_id, ip.score, ip.comments, ip.evaluator_id, ip.created_at, g1.name AS intern_name, k.kpi_name, g2.name AS evaluator_name, k.kpi_description as kpi_description
                                FROM `intern_performance` ip
                                INNER JOIN `graduate` g1 ON ip.intern_id = g1.id
                                INNER JOIN `graduate` g2 ON ip.evaluator_id = g2.id
                                INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
                                WHERE ip.performance_id = :performance_id");
        $stmt->bindParam(':performance_id', $performance_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $performance = $stmt->fetch();
        } else {
            echo "Performance not found!";
            exit();
        }

        // Fetch all performances for this intern for comparison
        $stmt_all = $conn->prepare("SELECT ip.score, ip.created_at, k.kpi_name
                                   FROM `intern_performance` ip
                                   INNER JOIN `kpi_metrics` k ON ip.kpi_id = k.kpi_id
                                   WHERE ip.intern_id = :intern_id
                                   ORDER BY ip.created_at DESC");
        $stmt_all->bindParam(':intern_id', $performance['intern_id']);
        $stmt_all->execute();
        $all_performances = $stmt_all->fetchAll();

        // Calculate analytics
        $total_scores = array_column($all_performances, 'score');
        $average_score = !empty($total_scores) ? array_sum($total_scores) / count($total_scores) : 0;
        $max_score = !empty($total_scores) ? max($total_scores) : 0;
        $min_score = !empty($total_scores) ? min($total_scores) : 0;
        $total_evaluations = count($all_performances);

        // Performance rating
        $current_score = intval($performance['score']);
        if ($current_score >= 90) {
            $rating = "Excellent";
            $rating_color = "success";
            $rating_icon = "fas fa-star";
        } elseif ($current_score >= 75) {
            $rating = "Good";
            $rating_color = "primary";
            $rating_icon = "fas fa-thumbs-up";
        } elseif ($current_score >= 60) {
            $rating = "Average";
            $rating_color = "warning";
            $rating_icon = "fas fa-minus-circle";
        } else {
            $rating = "Needs Improvement";
            $rating_color = "danger";
            $rating_icon = "fas fa-exclamation-triangle";
        }
    }

    include('layout/header.php');
    ?>

    <!-- Add Font Awesome and Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Performance Analysis</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="intern_perfomance_admin_list.php">Performance List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Export Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <button onclick="exportToPDF()" class="btn btn-danger me-2">
                        <i class="fas fa-file-pdf me-1"></i> Export to PDF
                    </button>
                    <button onclick="window.print()" class="btn btn-secondary me-2">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                    <a href="edit_intern_performance.php?id=<?= $performance['performance_id'] ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Edit Performance
                    </a>
                    <a href="intern_perfomance_admin_list.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Performance Overview Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-<?= $rating_color ?> text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?= $current_score ?></h4>
                                    <p class="card-text">Current Score</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="<?= $rating_icon ?> fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark"><?= $rating ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?= number_format($average_score, 1) ?></h4>
                                    <p class="card-text">Average Score</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-bar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?= $max_score ?></h4>
                                    <p class="card-text">Highest Score</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-trophy fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?= $total_evaluations ?></h4>
                                    <p class="card-text">Total Evaluations</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Performance Details -->
                <div class="col-lg-8">
                    <div class="card card-primary card-outline mb-4" id="performance-details">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-graduate me-2"></i>Performance Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%"><i class="fas fa-user me-2 text-primary"></i>Intern Name:</th>
                                            <td><strong><?= htmlspecialchars($performance['intern_name']) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-target me-2 text-success"></i>KPI Metric:</th>
                                            <td><?= htmlspecialchars($performance['kpi_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-star me-2 text-warning"></i>Score:</th>
                                            <td>
                                                <span class="badge bg-<?= $rating_color ?> fs-6"><?= intval($performance['score']) ?>/100</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%"><i class="fas fa-user-tie me-2 text-info"></i>Evaluator:</th>
                                            <td><?= htmlspecialchars($performance['evaluator_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-calendar me-2 text-secondary"></i>Date:</th>
                                            <td><?= date('F j, Y', strtotime($performance['created_at'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-medal me-2 text-<?= $rating_color ?>"></i>Rating:</th>
                                            <td><span class="badge bg-<?= $rating_color ?>"><?= $rating ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-comments me-2 text-primary"></i>Evaluator Comments</h5>
                                    <div class="alert alert-light border">
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($performance['comments'])) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Progress Bar -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-chart-line me-2 text-success"></i>Performance Level</h5>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-<?= $rating_color ?>" role="progressbar" 
                                             style="width: <?= $current_score ?>%" aria-valuenow="<?= $current_score ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?= $current_score ?>%
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1">Performance Score out of 100</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics & Recommendations -->
                <div class="col-lg-4">
                    <!-- Performance Chart -->
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie me-2"></i>Performance Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" width="300" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-lightbulb me-2"></i>Recommendations</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($current_score >= 90): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Excellent Performance!</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Consider for leadership roles</li>
                                        <li>Assign challenging projects</li>
                                        <li>Potential for early completion</li>
                                    </ul>
                                </div>
                            <?php elseif ($current_score >= 75): ?>
                                <div class="alert alert-primary">
                                    <i class="fas fa-thumbs-up me-2"></i>
                                    <strong>Good Performance</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Continue current trajectory</li>
                                        <li>Provide stretch assignments</li>
                                        <li>Regular feedback sessions</li>
                                    </ul>
                                </div>
                            <?php elseif ($current_score >= 60): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Average Performance</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Identify improvement areas</li>
                                        <li>Additional training needed</li>
                                        <li>Increase supervision</li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Needs Immediate Attention</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Immediate intervention required</li>
                                        <li>Create improvement plan</li>
                                        <li>Daily monitoring needed</li>
                                        <li>Consider additional support</li>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <!-- Performance Comparison -->
                            <div class="mt-3">
                                <h6><i class="fas fa-analytics me-2"></i>Performance Analysis</h6>
                                <small>
                                    <?php if ($current_score > $average_score): ?>
                                        <span class="text-success"><i class="fas fa-arrow-up"></i> 
                                        <?= number_format($current_score - $average_score, 1) ?> points above average</span>
                                    <?php elseif ($current_score < $average_score): ?>
                                        <span class="text-danger"><i class="fas fa-arrow-down"></i> 
                                        <?= number_format($average_score - $current_score, 1) ?> points below average</span>
                                    <?php else: ?>
                                        <span class="text-info"><i class="fas fa-equals"></i> At average performance level</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Performance History -->
            <?php if (count($all_performances) > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history me-2"></i>Performance History</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>KPI Metric</th>
                                            <th>Score</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($all_performances, 0, 5) as $perf): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($perf['kpi_name']) ?></td>
                                            <td>
                                                <?php
                                                $score = intval($perf['score']);
                                                $badge_class = $score >= 75 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $badge_class ?>"><?= $score ?></span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($perf['created_at'])) ?></td>
                                            <td>
                                                <?php if ($score >= 75): ?>
                                                    <i class="fas fa-check-circle text-success"></i> Good
                                                <?php elseif ($score >= 60): ?>
                                                    <i class="fas fa-exclamation-circle text-warning"></i> Average
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle text-danger"></i> Poor
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const performanceData = <?= json_encode(array_reverse(array_slice(array_column($all_performances, 'score'), -6))) ?>;
    const performanceLabels = <?= json_encode(array_reverse(array_slice(array_map(function($p) { 
        return date('M j', strtotime($p['created_at'])); 
    }, $all_performances), -6))) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: performanceLabels,
            datasets: [{
                label: 'Performance Score',
                data: performanceData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // PDF Export Function
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Title
        doc.setFontSize(20);
        doc.text('Intern Performance Report', 20, 20);
        
        // Basic Info
        doc.setFontSize(12);
        doc.text('Intern: <?= $performance['intern_name'] ?>', 20, 40);
        doc.text('KPI: <?= $performance['kpi_name'] ?>', 20, 50);
        doc.text('Score: <?= intval($performance['score']) ?>/100', 20, 60);
        doc.text('Rating: <?= $rating ?>', 20, 70);
        doc.text('Evaluator: <?= $performance['evaluator_name'] ?>', 20, 80);
        doc.text('Date: <?= date('F j, Y', strtotime($performance['created_at'])) ?>', 20, 90);
        
        // Comments
        doc.text('Comments:', 20, 110);
        const comments = doc.splitTextToSize('<?= str_replace(["\r", "\n"], ' ', $performance['comments']) ?>', 170);
        doc.text(comments, 20, 120);
        
        // Analytics
        doc.text('Performance Analytics:', 20, 160);
        doc.text('Average Score: <?= number_format($average_score, 1) ?>', 20, 170);
        doc.text('Highest Score: <?= $max_score ?>', 20, 180);
        doc.text('Total Evaluations: <?= $total_evaluations ?>', 20, 190);
        
        doc.save('performance-report-<?= $performance['intern_name'] ?>-<?= date('Y-m-d') ?>.pdf');
    }

    // Print Styles
    const printStyles = `
        <style media="print">
            .btn, .breadcrumb, .card-header .float-end { display: none !important; }
            .card { border: 1px solid #000 !important; }
            .badge { color: #000 !important; background-color: #fff !important; border: 1px solid #000 !important; }
        </style>
    `;
    document.head.insertAdjacentHTML('beforeend', printStyles);
    </script>

    <?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>