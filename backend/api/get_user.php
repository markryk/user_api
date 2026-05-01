<?php
    include_once "cors.php";
    include_once "../config/Database.php";
    
    $db = (new Database())->getConnection();

    $id = $_GET['id'];

    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode($stmt->fetch());
?>