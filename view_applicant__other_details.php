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
        $guuid = $row['graduate_uuid'];

        // Get the applicant's UUID from the query string
        $applicant_uuid = $_GET['applicant_uuid'] ?? null;
        
        if (!$applicant_uuid) {
            die('No applicant UUID provided');
        }

        // Query to fetch detailed information of the applicant
        $stmt = $conn->prepare("
            SELECT first_name, middle_name, last_name, gender, dob, national_id, mobile, email, graduate_uuid
            FROM `graduate`
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $applicant_uuid);
        $stmt->execute();
        $personal_details = $stmt->fetch();
        
        if (!$personal_details) {
            die('Applicant not found');
        }
        
        // Use the applicant's UUID for all subsequent queries
        $target_uuid = $personal_details['graduate_uuid'];

        // Fetch Guardian Details
        $stmt = $conn->prepare("
            SELECT name, mobile_number
            FROM `guardians`
            WHERE applicant_uuid = :applicant_uuid
        ");
        $stmt->bindParam(':applicant_uuid', $target_uuid);
        $stmt->execute();
        $guardian_details = $stmt->fetchAll();

        // Fetch Education Details
        $stmt = $conn->prepare("
            SELECT 
                ed.program_general,
                epg.name AS program_general_name,
                ed.major,
                ed.specific_major,
                epd.name AS specific_major_name,
                ed.name_of_institution,
                ed.completion_date,
                ed.other_general
            FROM `education_details` ed
            LEFT JOIN `education_programs_generals` epg ON ed.program_general = epg.uuid
            LEFT JOIN `education_programs_details` epd ON ed.specific_major = epd.uuid
            WHERE ed.applicant_uuid = :applicant_uuid
        ");
        $stmt->bindParam(':applicant_uuid', $target_uuid);
        $stmt->execute();
        $education_details = $stmt->fetchAll();

        // Fetch Preferred Districts
        $stmt = $conn->prepare("
            SELECT 
                sd.service_district_id, 
                sd.uuid, 
                sd.applicant_uuid, 
                sd.district_id, 
                d.name AS district_name
            FROM 
                service_district sd
            JOIN 
                districts d ON sd.district_id = d.id
            WHERE 
                sd.applicant_uuid = :applicant_uuid
        ");
        $stmt->bindParam(':applicant_uuid', $target_uuid);
        $stmt->execute();
        $preferred_districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Bank Details
        $stmt = $conn->prepare("
            SELECT bank_name, bank_branch, account_name, account_number
            FROM `bank_details`
            WHERE applicant_uuid = :applicant_uuid
        ");
        $stmt->bindParam(':applicant_uuid', $target_uuid);
        $stmt->execute();
        $bank_details = $stmt->fetch();

        // Fetch Attachments - THIS IS THE KEY FIX
        $stmt = $conn->prepare("
            SELECT file_name, file_path
            FROM `applicant_attachements`
            WHERE applicant_uuid = :applicant_uuid
        ");
        $stmt->bindParam(':applicant_uuid', $target_uuid);
        $stmt->execute();
        $attachments = $stmt->fetchAll();
    }

    include('layout/header.php');
?>

<div class="app-content-header">
    <!-- Section to list applicants -->
    <div class="card card-info card-outline mb-4">
        <div class="card-header">
            <h3 class="card-title">Applicant Details</h3>
        </div>
        <div class="card-body">
            <!-- Personal Details -->
            <h3>Personal Details</h3>
            <table class="table table-bordered">
                <tr>
                    <th>First Name</th>
                    <td><?= htmlspecialchars($personal_details['first_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Middle Name</th>
                    <td><?= htmlspecialchars($personal_details['middle_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?= htmlspecialchars($personal_details['last_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?= htmlspecialchars($personal_details['gender'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>DOB</th>
                    <td><?= htmlspecialchars($personal_details['dob'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>National ID</th>
                    <td><?= htmlspecialchars($personal_details['national_id'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td><?= htmlspecialchars($personal_details['mobile'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($personal_details['email'] ?? 'N/A') ?></td>
                </tr>
            </table>

            <!-- Guardian Details -->
            <h3>Guardian Details</h3>
            <?php if (!empty($guardian_details)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guardian_details as $guardian): ?>
                            <tr>
                                <td><?= htmlspecialchars($guardian['name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($guardian['mobile_number'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No guardian details found.</p>
            <?php endif; ?>

            <!-- Education Details -->
            <h3>Education Details</h3>
            <?php if (!empty($education_details)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Major</th>
                            <th>Specific Major</th>
                            <th>Institution</th>
                            <th>Completion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($education_details as $details): ?>
                            <tr>
                                <td><?= htmlspecialchars($details['program_general_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($details['major'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($details['specific_major_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($details['name_of_institution'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($details['completion_date'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No education details found.</p>
            <?php endif; ?>

            <!-- Preferred Districts -->
            <h3>Preferred Districts</h3>
            <?php if (!empty($preferred_districts)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>District</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preferred_districts as $district): ?>
                            <tr>
                                <td><?= htmlspecialchars($district['district_name'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No preferred districts found.</p>
            <?php endif; ?>

            <!-- Bank Details -->
            <h3>Bank Details</h3>
            <?php if ($bank_details): ?>
                <table class="table table-bordered">
                    <tr>
                        <th>Bank Name</th>
                        <td><?= htmlspecialchars($bank_details['bank_name'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Bank Branch</th>
                        <td><?= htmlspecialchars($bank_details['bank_branch'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Account Name</th>
                        <td><?= htmlspecialchars($bank_details['account_name'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Account Number</th>
                        <td><?= htmlspecialchars($bank_details['account_number'] ?? 'N/A') ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <p>No bank details found.</p>
            <?php endif; ?>

            <!-- Attachments -->
            <h3>Attachments</h3>
            <?php if (!empty($attachments)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attachments as $file): ?>
                            <tr>
                                <td><?= htmlspecialchars($file['file_name'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (!empty($file['file_path'])): ?>
                                        <a href="endpoint/<?= htmlspecialchars($file['file_path']) ?>" 
                                           download class="btn btn-sm btn-primary">
                                            Download
                                        </a>
                                        <a href="endpoint/<?= htmlspecialchars($file['file_path']) ?>" 
                                           target="_blank" class="btn btn-sm btn-info ml-2">
                                            View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No file path</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No attachments found for this applicant.</p>
            <?php endif; ?>

           

        </div>
    </div>
</div>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>