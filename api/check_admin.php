<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    require "../vendor/autoload.php";

    function checkAdmin($jwt, $secret_key) {
        try {
            $decoded = JWT::decode(str_replace("Bearer ", "", $jwt), new Key($secret_key, 'HS256'));
            $role = $decoded->data->role ?? 'user';

            if ($role !== 'admin') {
                http_response_code(403);
                echo json_encode(["message" => "Acesso negado. Somente administradores."]);
                exit;
            }

            return $decoded->data;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido."]);
            exit;
        }
    }
?>