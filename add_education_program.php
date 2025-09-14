<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the `graduate` table
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
    }


    include('layout/header.php');
?>

    <div class="container">

        <div class="container">
            <h2>Add New Program General and Details</h2>
            <form action="./endpoint/education_program.php" method="POST" id="programForm">

                <!-- General Program Section -->
                <div class="mb-3">
                    <label for="program_general_name" class="form-label">Program General Name</label>
                    <input type="text" class="form-control" id="program_general_name" name="program_general_name" required>
                    <div class="invalid-feedback">Please enter a program general name.</div>
                </div>

                <div class="mb-3">
                    <label for="program_general_description" class="form-label">Program General Description</label>
                    <textarea class="form-control" id="program_general_description" name="program_general_description" required></textarea>
                    <div class="invalid-feedback">Please enter a description.</div>
                </div>

                <h3>Program Details</h3>

                <div id="programDetailsContainer">
                    <!-- This is where dynamic fields for program details will be added -->
                    <div class="program-detail mb-3">
                        <label for="program_detail_name[]" class="form-label">Detail Name</label>
                        <input type="text" class="form-control" name="program_detail_name[]" required>
                        <div class="invalid-feedback">Please enter a detail name.</div>

                        <label for="program_detail_description[]" class="form-label">Detail Description</label>
                        <textarea class="form-control" name="program_detail_description[]" required></textarea>
                        <div class="invalid-feedback">Please enter a description.</div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" id="addDetailButton">Add More Details</button>
                <button type="button" class="btn btn-danger" id="removeDetailButton" style="display:none;">Remove Last Detail</button>

                <br><br>

                <button type="submit" class="btn btn-primary" name="add_program">Submit Program</button>
            </form>
        </div>

    </div>

<?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailsContainer = document.getElementById('programDetailsContainer');
        const addDetailButton = document.getElementById('addDetailButton');
        const removeDetailButton = document.getElementById('removeDetailButton');

        addDetailButton.addEventListener('click', function() {
            const newDetail = document.createElement('div');
            newDetail.classList.add('program-detail', 'mb-3');

            newDetail.innerHTML = `
            <label for="program_detail_name[]" class="form-label">Detail Name</label>
            <input type="text" class="form-control" name="program_detail_name[]" required>
            <div class="invalid-feedback">Please enter a detail name.</div>

            <label for="program_detail_description[]" class="form-label">Detail Description</label>
            <textarea class="form-control" name="program_detail_description[]" required></textarea>
            <div class="invalid-feedback">Please enter a description.</div>
        `;

            detailsContainer.appendChild(newDetail);
            removeDetailButton.style.display = 'block'; // Show "Remove" button if it was hidden
        });

        removeDetailButton.addEventListener('click', function() {
            const detailCount = detailsContainer.getElementsByClassName('program-detail').length;
            if (detailCount > 1) {
                detailsContainer.removeChild(detailsContainer.lastChild);
            }
            if (detailCount === 2) {
                removeDetailButton.style.display = 'none'; // Hide "Remove" button if only one detail remains
            }
        });
    });
</script>