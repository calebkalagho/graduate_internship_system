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
        $graduate_uuid = $row['graduate_uuid'];
    }

    // Initialize an empty array to hold notifications
    $notifications = [];

    if ($graduate_uuid) {
        // Fetch all notifications for the graduate
        $query = "SELECT * FROM notifications WHERE graduate_uuid = :graduate_uuid ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':graduate_uuid', $graduate_uuid);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    include('layout/header.php');
?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Notifications</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All notifications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notification) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($notification['title']) ?></td>
                                    <td><?= htmlspecialchars($notification['description']) ?></td>
                                    <td><?= $notification['status'] === 'new' ? '<span class="badge badge-warning">New</span>' : '<span class="badge badge-success">Read</span>' ?></td>
                                    <td><?= htmlspecialchars($notification['date']) ?></td>
                                    <td>
                                        <!-- Modified to trigger the modal -->
                                        <button class="btn btn-info view-notification-btn" data-toggle="modal" data-target="#notificationModal"
                                            data-id="<?= $notification['notification_id'] ?>"
                                            data-title="<?= htmlspecialchars($notification['title']) ?>"
                                            data-description="<?= htmlspecialchars($notification['description']) ?>"
                                            data-date="<?= htmlspecialchars($notification['date']) ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal structure -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 id="notificationTitle"></h5>
                    <p id="notificationDescription"></p>
                    <small id="notificationDate"></small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to populate modal with notification details
        document.querySelectorAll('.view-notification-btn').forEach(button => {
            button.addEventListener('click', function() {
                var notificationId = this.getAttribute('data-id');
                var title = this.getAttribute('data-title');
                var description = this.getAttribute('data-description');
                var date = this.getAttribute('data-date');

                document.getElementById('notificationTitle').innerText = title;
                document.getElementById('notificationDescription').innerText = description;
                document.getElementById('notificationDate').innerText = date;

                // Make an AJAX request to update the notification status to 'read'
                var xhr = new XMLHttpRequest();
                xhr.open('POST', './endpoint/Applicants.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('notification_id=' + notificationId);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Notification marked as read.');
                        // Optionally, you can update the status in the table without reloading
                    } else {
                        console.error('Error updating notification.');
                    }
                };
            });
        });
    </script>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>