<?php
    include_once "../config/Database.php";
    include_once "cors.php";
    include_once "auth.php";
    include_once "logger.php";

    header("Content-Type: application/json; charset=UTF-8");

    $user = authenticate();
    $data = json_decode(file_get_contents("php://input"));

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":id", $data->id);

    if ($stmt->execute()) {
        //Executa a atividade de atualizar os dados do perfil
        doActivity($data->id, "Perfil atualizado");

        //Registra a atividade
        logActivity($user->id, "Atualizou dados do perfil", $data->id, "Novo nome: {$data->name}");
        echo json_encode(["message" => "Perfil atualizado com sucesso."]);
    }
?>