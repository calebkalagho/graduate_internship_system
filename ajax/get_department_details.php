<?php
// ajax/get_department_details.php
session_start();
include('../conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}

if ($_POST && isset($_POST['dept_uuid'])) {
    $dept_uuid = trim($_POST['dept_uuid']);
    
    try {
        // Get department details
        $stmt = $conn->prepare("
            SELECT 
                d.uuid,
                d.name,
                d.description,
                d.post_address,
                d.physical_address,
                d.contacts,
                d.email_address,
                d.status,
                d.created_at,
                d.updated_at,
                COUNT(DISTINCT epg.uuid) as program_generals_count,
                COUNT(DISTINCT epd.uuid) as program_details_count,
                COUNT(DISTINCT a.uuid) as total_applications
            FROM departments d
            LEFT JOIN education_programs_generals epg ON d.uuid = epg.da_uuid
            LEFT JOIN education_programs_details epd ON epg.uuid = epd.general_pg_uuid
            LEFT JOIN education_details ed ON epd.uuid = ed.program_general
            LEFT JOIN applications a ON ed.applicant_uuid = a.applicant_uuid
            WHERE d.uuid = :dept_uuid
            GROUP BY d.uuid, d.name, d.description, d.post_address, d.physical_address, 
                     d.contacts, d.email_address, d.status, d.created_at, d.updated_at
        ");
        
        $stmt->bindParam(':dept_uuid', $dept_uuid);
        $stmt->execute();
        
        $department = $stmt->fetch();
        
        if ($department) {
            // Get program generals for this department
            $programs_stmt = $conn->prepare("
                SELECT 
                    epg.uuid,
                    epg.name,
                    epg.description,
                    COUNT(DISTINCT epd.uuid) as details_count,
                    COUNT(DISTINCT a.uuid) as applications_count
                FROM education_programs_generals epg
                LEFT JOIN education_programs_details epd ON epg.uuid = epd.general_pg_uuid
                LEFT JOIN education_details ed ON epd.uuid = ed.program_general
                LEFT JOIN applications a ON ed.applicant_uuid = a.applicant_uuid
                WHERE epg.da_uuid = :dept_uuid
                GROUP BY epg.uuid, epg.name, epg.description
                ORDER BY epg.name
            ");
            
            $programs_stmt->bindParam(':dept_uuid', $dept_uuid);
            $programs_stmt->execute();
            $programs = $programs_stmt->fetchAll();
            
            // Get recent applications
            $recent_apps_stmt = $conn->prepare("
                SELECT 
                    CONCAT(g.first_name, ' ', g.last_name) as applicant_name,
                    a.applied_date,
                    a.status,
                    epd.name as program_name
                FROM applications a
                JOIN graduate g ON a.applicant_uuid = g.graduate_uuid
                JOIN education_details ed ON a.applicant_uuid = ed.applicant_uuid
                JOIN education_programs_details epd ON ed.program_general = epd.uuid
                JOIN education_programs_generals epg ON epd.general_pg_uuid = epg.uuid
                WHERE epg.da_uuid = :dept_uuid
                ORDER BY a.applied_date DESC
                LIMIT 5
            ");
            
            $recent_apps_stmt->bindParam(':dept_uuid', $dept_uuid);
            $recent_apps_stmt->execute();
            $recent_applications = $recent_apps_stmt->fetchAll();
            
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-building"></i> <?= htmlspecialchars($department['name']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-info-circle text-primary"></i> Description</h6>
                                    <p class="text-muted"><?= htmlspecialchars($department['description'] ?: 'No description available') ?></p>
                                    
                                    <h6><i class="fas fa-envelope text-primary"></i> Contact Information</h6>
                                    <?php if ($department['email_address']): ?>
                                        <p><strong>Email:</strong> 
                                            <a href="mailto:<?= htmlspecialchars($department['email_address']) ?>">
                                                <?= htmlspecialchars($department['email_address']) ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($department['contacts']): ?>
                                        <p><strong>Phone:</strong> <?= htmlspecialchars($department['contacts']) ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="fas fa-map-marker-alt text-primary"></i> Address Information</h6>
                                    <?php if ($department['post_address']): ?>
                                        <p><strong>Postal Address:</strong><br>
                                        <?= nl2br(htmlspecialchars($department['post_address'])) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($department['physical_address']): ?>
                                        <p><strong>Physical Address:</strong><br>
                                        <?= nl2br(htmlspecialchars($department['physical_address'])) ?></p>
                                    <?php endif; ?>
                                    
                                    <h6><i class="fas fa-chart-bar text-primary"></i> Statistics</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success"><?= $department['program_generals_count'] ?></h4>
                                                <small>Program Categories</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-info"><?= $department['total_applications'] ?></h4>
                                                <small>Total Applications</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Programs Section -->
                    <?php if (!empty($programs)): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-list"></i> Program Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($programs as $program): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-success">
                                            <div class="card-body">
                                                <h6 class="font-weight-bold"><?= htmlspecialchars($program['name']) ?></h6>
                                                <p class="small text-muted"><?= htmlspecialchars($program['description'] ?: 'No description') ?></p>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <span class="badge badge-success"><?= $program['details_count'] ?></span>
                                                        <small class="d-block">Specific Programs</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="badge badge-info"><?= $program['applications_count'] ?></span>
                                                        <small class="d-block">Applications</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent Applications -->
                    <?php if (!empty($recent_applications)): ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-clock"></i> Recent Applications</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Program</th>
                                            <th>Applied Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_applications as $app): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($app['applicant_name']) ?></td>
                                                <td><?= htmlspecialchars($app['program_name']) ?></td>
                                                <td><?= date('M d, Y', strtotime($app['applied_date'])) ?></td>
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
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        } else {
            echo '<div class="alert alert-warning">Department not found.</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error loading department details: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>