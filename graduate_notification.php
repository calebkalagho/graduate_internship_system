<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's data from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
        $graduate_uuid = $row['graduate_uuid'];

        // Get notification statistics
        $stats_stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total_notifications,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as unread_notifications,
                SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_notifications,
                SUM(CASE WHEN date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as recent_notifications
            FROM notifications 
            WHERE graduate_uuid = :graduate_uuid
        ");
        $stats_stmt->execute([':graduate_uuid' => $graduate_uuid]);
        $notification_stats = $stats_stmt->fetch();

        // Get notifications by category/type if you have a type field, otherwise we'll categorize by title keywords
        $notifications_query = "
            SELECT *,
                CASE 
                    WHEN LOWER(title) LIKE '%application%' OR LOWER(title) LIKE '%apply%' THEN 'application'
                    WHEN LOWER(title) LIKE '%interview%' OR LOWER(title) LIKE '%meeting%' THEN 'interview'
                    WHEN LOWER(title) LIKE '%accept%' OR LOWER(title) LIKE '%approve%' THEN 'acceptance'
                    WHEN LOWER(title) LIKE '%reject%' OR LOWER(title) LIKE '%decline%' THEN 'rejection'
                    WHEN LOWER(title) LIKE '%deadline%' OR LOWER(title) LIKE '%due%' THEN 'deadline'
                    WHEN LOWER(title) LIKE '%performance%' OR LOWER(title) LIKE '%evaluation%' THEN 'performance'
                    WHEN LOWER(title) LIKE '%internship%' OR LOWER(title) LIKE '%placement%' THEN 'internship'
                    ELSE 'general'
                END as notification_category,
                DATEDIFF(NOW(), date) as days_ago
            FROM notifications 
            WHERE graduate_uuid = :graduate_uuid 
            ORDER BY date DESC
        ";
        $stmt = $conn->prepare($notifications_query);
        $stmt->execute([':graduate_uuid' => $graduate_uuid]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group notifications by category
        $categorized_notifications = [];
        foreach ($notifications as $notification) {
            $category = $notification['notification_category'];
            if (!isset($categorized_notifications[$category])) {
                $categorized_notifications[$category] = [];
            }
            $categorized_notifications[$category][] = $notification;
        }

        // Get priority notifications (unread and recent)
        $priority_notifications = array_filter($notifications, function($notif) {
            return $notif['status'] === 'new' && $notif['days_ago'] <= 3;
        });
    }

    include('layout/headergraduate.php');
?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">My Notifications</h3>
                        <p class="text-muted">Stay updated with your internship journey</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="dashboard_graduate.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                
                <!-- Notification Statistics -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3><?= $notification_stats['total_notifications'] ?></h3>
                                <p>Total Notifications</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-bell"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3><?= $notification_stats['unread_notifications'] ?></h3>
                                <p>Unread</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3><?= $notification_stats['read_notifications'] ?></h3>
                                <p>Read</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3><?= $notification_stats['recent_notifications'] ?></h3>
                                <p>This Week</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($priority_notifications)): ?>
                <!-- Priority Notifications -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="bi bi-exclamation-triangle text-warning"></i> 
                                    Priority Notifications
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (array_slice($priority_notifications, 0, 3) as $priority): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">
                                                        <i class="bi bi-bell-fill"></i>
                                                        <?= htmlspecialchars($priority['title']) ?>
                                                    </h6>
                                                    <p class="card-text"><?= htmlspecialchars(substr($priority['description'], 0, 80)) ?>...</p>
                                                    <small class="text-muted">
                                                        <?= $priority['days_ago'] == 0 ? 'Today' : $priority['days_ago'] . ' day(s) ago' ?>
                                                    </small>
                                                    <br>
                                                    <button class="btn btn-sm btn-warning mt-2 view-notification-btn"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#notificationModal"
                                                            data-id="<?= $priority['notification_id'] ?>"
                                                            data-title="<?= htmlspecialchars($priority['title']) ?>"
                                                            data-description="<?= htmlspecialchars($priority['description']) ?>"
                                                            data-date="<?= htmlspecialchars($priority['date']) ?>"
                                                            data-category="<?= $priority['notification_category'] ?>">
                                                        View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Filter and Search -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="filterNotifications('all')">All</button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterNotifications('new')">Unread</button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterNotifications('read')">Read</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="searchNotifications" class="form-control" placeholder="Search notifications...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categorized Notifications -->
                <div class="row">
                    <div class="col-12">
                        <?php if (!empty($notifications)): ?>
                            
                            <!-- Notification Categories Tabs -->
                            <div class="card">
                                <div class="card-header p-0">
                                    <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                                All <span class="badge bg-primary ms-1"><?= count($notifications) ?></span>
                                            </button>
                                        </li>
                                        <?php foreach ($categorized_notifications as $category => $category_notifications): ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="<?= $category ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $category ?>" type="button" role="tab">
                                                    <?= ucfirst($category) ?> <span class="badge bg-secondary ms-1"><?= count($category_notifications) ?></span>
                                                </button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="categoryTabContent">
                                        
                                        <!-- All Notifications Tab -->
                                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                                            <div class="timeline">
                                                <?php foreach ($notifications as $notification): ?>
                                                    <?php
                                                    $icon_class = '';
                                                    $card_class = '';
                                                    switch ($notification['notification_category']) {
                                                        case 'application': 
                                                            $icon_class = 'bi-file-earmark-text text-primary'; 
                                                            $card_class = 'border-primary';
                                                            break;
                                                        case 'interview': 
                                                            $icon_class = 'bi-person-video text-info'; 
                                                            $card_class = 'border-info';
                                                            break;
                                                        case 'acceptance': 
                                                            $icon_class = 'bi-check-circle text-success'; 
                                                            $card_class = 'border-success';
                                                            break;
                                                        case 'rejection': 
                                                            $icon_class = 'bi-x-circle text-danger'; 
                                                            $card_class = 'border-danger';
                                                            break;
                                                        case 'deadline': 
                                                            $icon_class = 'bi-clock text-warning'; 
                                                            $card_class = 'border-warning';
                                                            break;
                                                        case 'performance': 
                                                            $icon_class = 'bi-graph-up text-success'; 
                                                            $card_class = 'border-success';
                                                            break;
                                                        case 'internship': 
                                                            $icon_class = 'bi-building text-info'; 
                                                            $card_class = 'border-info';
                                                            break;
                                                        default: 
                                                            $icon_class = 'bi-bell text-secondary'; 
                                                            $card_class = 'border-secondary';
                                                    }
                                                    ?>
                                                    <div class="notification-item mb-3 <?= $notification['status'] ?>" data-status="<?= $notification['status'] ?>">
                                                        <div class="card <?= $card_class ?> <?= $notification['status'] === 'new' ? 'bg-light' : '' ?>">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-start">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <i class="<?= $icon_class ?>" style="font-size: 1.5rem;"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <div>
                                                                                <h6 class="mb-1">
                                                                                    <?= htmlspecialchars($notification['title']) ?>
                                                                                    <?php if ($notification['status'] === 'new'): ?>
                                                                                        <span class="badge bg-warning text-dark ms-2">New</span>
                                                                                    <?php endif; ?>
                                                                                </h6>
                                                                                <p class="mb-1 text-muted"><?= htmlspecialchars(substr($notification['description'], 0, 100)) ?>...</p>
                                                                                <small class="text-muted">
                                                                                    <i class="bi bi-clock"></i>
                                                                                    <?php
                                                                                    if ($notification['days_ago'] == 0) {
                                                                                        echo 'Today at ' . date('H:i', strtotime($notification['date']));
                                                                                    } elseif ($notification['days_ago'] == 1) {
                                                                                        echo 'Yesterday at ' . date('H:i', strtotime($notification['date']));
                                                                                    } else {
                                                                                        echo $notification['days_ago'] . ' days ago';
                                                                                    }
                                                                                    ?>
                                                                                </small>
                                                                                <span class="badge bg-secondary ms-2"><?= ucfirst($notification['notification_category']) ?></span>
                                                                            </div>
                                                                            <div>
                                                                                <button class="btn btn-sm btn-outline-primary view-notification-btn"
                                                                                        data-bs-toggle="modal" 
                                                                                        data-bs-target="#notificationModal"
                                                                                        data-id="<?= $notification['notification_id'] ?>"
                                                                                        data-title="<?= htmlspecialchars($notification['title']) ?>"
                                                                                        data-description="<?= htmlspecialchars($notification['description']) ?>"
                                                                                        data-date="<?= htmlspecialchars($notification['date']) ?>"
                                                                                        data-category="<?= $notification['notification_category'] ?>">
                                                                                    <i class="bi bi-eye"></i> View
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <!-- Category-specific tabs -->
                                        <?php foreach ($categorized_notifications as $category => $category_notifications): ?>
                                            <div class="tab-pane fade" id="<?= $category ?>" role="tabpanel">
                                                <div class="row">
                                                    <?php foreach ($category_notifications as $notification): ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card <?= $notification['status'] === 'new' ? 'bg-light border-primary' : '' ?>">
                                                                <div class="card-body">
                                                                    <h6 class="card-title">
                                                                        <?= htmlspecialchars($notification['title']) ?>
                                                                        <?php if ($notification['status'] === 'new'): ?>
                                                                            <span class="badge bg-warning text-dark">New</span>
                                                                        <?php endif; ?>
                                                                    </h6>
                                                                    <p class="card-text"><?= htmlspecialchars(substr($notification['description'], 0, 100)) ?>...</p>
                                                                    <small class="text-muted">
                                                                        <?= $notification['days_ago'] == 0 ? 'Today' : $notification['days_ago'] . ' day(s) ago' ?>
                                                                    </small>
                                                                    <br>
                                                                    <button class="btn btn-sm btn-primary mt-2 view-notification-btn"
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#notificationModal"
                                                                            data-id="<?= $notification['notification_id'] ?>"
                                                                            data-title="<?= htmlspecialchars($notification['title']) ?>"
                                                                            data-description="<?= htmlspecialchars($notification['description']) ?>"
                                                                            data-date="<?= htmlspecialchars($notification['date']) ?>"
                                                                            data-category="<?= $notification['notification_category'] ?>">
                                                                        View Details
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- No Notifications State -->
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-bell" style="font-size: 4rem; color: #ccc;"></i>
                                    <h4 class="mt-3 text-muted">No Notifications Yet</h4>
                                    <p class="text-muted">You'll receive notifications about your applications, interviews, and internship updates here.</p>
                                    <a href="dashboard_graduate.php" class="btn btn-primary">
                                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="bi bi-bell-fill text-primary"></i> Notification Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <h5 id="notificationTitle" class="mb-0"></h5>
                            <span id="categoryBadge" class="badge bg-secondary ms-2"></span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <span id="notificationDate"></span>
                        </small>
                    </div>
                    <div class="alert alert-light">
                        <p id="notificationDescription" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary" id="markAsReadBtn">
                        <i class="bi bi-check"></i> Mark as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter notifications by status
        function filterNotifications(status) {
            const items = document.querySelectorAll('.notification-item');
            const buttons = document.querySelectorAll('.btn-group .btn');
            
            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            items.forEach(item => {
                if (status === 'all' || item.dataset.status === status) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Search functionality
        document.getElementById('searchNotifications').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.notification-item');
            
            items.forEach(item => {
                const title = item.querySelector('h6').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Handle notification modal
        document.querySelectorAll('.view-notification-btn').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');
                const date = this.getAttribute('data-date');
                const category = this.getAttribute('data-category');

                document.getElementById('notificationTitle').textContent = title;
                document.getElementById('notificationDescription').textContent = description;
                document.getElementById('notificationDate').textContent = new Date(date).toLocaleString();
                document.getElementById('categoryBadge').textContent = category.charAt(0).toUpperCase() + category.slice(1);

                // Store notification ID for mark as read functionality
                document.getElementById('markAsReadBtn').dataset.notificationId = notificationId;
                
                // Auto-mark as read when viewed
                markNotificationAsRead(notificationId);
            });
        });

        // Mark notification as read
        function markNotificationAsRead(notificationId) {
            fetch('./endpoint/Applicants.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'notification_id=' + notificationId
            })
            .then(response => response.text())
            .then(data => {
                console.log('Notification marked as read');
                // Update UI to reflect read status
                const notificationCard = document.querySelector(`[data-id="${notificationId}"]`).closest('.notification-item');
                if (notificationCard) {
                    notificationCard.classList.remove('new');
                    notificationCard.classList.add('read');
                    const badge = notificationCard.querySelector('.badge.bg-warning');
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Mark as read button handler
        document.getElementById('markAsReadBtn').addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            markNotificationAsRead(notificationId);
            
            // Hide mark as read button
            this.style.display = 'none';
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-light)');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    alert.style.display = 'none';
                }
            });
        }, 5000);
    </script>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
    exit();
}
?>