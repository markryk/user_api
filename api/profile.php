<?php
    include_once "../config/Database.php";
    include_once "auth.php";

    header("Content-Type: application/json; charset=UTF-8");

    $user = authenticate(); // apenas precisa estar logado

    $db = (new Database())->getConnection();

    // Se for admin e quiser ver outro perfil, pode mandar ?id= no GET
    $id = $_GET['id'] ?? $user->id;

    // Se não for admin, só pode ver o próprio
    if (($user->role ?? 'user') !== 'admin' && $id != $user->id) {
        http_response_code(403);
        echo json_encode(["message" => "Acesso negado. Você só pode acessar o seu próprio perfil."]);
        exit;
    }

    $stmt = $db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
        echo json_encode($profile);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Usuário não encontrado."]);
    }
?>