<?php
// ajax/get_program_details.php
session_start();
include('../conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}

if ($_POST && isset($_POST['program_uuid'])) {
    $program_uuid = trim($_POST['program_uuid']);
    
    try {
        // Get program details
        $stmt = $conn->prepare("
            SELECT 
                epd.uuid,
                epd.name,
                epd.description,
                epd.created_at,
                epd.updated_at,
                epg.uuid as general_uuid,
                epg.name as general_name,
                epg.description as general_description,
                d.uuid as dept_uuid,
                d.name as department_name,
                d.description as dept_description,
                COUNT(DISTINCT a.uuid) as total_applications,
                COUNT(DISTINCT CASE WHEN a.status = 'submitted' THEN a.uuid END) as pending_applications,
                COUNT(DISTINCT CASE WHEN a.status = 'reviewed' THEN a.uuid END) as reviewed_applications,
                COUNT(DISTINCT CASE WHEN a.status = 'rejected' THEN a.uuid END) as rejected_applications
            FROM education_programs_details epd
            INNER JOIN education_programs_generals epg ON epd.general_pg_uuid = epg.uuid
            INNER JOIN departments d ON epg.da_uuid = d.uuid
            LEFT JOIN education_details ed ON epd.uuid = ed.program_general
            LEFT JOIN applications a ON ed.applicant_uuid = a.applicant_uuid
            WHERE epd.uuid = :program_uuid
            GROUP BY epd.uuid, epd.name, epd.description, epd.created_at, epd.updated_at,
                     epg.uuid, epg.name, epg.description, d.uuid, d.name, d.description
        ");
        
        $stmt->bindParam(':program_uuid', $program_uuid);
        $stmt->execute();
        
        $program = $stmt->fetch();
        
        if ($program) {
            // Get applicant details for this program
            $applicants_stmt = $conn->prepare("
                SELECT 
                    CONCAT(g.first_name, ' ', g.last_name) as applicant_name,
                    g.email,
                    g.phone,
                    a.applied_date,
                    a.status,
                    ed.major,
                    ed.specific_major,
                    ed.name_of_institution,
                    ed.completion_date
                FROM applications a
                JOIN graduate g ON a.applicant_uuid = g.graduate_uuid
                JOIN education_details ed ON a.applicant_uuid = ed.applicant_uuid
                WHERE ed.program_general = :program_uuid
                ORDER BY a.applied_date DESC
                LIMIT 10
            ");
            
            $applicants_stmt->bindParam(':program_uuid', $program_uuid);
            $applicants_stmt->execute();
            $applicants = $applicants_stmt->fetchAll();
            
            // Get major distribution
            $majors_stmt = $conn->prepare("
                SELECT 
                    CASE 
                        WHEN ed.major IS NOT NULL AND ed.major != '' THEN ed.major
                        WHEN ed.specific_major IS NOT NULL AND ed.specific_major != '' THEN ed.specific_major
                        ELSE 'General'
                    END as major_name,
                    COUNT(*) as count
                FROM education_details ed
                JOIN applications a ON ed.applicant_uuid = a.applicant_uuid
                WHERE ed.program_general = :program_uuid
                GROUP BY major_name
                ORDER BY count DESC
                LIMIT 5
            ");
            
            $majors_stmt->bindParam(':program_uuid', $program_uuid);
            $majors_stmt->execute();
            $majors = $majors_stmt->fetchAll();
            
            ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- Program Overview -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($program['name']) ?>
                            </h5>
                            <small><i class="fas fa-layer-group"></i> <?= htmlspecialchars($program['general_name']) ?></small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6><i class="fas fa-info-circle text-success"></i> Program Description</h6>
                                    <p class="text-muted"><?= htmlspecialchars($program['description'] ?: 'No description available') ?></p>
                                    
                                    <h6><i class="fas fa-building text-success"></i> Department</h6>
                                    <p><strong><?= htmlspecialchars($program['department_name']) ?></strong></p>
                                    <p class="small text-muted"><?= htmlspecialchars($program['dept_description'] ?: 'No department description') ?></p>
                                    
                                    <h6><i class="fas fa-layer-group text-success"></i> General Program Category</h6>
                                    <p><strong><?= htmlspecialchars($program['general_name']) ?></strong></p>
                                    <p class="small text-muted"><?= htmlspecialchars($program['general_description'] ?: 'No category description') ?></p>
                                </div>
                                
                                <div class="col-md-4">
                                    <h6><i class="fas fa-chart-pie text-success"></i> Application Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body p-2">
                                                    <h4 class="text-primary"><?= $program['total_applications'] ?></h4>
                                                    <small>Total Applications</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body p-2">
                                                    <h4 class="text-warning"><?= $program['pending_applications'] ?></h4>
                                                    <small>Pending</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body p-2">
                                                    <h4 class="text-success"><?= $program['reviewed_applications'] ?></h4>
                                                    <small>Reviewed</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body p-2">
                                                    <h4 class="text-danger"><?= $program['rejected_applications'] ?></h4>
                                                    <small>Rejected</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6><i class="fas fa-calendar text-success"></i> Program Info</h6>
                                        <p class="small">
                                            <strong>Created:</strong> <?= date('M d, Y', strtotime($program['created_at'])) ?><br>
                                            <strong>Updated:</strong> <?= date('M d, Y', strtotime($program['updated_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Major Distribution -->
                    <?php if (!empty($majors)): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Popular Majors/Specializations</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($majors as $major): ?>
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <span><?= htmlspecialchars($major['major_name']) ?></span>
                                            <span class="badge badge-info"><?= $major['count'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent Applicants -->
                    <?php if (!empty($applicants)): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Recent Applicants (Last 10)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Contact</th>
                                            <th>Major/Specialization</th>
                                            <th>Institution</th>
                                            <th>Graduation</th>
                                            <th>Applied Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applicants as $app): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($app['applicant_name']) ?></strong>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php if ($app['email']): ?>
                                                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($app['email']) ?><br>
                                                        <?php endif; ?>
                                                        <?php if ($app['phone']): ?>
                                                            <i class="fas fa-phone"></i> <?= htmlspecialchars($app['phone']) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($app['major'] ?: $app['specific_major'] ?: 'General') ?>
                                                </td>
                                                <td>
                                                    <small><?= htmlspecialchars($app['name_of_institution'] ?: 'Not specified') ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($app['completion_date']): ?>
                                                        <?= date('Y', strtotime($app['completion_date'])) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y', strtotime($app['applied_date'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $app['status'] == 'submitted' ? 'warning' : ($app['status'] == 'reviewed' ? 'success' : 'danger') ?>">
                                                        <?= ucfirst($app['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($applicants) >= 10): ?>
                                <div class="text-center mt-3">
                                    <small class="text-muted">Showing latest 10 applicants</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="text-center mt-3">
                <button class="btn btn-outline-primary btn-sm" onclick="filterByProgram('<?= $program['uuid'] ?>')">
                    <i class="fas fa-filter"></i> Filter Main List by This Program
                </button>
            </div>
            
            <script>
            function filterByProgram(programUuid) {
                // Close modal and apply filter to main page
                $('#programModal').modal('hide');
                
                // Set the program filter and submit form
                setTimeout(function() {
                    $('#program').val(programUuid);
                    $('#filterForm').submit();
                }, 500);
            }
            </script>
            <?php
        } else {
            echo '<div class="alert alert-warning">Program not found.</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error loading program details: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>