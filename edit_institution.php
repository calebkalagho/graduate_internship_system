<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if an institution UUID has been passed to edit
    if (isset($_GET['id'])) {
        $institution_uuid = $_GET['id'];

        // Fetch the institution details using the UUID
        $stmt = $conn->prepare("SELECT * FROM `institutions` WHERE `uuid` = :uuid");
        $stmt->bindParam(':uuid', $institution_uuid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $institution = $stmt->fetch();
            $institution_name = $institution['name'];
            $institution_description = $institution['description'];
            $post_address = $institution['post_address'];
            $physical_address = $institution['physical_address'];
            $contacts = $institution['contacts'];
            $email_address = $institution['email_address'];
            $district_id = $institution['district_id'];
        } else {
            echo "Institution not found.";
            exit();
        }
    } else {
        echo "No institution selected.";
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name FROM districts"); // Changed from 'district' to 'districts'
$stmt->execute();
$districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include('layout/header.php');
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Institution</h3>
                    </div>
                    <div class="card-body">
                        <!-- Institution Edit Form -->
                        <form action="./endpoint/institutions.php" method="POST">
                            <input type="hidden" name="uuid" value="<?= $institution_uuid ?>">
                            <div class="form-group mb-3">
                                <label for="name">Institution Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?= $institution_name ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" required><?= $institution_description ?></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="post_address">Post Address</label>
                                <input type="text" class="form-control" name="post_address" value="<?= $post_address ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="physical_address">Physical Address</label>
                                <input type="text" class="form-control" name="physical_address" value="<?= $physical_address ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contacts">Contacts</label>
                                <input type="text" class="form-control" name="contacts" value="<?= $contacts ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email_address">Email Address</label>
                                <input type="email" class="form-control" name="email_address" value="<?= $email_address ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="district_id">District</label>
                                <select class="form-control" name="district_id" required>
                                    <option value="">Select District</option>
                                    <?php
                                    // Fetch districts from the district table
                                    $stmt = $conn->prepare("SELECT id, name FROM districts"); // Changed from 'district' to 'districts'
                                    $stmt->execute();
                                    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($districts as $district) {
                                        $selected = ($district['id'] == $district_id) ? "selected" : "";
                                        echo "<option value='" . $district['id'] . "' $selected>" . htmlspecialchars($district['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Institution</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>
