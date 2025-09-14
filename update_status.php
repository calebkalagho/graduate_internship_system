<?php
session_start();
include('./conn/conn.php');

// --- PART 1: HANDLE THE FORM SUBMISSION (POST REQUEST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateApplicantStatus'])) {
    try {
        $uuid = $_POST['uuid'];
        $status = $_POST['status'];

        if (empty($uuid) || empty($status)) {
            throw new Exception('UUID and status are required.');
        }

        // The update and notification logic is the same as before
        $stmt = $conn->prepare("UPDATE `applications` SET `status` = :status WHERE `uuid` = :uuid");
        $stmt->execute([':status' => $status, ':uuid' => $uuid]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("SELECT `applicant_uuid` FROM `applications` WHERE `uuid` = :uuid");
            $stmt->execute([':uuid' => $uuid]);
            $app_row = $stmt->fetch();

            if ($app_row) {
                // ... (The entire notification insertion logic is the same)
                $graduate_uuid = $app_row['applicant_uuid'];
                $notification_title = "Application Status Updated";
                if ($status === 'reviewed') {
                    $notification_desc = "Your application has been reviewed and is under consideration.";
                } elseif ($status === 'rejected') {
                    $notification_desc = "We regret to inform you that your application has been unsuccessful at this time.";
                }
                // ... etc. ...
                $stmt = $conn->prepare("INSERT INTO `notifications` (`graduate_uuid`, `title`, `description`, `status`, `date`) VALUES (?, ?, ?, 'new', NOW())");
                $stmt->execute([$graduate_uuid, $notification_title, $notification_desc]);

                echo "<script>
                        alert('Application status updated successfully!');
                        window.location.href = 'application_list.php';
                      </script>";
                exit();
            }
        } else {
             throw new Exception('No application found or status was unchanged.');
        }

    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.location.href = 'application_list.php';
              </script>";
        exit();
    }
}


// --- PART 2: DISPLAY THE UPDATE FORM (GET REQUEST) ---

// Check if UUID is provided in the URL
if (!isset($_GET['uuid']) || empty($_GET['uuid'])) {
    header('Location: application_list.php');
    exit();
}

$application_uuid = $_GET['uuid'];

// Fetch applicant details to display on the page
$stmt = $conn->prepare("
    SELECT a.uuid, a.status, app.first_name, app.last_name
    FROM `applications` a
    JOIN `graduate` app ON a.applicant_uuid = app.graduate_uuid
    WHERE a.uuid = :uuid
");
$stmt->bindParam(':uuid', $application_uuid);
$stmt->execute();
$applicant = $stmt->fetch();

// If no applicant found, redirect back
if (!$applicant) {
    header('Location: application_list.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/graduate_internship_system");
    exit();
}

include('layout/header.php');
?>

<div class="app-content-header">
    <div class="card card-info card-outline mx-auto" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Update Applicant Status</h3>
        </div>
        <div class="card-body">
            <h5 class="mb-3">
                Applicant: <strong><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></strong>
            </h5>
            <p>
                Current Status: <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($applicant['status'])) ?></span>
            </p>

            <form action="update_status.php" method="POST">
                <input type="hidden" name="uuid" value="<?= htmlspecialchars($applicant['uuid']) ?>">

                <div class="form-group">
                    <label for="status">Select New Status:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="reviewed">Reviewed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="mt-4">
                    <a href="application_list.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="updateApplicantStatus" class="btn btn-success">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>