<?php
// ajax/get_programs.php
session_start();
include('../conn/conn.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_POST && isset($_POST['department_uuid'])) {
    $department_uuid = trim($_POST['department_uuid']);
    
    try {
        $stmt = $conn->prepare("
            SELECT DISTINCT 
                epd.uuid, 
                epd.name, 
                epd.description,
                epd.created_at,
                epd.updated_at,
                epg.name as general_name,
                COUNT(DISTINCT a.uuid) as applicant_count
            FROM education_programs_details epd
            INNER JOIN education_programs_generals epg ON epd.general_pg_uuid = epg.uuid
            INNER JOIN departments d ON epg.da_uuid = d.uuid
            LEFT JOIN education_details ed ON epd.uuid = ed.program_general
            LEFT JOIN applications a ON ed.applicant_uuid = a.applicant_uuid
            WHERE d.uuid = :dept_uuid AND d.status = 'active'
            GROUP BY epd.uuid, epd.name, epd.description, epd.created_at, epd.updated_at, epg.name
            ORDER BY epd.name
        ");
        
        $stmt->bindParam(':dept_uuid', $department_uuid);
        $stmt->execute();
        
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'programs' => $programs,
            'count' => count($programs)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>