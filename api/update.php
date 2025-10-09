<?php
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../config/Database.php";

    $data = json_decode(file_get_contents("php://input"));
    var_dump($data);

    if (!empty($data->id) && !empty($data->name) && !empty($data->email) && !empty($data->password)) {

        // Obtém os dados
        /*$id = intval($data->id);
        $name = trim($data->name);
        $email = trim($data->email);
        $password = password_hash($data->password, PASSWORD_DEFAULT); // Criptografa a senha*/

        $db = (new Database())->getConnection();

        $query = "UPDATE users SET name=:name, email=:email, password=:password WHERE id=:id";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":id", $data->id);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        $pwd = password_hash($data->password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $pwd);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário atualizado com sucesso!"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Erro ao atualizar usuário."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Dados incompletos."]);
    }
?>