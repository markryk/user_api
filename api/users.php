<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    require "../vendor/autoload.php";
    include_once "../config/Database.php";
    include_once "auth.php"; //Função de autenticação nesse arquivo

    $user = authenticate(); //apenas requer login
    //$user = authenticate(true); //requer login + role admin

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, email, role, created_at FROM users");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
?>
