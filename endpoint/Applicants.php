<?php
session_start();
include('../conn/conn.php');


// Check if the form has been submitted
// Check if the form has been submitted
if (isset($_POST['add_applicant'])) {
    // Get form inputs
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $national_id = $_POST['national_id'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $full_name = $_POST['first_name'] . '' . $_POST['last_name'];
    // Hash the password before storing it
    $hashed_password = sha1($password);

    // Generate UUID for the graduate
    function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // Generate UUID for this graduate
    $uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `graduate` (`graduate_uuid`,`name`, `first_name`, `middle_name`, `last_name`, `gender`, `dob`, `national_id`, `mobile`, `email`, `password`, `created_at`, `updated_at`)
                                VALUES (:graduate_uuid, :name,:first_name, :middle_name, :last_name, :gender, :dob, :national_id, :mobile, :email, :password, :created_at, :updated_at)");

        // Bind parameters
        $stmt->bindParam(':graduate_uuid', $uuid);
        $stmt->bindParam(':name', $full_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':national_id', $national_id);
        $stmt->bindParam(':mobile', $mobile);   
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);  // Store the hashed password
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':updated_at', $updated_at);

        // Execute the statement
        $stmt->execute();

        // Redirect or show success message
        echo "
            <script>
                alert('Applicant added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/login.php';
            </script>
            ";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Check if form is submitted
if (isset($_POST['submit_application'])) {
    try {


        function generateUUID()
        {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );
        }
        $uuid = generateUUID();
        $guaduuid = generateUUID();
        $edudetailsuuid = generateUUID();
        $districtuuid = generateUUID();
        $bankuuid = generateUUID();
        $attachmentuuid = generateUUID();
        $applicationsuuid = generateUUID();
        $applieddate = date('Y-m-d');
        // Insert Communication Details
        $stmt = $conn->prepare("INSERT INTO guardians (uuid, name, mobile_number, applicant_uuid)
        VALUES (:uuid, :name, :mobile_number, :applicant_uuid)");

        $stmt->execute([
            ':uuid' => $guaduuid, // Make sure $guaduuid is correctly defined
            ':name' => $_POST['next_of_kin_name'],
            ':mobile_number' => $_POST['next_of_kin_mobile'],
            ':applicant_uuid' => $_POST['graduate_uuid'] // Ensure 'graduate_uuid' is present in the form
        ]);

        // Insert Education Details
        $stmt = $conn->prepare("INSERT INTO education_details (applicant_uuid, program_general, major, specific_major, name_of_institution, completion_date, other_general)
          VALUES (:applicant_uuid, :program_general, :major, :specific_major, :name_of_institution, :completion_date, :other_general)");
        $stmt->execute([
            ':applicant_uuid' => $_POST['graduate_uuid'],
            ':program_general' => $_POST['program_general'],
            ':major' => $_POST['major'],
            ':specific_major' => $_POST['specific_major'],
            ':name_of_institution' => $_POST['institution'],  // No space before the placeholder
            ':completion_date' => $_POST['completion_date'],
            ':other_general' => $_POST['other_general']
        ]);

        // Insert Preferred District of Service
        $stmt = $conn->prepare("INSERT INTO service_district (uuid, applicant_uuid, district_id)
                        VALUES (:uuid, :applicant_uuid, :district_id)");
        $stmt->execute([
            ':uuid' => $districtuuid,  // Placeholder name matches the query
            ':applicant_uuid' => $_POST['graduate_uuid'],  // Correct placeholder
            ':district_id' => $_POST['preferred_district']
        ]);



        $stmt = $conn->prepare("INSERT INTO bank_details (uuid,bank_name, bank_branch, account_name, account_number,applicant_uuid)
          VALUES (:uuid, :bank_name, :bank_branch, :account_name, :account_number,:applicant_uuid)");
        $stmt->execute([
            ':uuid' => $bankuuid,
            ':bank_name' => $_POST['bank_name'],
            ':bank_branch' => $_POST['bank_branch'],
            ':account_name' => $_POST['account_name'],
            ':account_number' => $_POST['account_number'],
            ':applicant_uuid' => $_POST['graduate_uuid'],  // Use the correct placeholder
        ]);

        // Handle file uploads (e.g., National ID and Degree Certificate)
        $target_dir = "uploads/";
        $id_copy = $target_dir . basename($_FILES["id_copy"]["name"]);
        $degree_copy = $target_dir . basename($_FILES["degree_copy"]["name"]);

        // Ensure the target directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory with proper permissions
        }

        // Attempt to move the uploaded files
        if (
            move_uploaded_file($_FILES["id_copy"]["tmp_name"], $id_copy) &&
            move_uploaded_file($_FILES["degree_copy"]["tmp_name"], $degree_copy)
        ) {
            // Insert ID copy details
            $stmt = $conn->prepare("INSERT INTO applicant_attachements (uuid, applicant_uuid, declaration, file_name, file_path)
                                    VALUES (:uuid, :applicant_uuid, :declaration, :file_name, :file_path)");
            $stmt->execute([
                ':uuid' => $attachmentuuid, // Generate this UUID as needed
                ':applicant_uuid' => $_POST['graduate_uuid'],
                ':declaration' => 'National ID', // Assuming the declaration field describes the file type
                ':file_name' => basename($_FILES["id_copy"]["name"]),
                ':file_path' => $id_copy
            ]);

            // Insert Degree copy details
            $stmt = $conn->prepare("INSERT INTO applicant_attachements (uuid, applicant_uuid, declaration, file_name, file_path)
                                    VALUES (:uuid, :applicant_uuid, :declaration, :file_name, :file_path)");
            $stmt->execute([
                ':uuid' => $attachmentuuid, // Generate another UUID for this if necessary
                ':applicant_uuid' => $_POST['graduate_uuid'],
                ':declaration' => 'Degree Certificate', // Assuming the declaration field describes the file type
                ':file_name' => basename($_FILES["degree_copy"]["name"]),
                ':file_path' => $degree_copy
            ]);
        } else {
            // Check for specific error on file upload failure
            if ($_FILES["id_copy"]["error"] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload failed: ' . $_FILES["id_copy"]["error"]);
            }
            if ($_FILES["degree_copy"]["error"] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload failed: ' . $_FILES["degree_copy"]["error"]);
            }
            throw new Exception('File upload failed');
        }



        $stmt = $conn->prepare("INSERT INTO applications (uuid, cohort_uuid, applicant_uuid, signature, status, applied_date) 
                                VALUES (:uuid, :cohort_uuid, :applicant_uuid, :signature, :status, :applied_date)");
        $stmt->execute([
            ':uuid' => $applicationsuuid,
            ':cohort_uuid' => $_POST['cohort_uuid'],
            ':applicant_uuid' => $_POST['graduate_uuid'],
            ':signature' => $_POST['graduate_uuid'],
            ':status' => 'submitted',  // You can adjust this status based on your logic
            ':applied_date' =>  $applieddate, // Insert the current date and time
        ]);





        // Redirect or show success message
        echo "
            <script>
                alert('Application submitted successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/application_graduate_list.php';
            </script>
            ";
    }
    // Commit the transaction
    catch (Exception $e) {
        // Rollback transaction if something went wrong

         echo "
            <script>
                alert('Application submitted successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/application_graduate_list.php';
            </script>
            ";

        echo "Failed to submit application: " . $e->getMessage();
    }
}


// Check if the form has been submitted
// Check if the form has been submitted
if (isset($_POST['add_employee'])) {
    // Get form inputs
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $full_name = $_POST['first_name'] . '' . $_POST['last_name'];



    $department_uuid = $_POST['department'];
    $role_uuid = $_POST['role'];

    // Hash the password before storing it
    $hashed_password = sha1($password);

    // Generate UUID for the employee
    function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // Generate UUID for this graduate
    $graduate_uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $type = 'employee';
    $role = 'hr';

    try {
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `graduate` (`graduate_uuid`, `name`,`first_name`, `middle_name`, `last_name`, `gender`, `email`, `mobile`, `password`, `role`, `role_type`, `role_uuid`, `department_uuid`,  `created_at`, `updated_at`)
                                VALUES (:graduate_uuid,:name, :first_name, :middle_name, :last_name, :gender, :email, :mobile, :password,:role, :role_type, :role_uuid, :department_uuid,  :created_at, :updated_at)");

        // Bind parameters
        $stmt->bindParam(':graduate_uuid', $graduate_uuid);
        $stmt->bindParam(':name', $full_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':role_type', $type);
        $stmt->bindParam(':role_uuid', $role_uuid);
        $stmt->bindParam(':department_uuid', $department_uuid);

        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':updated_at', $updated_at);

        // Execute the query
        if ($stmt->execute()) {
            echo "New employee added successfully.";
            echo "
            <script>
                alert('Employee submitted successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/employees_list.php';
            </script>
            ";
        } else {
            echo "Error adding employee.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}



// Check if the form has been submitted
if (isset($_POST['update_employee'])) {
    // Get form inputs
    $employee_id = $_POST['employee_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $full_name = $first_name . ' ' . $last_name;

    $department_uuid = $_POST['department'];
 

   
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Prepare the SQL update statement
        $stmt = $conn->prepare("UPDATE `graduate` SET 
            `name` = :name,
            `first_name` = :first_name, 
            `middle_name` = :middle_name, 
            `last_name` = :last_name, 
            `gender` = :gender, 
            `email` = :email, 
            `mobile` = :mobile,
          
            `updated_at` = :updated_at
            WHERE `id` = :employee_id");

        // Bind parameters
        $stmt->bindParam(':name', $full_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile', $mobile);
    
        $stmt->bindParam(':updated_at', $updated_at);
        $stmt->bindParam(':employee_id', $employee_id);

        // Execute the query
        if ($stmt->execute()) {
            echo "
            <script>
                alert('Employee updated successfully!');
                window.location.href = '../employees_list.php';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Error updating employee.');
                window.location.href = '../employees_list.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        echo "
        <script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = '../employees_list.php';
        </script>
        ";
    }
} else {
    // If the form wasn't submitted, redirect to the employee list
    header("Location: ../employees_list.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uuid']) && isset($_POST['status'])) {
    $uuid = $_POST['uuid'];
    $status = $_POST['status'];

    // Update the application status
    $stmt = $conn->prepare("UPDATE `applications` SET `status` = :status WHERE `uuid` = :uuid");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':uuid', $uuid);

    if ($stmt->execute()) {
        // Get the graduate_uuid of the applicant
        $stmt = $conn->prepare("SELECT `applicant_uuid` FROM `applications` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        $row = $stmt->fetch();
        $graduate_uuid = $row['applicant_uuid'];

        // Insert into the notifications table
        $notification_title = "Application Status Updated";
        $notification_desc = "The application status has been updated to: " . $status;
        $notification_status = "new";
        $notification_date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("
            INSERT INTO `notifications` 
            (`graduate_uuid`, `title`, `description`, `status`, `date`) 
            VALUES (:graduate_uuid, :title, :description, :status, :date)
        ");
        $stmt->bindParam(':graduate_uuid', $graduate_uuid);
        $stmt->bindParam(':title', $notification_title);
        $stmt->bindParam(':description', $notification_desc);
        $stmt->bindParam(':status', $notification_status);
        $stmt->bindParam(':date', $notification_date);

        if ($stmt->execute()) {
            // Success message
            echo "Status updated and notification sent!";

            echo "
            <script>
                alert('Employee submitted successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/application_list.php';
            </script>
            ";
        } else {
            echo "Error sending notification.";
        }
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid request.";
}



if (isset($_POST['allocate_applicant'])) {
    try {
        // FIX: Trim whitespace from the incoming UUID
        $applications_uuid = trim($_POST['applications_uuid']);
        $department_uuid = isset($_POST['department_uuid']) && !empty($_POST['department_uuid']) ? $_POST['department_uuid'] : null;
        $reporting_date = $_POST['reporting_date'];

        // Get the graduate_uuid of the applicant using the correct column 'uuid'
        // FIX: Changed WHERE clause from 'application_id' to 'uuid'
        $stmt = $conn->prepare("SELECT `applicant_uuid` FROM `applications` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $applications_uuid);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Application not found.");
        }
        $graduate_uuid = $row['applicant_uuid'];

        // Update the application status to 'allocated'
        // FIX: Changed WHERE clause from 'application_id' to 'uuid'
        $status = 'allocated';
        $stmt = $conn->prepare("UPDATE `applications` SET `status` = :status WHERE `uuid` = :uuid");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':uuid', 'applications_uuid');

        // FIX: Execute the statement only once and check if it was successful
        if ($stmt->execute()) {
            
            // Get the department name for the notification message
            $department_name = "your assigned institution"; // Default message
            if ($department_uuid) {
                 $stmt = $conn->prepare("SELECT `name` FROM `departments` WHERE `uuid` = :uuid");
                 $stmt->bindParam(':uuid', $department_uuid);
                 $stmt->execute();
                 $dept_row = $stmt->fetch();
                 if ($dept_row) {
                    $department_name = $dept_row['name'];
                 }
            }

            // Insert the allocation into the database
            $stmt = $conn->prepare("
              INSERT INTO allocate_applicants (applications_uuid, department_uuid, reporting_date, applicant_uuid)
              VALUES (:applications_uuid, :department_uuid, :reporting_date, :applicant_uuid)
            ");
            $stmt->execute([
                ':applications_uuid' => $applications_uuid,
                ':department_uuid' => $department_uuid,
                ':reporting_date' => $reporting_date,
                ':applicant_uuid' => $graduate_uuid
            ]);

            // Insert the notification for the applicant
            $notification_title = "Internship Allocation Successful";
            $notification_desc = "Congratulations! You have been allocated to the $department_name. You are expected to report on $reporting_date. Failure to report by this date will be considered a withdrawal of interest.";
            
            $stmt = $conn->prepare("
                INSERT INTO `notifications` (`graduate_uuid`, `title`, `description`, `status`, `date`) 
                VALUES (:graduate_uuid, :title, :description, 'new', NOW())
            ");
            $stmt->execute([
                ':graduate_uuid' => $graduate_uuid,
                ':title' => $notification_title,
                ':description' => $notification_desc
            ]);
            
            // Success message and redirect
            echo "<script>
                    alert('Applicant allocated successfully!');
                    window.location.href = 'application_list_reviewed.php';
                  </script>";
            exit(); // Stop script execution after redirect

        } else {
            throw new Exception("Error updating application status.");
        }
    } catch (Exception $e) {
        echo "<script>
                alert('An error occurred: " . addslashes($e->getMessage()) . "');
                window.location.href = 'application_list_reviewed.php';
              </script>";
        exit();
    }
}


if (isset($_POST['updateApplicantStatus'])) {
    try {
        $uuid = $_POST['uuid'];
        $status = $_POST['status'];

        // Validate required fields
        if (empty($uuid) || empty($status)) {
            throw new Exception('UUID and status are required fields.');
        }

        // Update the application status
        $stmt = $conn->prepare("UPDATE `applications` SET `status` = :status WHERE `uuid` = :uuid");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':uuid', $uuid);

        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->rowCount() > 0) {
                // Get the applicant_uuid for notification
                $stmt = $conn->prepare("SELECT `applicant_uuid` FROM `applications` WHERE `uuid` = :uuid");
                $stmt->bindParam(':uuid', $uuid);
                $stmt->execute();
                $row = $stmt->fetch();

                if ($row) {
                    $graduate_uuid = $row['applicant_uuid'];

                    // Create notification message based on status
                    $notification_title = "Application Status Updated";
                    if ($status === 'reviewed') {
                        $notification_desc = "Your application has been reviewed and is under consideration.";
                    } elseif ($status === 'rejected') {
                        $notification_desc = "We regret to inform you that your application has been unsuccessful at this time.";
                    } else {
                        $notification_desc = "Your application status has been updated to: " . ucfirst($status);
                    }

                    $notification_status = "new";
                    $notification_date = date('Y-m-d H:i:s');

                    // Insert notification
                    $stmt = $conn->prepare("
                        INSERT INTO `notifications` 
                        (`graduate_uuid`, `title`, `description`, `status`, `date`) 
                        VALUES (:graduate_uuid, :title, :description, :status, :date)
                    ");
                    $stmt->bindParam(':graduate_uuid', $graduate_uuid);
                    $stmt->bindParam(':title', $notification_title);
                    $stmt->bindParam(':description', $notification_desc);
                    $stmt->bindParam(':status', $notification_status);
                    $stmt->bindParam(':date', $notification_date);

                    if ($stmt->execute()) {
                        // Success - redirect back to the application list
                        echo "
                        <script>
                            alert('Application status updated successfully and notification sent!');
                            window.location.href = '../application_list.php';
                        </script>
                        ";
                    } else {
                        throw new Exception('Status updated but failed to send notification.');
                    }
                } else {
                    throw new Exception('Application not found after update.');
                }
            } else {
                throw new Exception('No application found with the given UUID or status was unchanged.');
            }
        } else {
            throw new Exception('Failed to update application status.');
        }
    } catch (Exception $e) {
        // Error handling
        echo "
        <script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../application_list.php';
        </script>
        ";
    }
} else {
    // Redirect if accessed directly without POST data
    header('Location: ../application_list.php');
    exit();
}

if (isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];

    // Update the notification status to 'read'
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = :notification_id");
    $stmt->bindParam(':notification_id', $notification_id);
    $stmt->execute();
}


if (isset($_POST['update_reporting_applicant'])) {
    // Get form data
    $applicant_id = $_POST['applicant_id'];
    $allocation_status = $_POST['allocation_status'];
    $reported_date = isset($_POST['reported_date']) ? $_POST['reported_date'] : null;

    // Prepare the SQL query to update the allocate_applicants table
    $stmt = $conn->prepare("
        UPDATE allocate_applicants
        SET 
            allocation_status = :allocation_status,
            reported_date = :reported_date
        WHERE 
            allocate_applicant_id = :allocate_applicant_id
    ");

    // Bind parameters to the query
    $stmt->bindParam(':allocation_status', $allocation_status);
    $stmt->bindParam(':reported_date', $reported_date);
    $stmt->bindParam(':allocate_applicant_id', $applicant_id);

    // Execute the query
    if ($stmt->execute()) {

        echo "
        <script>
            alert('Updateed allocated Applicant successfully!');
            window.location.href = 'http://localhost/graduate_internship_system/application_list_reviewed_hr.php';
        </script>
        ";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update applicant status']);
        echo "
        <script>
            alert('Failed to update Applicant!');
            window.location.href = 'http://localhost/graduate_internship_system/application_list_reviewed_hr.php';
        </script>
        ";
    }
}
