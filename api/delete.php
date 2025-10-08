<?php
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../config/Database.php";

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id)) {

        // Obtém os dados
        //$id = intval($data->id);

        $db = (new Database())->getConnection();

        $query = "DELETE FROM users WHERE id=:id";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":id", $data->id);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário excluído com sucesso!"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Erro ao excluir usuário."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Dados incompletos."]);
    }
?>