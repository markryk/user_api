<?php
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../config/Database.php";
    include_once "auth.php";
    include_once "logger.php";

    $user = authenticate();
    $data = json_decode(file_get_contents("php://input"));
    //var_dump($data);

    /*if (!$data) {
        http_response_code(404);
        echo json_encode(["message" => "Usuário não encontrado."]);
        exit;
    }*/

    if ($data->id != $user->id && $user->role !== 'admin') {
        http_response_code(403);
        echo json_encode(["message" => "Você só pode editar o próprio perfil."]);
        exit;
    }

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":id", $data->id);

    if ($stmt->execute()) {
        //Executa a atividade de atualizar os dados do usuário
        doActivity($data->id, "Atualizou usuário");

        //Registra a atividade
        logActivity($user->id, "Atualizou usuário", $data->id, "Novo nome: {$data->name}");
        echo json_encode(["message" => "Usuário atualizado com sucesso."]);
    }

    //Código antigo, antes da task de criar os logs
    /*if (!empty($data->id) && !empty($data->name) && !empty($data->email) && !empty($data->password)) {

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
    }*/
?>