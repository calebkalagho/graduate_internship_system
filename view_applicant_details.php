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
        $applicant_uuid = $_GET['applicant_uuid'];

        // Query to fetch detailed information of the applicant
        $stmt = $conn->prepare("
        SELECT first_name, middle_name, last_name, gender, dob, national_id, mobile, email
        FROM `graduate`
        WHERE id = :id
            ");
        $stmt->execute([':id' => $applicant_uuid]);
        $personal_details = $stmt->fetch();



        $stmt = $conn->prepare("
    SELECT name, mobile_number
    FROM `guardians`
    WHERE applicant_uuid = :applicant_uuid
        ");
        $stmt->execute([':applicant_uuid' =>  $guuid]);
        $guardian_details = $stmt->fetchAll();




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
        $stmt->execute([':applicant_uuid' => $guuid]);
        $education_details = $stmt->fetchAll();



        $stmt = $conn->prepare("
    SELECT district_id
    FROM `service_district`
    WHERE applicant_uuid = :applicant_uuid
                    ");
        $stmt->execute([':applicant_uuid' =>  $guuid]);
        $service_district = $stmt->fetch();



        $stmt = $conn->prepare("
    SELECT bank_name, bank_branch, account_name, account_number
    FROM `bank_details`
    WHERE applicant_uuid = :applicant_uuid
        ");
        $stmt->execute([':applicant_uuid' =>  $guuid]);
        $bank_details = $stmt->fetch();


        $stmt = $conn->prepare("
        SELECT file_name, file_path
        FROM `applicant_attachements`
        WHERE applicant_uuid = :applicant_uuid
    ");
        $stmt->execute([':applicant_uuid' =>  $guuid]);
        $attachments = $stmt->fetchAll();
    }


    // Fetch preferred district of the applicant
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

    // Bind the applicant_uuid parameter
    $stmt->bindParam(':applicant_uuid', $guuid);

    // Execute the query
    $stmt->execute();

    // Fetch the preferred districts
    $preffered = $stmt->fetchAll(PDO::FETCH_ASSOC);


    include('layout/headergraduate.php');
?>

    <div class="app-content-header">


        <!-- Section to list applicants -->
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Applicant List</h3>
            </div>
            <div class="card-body">
                <h3>Personal Details</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>First Name</th>
                        <td><?= $personal_details['first_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><?= $personal_details['last_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?= $personal_details['gender'] ?></td>
                    </tr>
                    <tr>
                        <th>DOB</th>
                        <td><?= $personal_details['dob'] ?></td>
                    </tr>
                    <tr>
                        <th>National ID</th>
                        <td><?= $personal_details['national_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Mobile</th>
                        <td><?= $personal_details['mobile'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $personal_details['email'] ?></td>
                    </tr>
                </table>



                <h3>Guardian Details</h3>
                <table class="table table-bordered">
                    <?php foreach ($guardian_details as $guardian): ?>
                        <tr>
                            <th>Name</th>
                            <td><?= $guardian['name'] ?></td>
                        </tr>
                        <tr>
                            <th>Mobile Number</th>
                            <td><?= $guardian['mobile_number'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>


                <h3>Education Details</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>Program</th>


                        <th>Major</th>


                        <th>Specific Major</th>

                        <th>Institution</th>

                        <th>Completion Date</th>

                    </tr>

                    <?php foreach ($education_details as $details): ?>


                        <tr>

                            <td><?= $details['program_general_name'] ?></td>
                            <td><?= $details['specific_major_name'] ?></td>
                            <td><?= $details['specific_major_name'] ?></td>
                            <td><?= $details['name_of_institution'] ?></td>
                            <td><?= $details['completion_date'] ?></td>

                        </tr>

                    <?php endforeach; ?>
                </table>


                <h3>Preferred District</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>District</th>
                    </tr>

                    <?php foreach ($preffered as $ddetails): ?>
                        <tr>
                            <td><?= $ddetails['district_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Display Bank Details -->
                <h3>Attachments</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>File Name</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($attachments as $file): ?>
                        <tr>
                            <td><?= $file['file_name'] ?></td>
                            <td><a href="endpoint/<?= $file['file_path'] ?>" download>Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            </div>
        </div>
    </div>
    </div>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>