<?php
    require "../vendor/autoload.php";
    include_once "../config/Database.php";
    include_once "cors.php";

    use Firebase\JWT\JWT;

    header("Content-Type: application/json; charset=UTF-8");

    $data = json_decode(file_get_contents("php://input"));
    $secret_key = "chave_secreta_super_segura";

    if (!empty($data->refresh_token)) {
        $db = (new Database())->getConnection();

        $query = "SELECT * FROM users WHERE refresh_token = :token LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":token", $data->refresh_token);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $payload = [
                "iss" => "localhost",
                "iat" => time(),
                "exp" => time() + (60 * 60),
                "data" => [
                    "id" => $user['id'],
                    "email" => $user['email'], 
                    "role" => $user['role']
                ]
            ];

            $access_token = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "access_token" => $access_token,
                "message" => "Novo token gerado com sucesso."
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Refresh token inválido."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Token não fornecido."]);
    }
?>