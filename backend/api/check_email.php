<?php
    $db = (new Database())->getConnection();
    
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    echo json_encode(["exists" => $stmt->fetch() ? true : false]);
?>