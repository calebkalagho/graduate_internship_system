<?php
include('./conn/conn.php');


// Fetch vacancies from the database
$stmt = $conn->prepare("
    SELECT 
        v.uuid,
        v.vacancy_title,
        v.summary,
        v.department_uuid,
        v.opening_date,
        v.closing_date,
        d.name AS department_name
    FROM vacancies v
    JOIN departments d ON v.department_uuid = d.uuid
   
    ORDER BY v.created_at DESC
");
$stmt->execute();
$vacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);
include('layout/front/header.php');
?>

    <!-- Vacancy Listing Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 500px;">
                <h1 class="display-5">Current Vacancies</h1>
                <hr class="w-25 mx-auto text-primary" style="opacity: 1;">
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Vacancy Title</th>
                                <th>Department</th>
                                <th>Opening Date</th>
                                <th>Closing Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($vacancies as $vacancy): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vacancy['vacancy_title']); ?></td>
                                    <td><?php echo htmlspecialchars($vacancy['department_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($vacancy['opening_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($vacancy['closing_date'])); ?></td>
                                    <td>
                                       
                                        <button class="btn btn-primary" onclick="shareVacancy('<?= addslashes($vacancy['vacancy_title']) ?>', '<?= $vacancy['uuid'] ?>')">Share</button>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">Share Vacancy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="share-options"></div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>
    <!-- Vacancy Listing End -->


<?php
include('layout/front/footer.php');
?>