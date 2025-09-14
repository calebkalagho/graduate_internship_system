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

        $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the row data

        // Access the name from the fetched row

        $user_name = $userdata['name'];
    } else {
        echo "No user found.";
    }



    // Fetch categories and brands for the select fields
    $districts = $conn->query("SELECT * FROM `districts`")->fetchAll(PDO::FETCH_ASSOC);
    $education_programs_generals = $conn->query("SELECT * FROM `education_programs_generals`")->fetchAll(PDO::FETCH_ASSOC);
    $education_programs_generals_majors = $conn->query("SELECT * FROM `education_programs_details`")->fetchAll(PDO::FETCH_ASSOC);


    // education_programs_details

    // Query to get the active cohort's UUID
    $stmt = $conn->prepare("SELECT uuid FROM cohort_programs WHERE status = 'active' LIMIT 1");
    $stmt->execute();

    // Check if a cohort program is found
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $active_cohort_uuid = $row['uuid'];
    } else {
        // Handle case when no active cohort is found
        throw new Exception('No active cohort program found');
    }

    // Check if the AJAX request has sent the general_pg_uuid
    if (isset($_POST['general_pg_uuid'])) {
        $general_pg_uuid = $_POST['general_pg_uuid'];

        // Fetch majors based on the program general UUID
        $sql = "SELECT uuid, name FROM education_programs_details WHERE general_pg_uuid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $general_pg_uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        // Generate the options for the Major dropdown
        if ($result->num_rows > 0) {
            echo '<option value="">Choose Major...</option>';
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['uuid'] . '">' . $row['name'] . '</option>';
            }
        } else {
            echo '<option value="">No majors found</option>';
        }
    }
    include('layout/headergraduate.php');

?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Graduate Internship Application Form</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Application Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Graduate Internship Program Application</div>
                </div>
                <form action="./endpoint/Applicants.php" method="post" enctype="multipart/form-data">

                    <div class="card-body">
                        <h5>A. Personal Details of Applicant</h5>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="title" class="form-label">Title</label>
                                <select class="form-select" id="title" name="title" required>
                                    <option value="">Choose...</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Mrs">Mrs</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($userdata['last_name']) ? $userdata['last_name'] : ''; ?>" required>
                                <input type="hidden" class="form-control" id="graduate_uuid" name="graduate_uuid" value="<?= isset($userdata['graduate_uuid']) ? $userdata['graduate_uuid'] : ''; ?>" required>
                                <input type="hidden" class="form-control" id="cohort_uuid" name="cohort_uuid" value="<?= $active_cohort_uuid ? $active_cohort_uuid : ''; ?>" required>

                            </div>
                            <div class="col-md-5">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($userdata['first_name']) ? $userdata['first_name'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" required>
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= isset($userdata['dob']) ? $userdata['dob'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="national_id" class="form-label">National Identity Number</label>
                                <input type="text" class="form-control" id="national_id" name="national_id" value="<?= isset($userdata['national_id']) ? $userdata['national_id'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="disability" class="form-label">Any Disability</label>
                                <select class="form-select" id="disability" name="disability" required>
                                    <option value="">Choose...</option>
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="disability_details" class="form-label">If Yes, Please Specify</label>
                                <input type="text" class="form-control" id="disability_details" name="disability_details">
                            </div>
                        </div>

                        <h5 class="mt-4">B. Communication</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required value="<?= isset($userdata['mobile']) ? $userdata['mobile'] : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= isset($userdata['email']) ? $userdata['email'] : ''; ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="postal_address" class="form-label">Postal Address</label>
                                <textarea class="form-control" id="postal_address" name="postal_address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="next_of_kin_name" class="form-label">Next of Kin: Name</label>
                                <input type="text" class="form-control" id="next_of_kin_name" name="next_of_kin_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="next_of_kin_mobile" class="form-label">Next of Kin: Mobile Number</label>
                                <input type="tel" class="form-control" id="next_of_kin_mobile" name="next_of_kin_mobile" required>
                            </div>
                        </div>

                        <h5 class="mt-4">C. Education Qualification</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="program_general" class="form-label">Program General</label>
                                <select class="form-select" id="program_general" name="program_general" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($education_programs_generals as $program): ?>
                                        <option value="<?= $program['uuid'] ?>"><?= $program['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="major" class="form-label">Major</label>
                                <select class="form-select" id="" name="major" required>
                                    <option value="">Choose ...</option>
                                    <?php foreach ($education_programs_generals_majors as $programm): ?>
                                        <option value="<?= $programm['uuid'] ?>"><?= $programm['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="specific_major" class="form-label">Specify</label>
                                <input type="text" class="form-control" id="specific_major" name="specific_major" required>
                            </div>
                            <div class="col-md-6">
                                <label for="institution" class="form-label">Name of Institution</label>
                                <input type="text" class="form-control" id="institution" name="institution" required>
                            </div>
                            <div class="col-md-6">
                                <label for="institution" class="form-label">Other</label>
                                <input type="text" class="form-control" id="other_general" name="other_general" required>
                            </div>
                            <div class="col-md-6">
                                <label for="completion_date" class="form-label">Completion Date</label>
                                <input type="date" class="form-control" id="completion_date" name="completion_date" required>
                            </div>
                        </div>



                        <h5 class="mt-4">D. Preferred District of Service</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="preferred_district" class="form-label">Preferred District</label>
                                <select class="form-select" id="preferred_district" name="preferred_district" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($districts as $district): ?>
                                        <option value="<?= $district['id'] ?>"><?= $district['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <h5 class="mt-4">E. Bank Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Name of the Bank</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_branch" class="form-label">Branch</label>
                                <input type="text" class="form-control" id="bank_branch" name="bank_branch" required>
                            </div>
                            <div class="col-md-6">
                                <label for="account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" required>
                            </div>
                        </div>

                        <h5 class="mt-4">F. Attachments</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="id_copy" class="form-label">Copy of National ID</label>
                                <input type="file" class="form-control" id="id_copy" name="id_copy" required>
                            </div>
                            <div class="col-md-6">
                                <label for="degree_copy" class="form-label">Copy of Degree Certificate</label>
                                <input type="file" class="form-control" id="degree_copy" name="degree_copy" required>
                            </div>
                        </div>

                        <h5 class="mt-4">G. Declaration</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                                    <label class="form-check-label" for="declaration">
                                        I declare that the information provided in this form is true and correct to the best of my knowledge. I am aware that the Ministry of Labour reserves the right to reject my application or terminate enrollment should the information given above be found to be incorrect. I am also aware that the Ministry reserves the right to place me where it deems to be necessary and subject to availability of space.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit" name="submit_application">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>