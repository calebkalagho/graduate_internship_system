<?php
// ajax/get_full_profile.php
session_start();
include('../conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}

if ($_POST && isset($_POST['applicant_uuid'])) {
    $applicant_uuid = trim($_POST['applicant_uuid']);
    
    try {
        // Get complete applicant profile
        $stmt = $conn->prepare("
            SELECT 
                g.*,
                a.uuid as application_uuid,
                a.applied_date,
                a.status as application_status,
                a.cohort_uuid,
                cp.name as cohort_name,
                cp.description as cohort_description,
                cp.start_date as cohort_start,
                cp.end_date as cohort_end,
                ed.major,
                ed.specific_major,
                ed.name_of_institution,
                ed.completion_date,
                ed.other_general,
                ed.program_general,
                epd.name as program_name,
                epd.description as program_description,
                epg.name as general_program_name,
                epg.description as general_program_description,
                d.name as department_name,
                d.description as department_description,
                d.contacts as dept_contacts,
                d.email_address as dept_email
            FROM graduate g
            LEFT JOIN applications a ON g.graduate_uuid = a.applicant_uuid
            LEFT JOIN cohort_programs cp ON a.cohort_uuid = cp.uuid
            LEFT JOIN education_details ed ON g.graduate_uuid = ed.applicant_uuid
            LEFT JOIN education_programs_details epd ON ed.program_general = epd.uuid
            LEFT JOIN education_programs_generals epg ON epd.general_pg_uuid = epg.uuid
            LEFT JOIN departments d ON epg.da_uuid = d.uuid
            WHERE a.uuid = :applicant_uuid
        ");
        
        $stmt->bindParam(':applicant_uuid', $applicant_uuid);
        $stmt->execute();
        
        $profile = $stmt->fetch();
        
        if ($profile) {
            // Get additional application history if any
            $history_stmt = $conn->prepare("
                SELECT 
                    a.applied_date,
                    a.status,
                    cp.name as cohort_name,
                    epd.name as program_name
                FROM applications a
                LEFT JOIN cohort_programs cp ON a.cohort_uuid = cp.uuid
                LEFT JOIN education_details ed ON a.applicant_uuid = ed.applicant_uuid
                LEFT JOIN education_programs_details epd ON ed.program_general = epd.uuid
                WHERE a.applicant_uuid = :graduate_uuid
                ORDER BY a.applied_date DESC
            ");
            
            $history_stmt->bindParam(':graduate_uuid', $profile['graduate_uuid']);
            $history_stmt->execute();
            $application_history = $history_stmt->fetchAll();
            
            ?>
            <div class="row">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><strong>Full Name:</strong></div>
                                <div class="col-sm-8"><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></div>
                            </div>
                            <hr class="my-2">
                            
                            <?php if ($profile['email']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">
                                    <a href="mailto:<?= htmlspecialchars($profile['email']) ?>">
                                        <?= htmlspecialchars($profile['email']) ?>
                                    </a>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['phone']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Phone:</strong></div>
                                <div class="col-sm-8">
                                    <a href="tel:<?= htmlspecialchars($profile['phone']) ?>">
                                        <?= htmlspecialchars($profile['phone']) ?>
                                    </a>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['date_of_birth']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                                <div class="col-sm-8"><?= date('M d, Y', strtotime($profile['date_of_birth'])) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['gender']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Gender:</strong></div>
                                <div class="col-sm-8"><?= ucfirst(htmlspecialchars($profile['gender'])) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['address']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Address:</strong></div>
                                <div class="col-sm-8"><?= nl2br(htmlspecialchars($profile['address'])) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-sm-4"><strong>Registration Date:</strong></div>
                                <div class="col-sm-8"><?= date('M d, Y', strtotime($profile['created_at'])) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Current Application Status -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-file-alt"></i> Current Application</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><strong>Application ID:</strong></div>
                                <div class="col-sm-8">
                                    <code><?= htmlspecialchars($profile['application_uuid']) ?></code>
                                </div>
                            </div>
                            <hr class="my-2">
                            
                            <div class="row">
                                <div class="col-sm-4"><strong>Applied Date:</strong></div>
                                <div class="col-sm-8"><?= date('M d, Y H:i', strtotime($profile['applied_date'])) ?></div>
                            </div>
                            <hr class="my-2">
                            
                            <div class="row">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge badge-<?= $profile['application_status'] == 'submitted' ? 'warning' : ($profile['application_status'] == 'reviewed' ? 'success' : 'danger') ?> badge-lg">
                                        <i class="fas fa-<?= $profile['application_status'] == 'submitted' ? 'clock' : ($profile['application_status'] == 'reviewed' ? 'check' : 'times') ?>"></i>
                                        <?= ucfirst($profile['application_status']) ?>
                                    </span>
                                </div>
                            </div>
                            <hr class="my-2">
                            
                            <?php if ($profile['cohort_name']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Cohort:</strong></div>
                                <div class="col-sm-8">
                                    <strong><?= htmlspecialchars($profile['cohort_name']) ?></strong>
                                    <?php if ($profile['cohort_description']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($profile['cohort_description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['cohort_start'] && $profile['cohort_end']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Cohort Period:</strong></div>
                                <div class="col-sm-8">
                                    <?= date('M d, Y', strtotime($profile['cohort_start'])) ?> - 
                                    <?= date('M d, Y', strtotime($profile['cohort_end'])) ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Education Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap"></i> Education Background</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($profile['name_of_institution']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Institution:</strong></div>
                                <div class="col-sm-8"><?= htmlspecialchars($profile['name_of_institution']) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['major'] || $profile['specific_major']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Major/Field:</strong></div>
                                <div class="col-sm-8">
                                    <?= htmlspecialchars($profile['major'] ?: $profile['specific_major']) ?>
                                    <?php if ($profile['major'] && $profile['specific_major'] && $profile['major'] != $profile['specific_major']): ?>
                                        <br><small class="text-muted">Specialization: <?= htmlspecialchars($profile['specific_major']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['completion_date']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Graduation Date:</strong></div>
                                <div class="col-sm-8"><?= date('M Y', strtotime($profile['completion_date'])) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['other_general']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Additional Info:</strong></div>
                                <div class="col-sm-8"><?= nl2br(htmlspecialchars($profile['other_general'])) ?></div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Program Information -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-list-alt"></i> Applied Program</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($profile['department_name']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Department:</strong></div>
                                <div class="col-sm-8">
                                    <?= htmlspecialchars($profile['department_name']) ?>
                                    <?php if ($profile['department_description']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($profile['department_description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['general_program_name']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Program Category:</strong></div>
                                <div class="col-sm-8">
                                    <?= htmlspecialchars($profile['general_program_name']) ?>
                                    <?php if ($profile['general_program_description']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($profile['general_program_description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['program_name']): ?>
                            <div class="row">
                                <div class="col-sm-4"><strong>Specific Program:</strong></div>
                                <div class="col-sm-8">
                                    <strong><?= htmlspecialchars($profile['program_name']) ?></strong>
                                    <?php if ($profile['program_description']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($profile['program_description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr class="my-2">
                            <?php endif; ?>
                            
                            <?php if ($profile['dept_contacts'] || $profile['dept_email']): ?>
                            <div class="mt-3 p-2 bg-light rounded">
                                <strong><i class="fas fa-info-circle"></i> Department Contact:</strong><br>
                                <?php if ($profile['dept_contacts']): ?>
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($profile['dept_contacts']) ?><br>
                                <?php endif; ?>
                                <?php if ($profile['dept_email']): ?>
                                    <i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars($profile['dept_email']) ?>"><?= htmlspecialchars($profile['dept_email']) ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Application History -->
                <?php if (count($application_history) > 1): ?>
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Application History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Applied Date</th>
                                            <th>Program</th>
                                            <th>Cohort</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($application_history as $app): ?>
                                            <tr>
                                                <td><?= date('M d, Y H:i', strtotime($app['applied_date'])) ?></td>
                                                <td><?= htmlspecialchars($app['program_name'] ?: 'N/A') ?></td>
                                                <td><?= htmlspecialchars($app['cohort_name'] ?: 'N/A') ?></td>
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
                </div>
                <?php endif; ?>
                
                <!-- Additional Information -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> System Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Graduate UUID:</strong><br>
                                    <code class="small"><?= htmlspecialchars($profile['graduate_uuid']) ?></code>
                                </div>
                                <div class="col-md-3">
                                    <strong>Profile Created:</strong><br>
                                    <?= date('M d, Y H:i', strtotime($profile['created_at'])) ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Last Updated:</strong><br>
                                    <?= date('M d, Y H:i', strtotime($profile['updated_at'])) ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Profile Status:</strong><br>
                                    <span class="badge badge-<?= $profile['status'] == 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($profile['status'] ?: 'Active') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <div class="btn-group" role="group">
                    <a href="view_applicant__other_details.php?applicant_uuid=<?= $profile['id'] ?>" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Full Details Page
                    </a>
                    <button type="button" class="btn btn-success" 
                            onclick="updateStatusFromProfile('<?= $profile['application_uuid'] ?>', '<?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?>', '<?= $profile['application_status'] ?>')">
                        <i class="fas fa-edit"></i> Update Status
                    </button>
                    <button type="button" class="btn btn-info" onclick="exportProfile()">
                        <i class="fas fa-download"></i> Export Profile
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="printProfile()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
            
            <script>
            function updateStatusFromProfile(applicationUuid, applicantName, currentStatus) {
                // Close full profile modal
                $('#fullProfileModal').modal('hide');
                
                // Wait a moment then open status update modal
                setTimeout(function() {
                    $('#applicantUuid').val(applicationUuid);
                    $('#applicantNameDisplay').text(applicantName);
                    $('#current_status').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
                    $('#updateStatusModal').modal('show');
                }, 500);
            }
            
            function exportProfile() {
                // Create and trigger download of profile data
                const profileData = {
                    name: "<?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?>",
                    email: "<?= htmlspecialchars($profile['email'] ?: '') ?>",
                    phone: "<?= htmlspecialchars($profile['phone'] ?: '') ?>",
                    application_uuid: "<?= htmlspecialchars($profile['application_uuid']) ?>",
                    applied_date: "<?= $profile['applied_date'] ?>",
                    status: "<?= $profile['application_status'] ?>",
                    institution: "<?= htmlspecialchars($profile['name_of_institution'] ?: '') ?>",
                    major: "<?= htmlspecialchars($profile['major'] ?: $profile['specific_major'] ?: '') ?>",
                    department: "<?= htmlspecialchars($profile['department_name'] ?: '') ?>",
                    program: "<?= htmlspecialchars($profile['program_name'] ?: '') ?>",
                    cohort: "<?= htmlspecialchars($profile['cohort_name'] ?: '') ?>"
                };
                
                const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(profileData, null, 2));
                const downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                downloadAnchorNode.setAttribute("download", "applicant_profile_<?= $profile['application_uuid'] ?>.json");
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
            }
            
            function printProfile() {
                const printContent = document.getElementById('fullProfileModalBody').innerHTML;
                const printWindow = window.open('', '_blank');
                
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Applicant Profile - <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }
                            .card { border: 1px solid #ddd; margin-bottom: 20px; page-break-inside: avoid; }
                            .card-header { background-color: #f8f9fa; padding: 10px; font-weight: bold; border-bottom: 1px solid #ddd; }
                            .card-body { padding: 15px; }
                            .row { margin-bottom: 5px; }
                            .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
                            .badge-success { background-color: #28a745; color: white; }
                            .badge-warning { background-color: #ffc107; color: black; }
                            .badge-danger { background-color: #dc3545; color: white; }
                            .badge-info { background-color: #17a2b8; color: white; }
                            .badge-lg { padding: 6px 12px; font-size: 0.9em; }
                            .text-muted { color: #666; }
                            .bg-light { background-color: #f8f9fa; }
                            .rounded { border-radius: 4px; }
                            code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-family: monospace; }
                            table { border-collapse: collapse; width: 100%; margin-top: 10px; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f8f9fa; }
                            hr { margin: 8px 0; }
                            @media print { .btn-group { display: none !important; } }
                        </style>
                    </head>
                    <body>
                        <h1>Applicant Profile</h1>
                        <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                        <hr>
                        ${printContent.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')}
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
                setTimeout(() => {
                    printWindow.print();
                }, 250);
            }
            </script>
            
            <?php
        } else {
            echo '<div class="alert alert-warning">Applicant profile not found.</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error loading applicant profile: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>