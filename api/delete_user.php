<?php
    require "../vendor/autoload.php";
    include_once "../config/Database.php";
    include_once "auth.php";
    include_once "logger.php";
    //include_once "check_admin.php"; //Código antigo, antes da task de criar os logs

    //Código antigo, antes da task de criar os logs
    /*header("Content-Type: application/json; charset=UTF-8");

    $headers = getallheaders();
    $jwt = $headers["Authorization"] ?? null;
    $secret_key = "chave_secreta_super_segura";

    if (!$jwt) {
        http_response_code(401);
        echo json_encode(["message" => "Token não fornecido."]);
        exit;
    }
    $userData = checkAdmin($jwt, $secret_key);
    $data = json_decode(file_get_contents("php://input"));*/

    $admin = authenticate(true);
    $data = json_decode(file_get_contents("php://input"));

    if (!$data->id) {
        http_response_code(404);
        echo json_encode(["message" => "Usuário não encontrado."]);
        exit;
    }

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(":id", $data->id);

    if ($stmt->execute()) {
        //logActivity($admin->id, "Deletou usuário", $data->id); //excluir em breve
        //Registra que foi deletado o usuário
        logActivity($admin->id, "Deletou usuário");
        echo json_encode(["message" => "Usuário deletado com sucesso."]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Erro ao deletar usuário."]);
    }

    //Código antigo, antes da task de criar os logs
    /*if (!empty($data->id)) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(":id", $data->id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuário deletado com sucesso."]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Erro ao deletar usuário."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID não fornecido."]);
    }*/
?>