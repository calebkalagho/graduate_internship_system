<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    include('layout/header.php');
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Institution</h3>
                    </div>
                    <div class="card-body">
                        <!-- Institution Add Form -->
                        <form action="./endpoint/institutions.php" method="POST">
                            <div class="form-group mb-3">
                                <label for="name">Institution Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="post_address">Post Address</label>
                                <input type="text" class="form-control" name="post_address" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="physical_address">Physical Address</label>
                                <input type="text" class="form-control" name="physical_address" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contacts">Contacts</label>
                                <input type="text" class="form-control" name="contacts" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email_address">Email Address</label>
                                <input type="email" class="form-control" name="email_address" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="district_id">District</label>
                                <select class="form-control" name="district_id" required>
                                    <option value="">Select District</option>
                                    <?php
                                    // Fetch districts from the district table
                                    $stmt = $conn->prepare("SELECT id, name FROM districts");
                                    $stmt->execute();
                                    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($districts as $district) {
                                        echo "<option value='" . $district['id'] . "'>" . $district['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Institution</button>
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
