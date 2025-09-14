<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (isset($_POST['add_department'])) {
    // Get form inputs
    $name = $_POST['name'];
    $description = $_POST['desc'];
    $post_address = $_POST['post_address'];
    $physical_address = $_POST['physical_addres'];
    $email_address = $_POST['email_address'];
    $mda_type = $_POST['mda_type'];
    $ministry_uuid = $_POST['ministry'] ?? null;
    $institution_uuid = $_POST['institution'] ?? null;
    $district_id = $_POST['district_id'];

    // Generate UUID for the department
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

    // Generate UUID for the department
    $department_uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $status = 'active'; // Assuming status is 'active' by default

    try {
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `departments` (`uuid`, `name`, `description`, `post_address`, `physical_address`, `email_address`, `status`, `district_id`, `ministry_uuid`,`da_uuid`, `created_at`, `updated_at`)
                                VALUES (:uuid, :name, :description, :post_address, :physical_address, :email_address, :status, :district_id, :ministry_uuid,:da_uuid, :created_at, :updated_at)");

        // Bind parameters
        $stmt->bindParam(':uuid', $department_uuid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':post_address', $post_address);
        $stmt->bindParam(':physical_address', $physical_address);
        $stmt->bindParam(':email_address', $email_address);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':district_id', $district_id);
        $stmt->bindParam(':ministry_uuid', $ministry_uuid);
        $stmt->bindParam(':da_uuid', $institution_uuid);


        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':updated_at', $updated_at);

        // Execute the query
        if ($stmt->execute()) {
            echo "New department added successfully.";
            echo "
            <script>
                alert('Department submitted successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/department_list.php';
            </script>
            ";
        } else {
            echo "Error adding department.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
