<?php
    include_once "../config/Database.php";
    include_once "auth.php";

    header("Content-Type: application/json; charset=UTF-8");

    $admin = authenticate(true); // só admin pode ver logs

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("
        SELECT 
            l.id, 
            u.name AS admin_name, 
            l.action, 
            t.name AS target_name, 
            l.created_at
        FROM activity_logs l
        JOIN users u ON u.id = l.admin_id
        LEFT JOIN users t ON t.id = l.target_user_id
        ORDER BY l.created_at DESC
    ");
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($logs);
?>
