<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

    require "../vendor/autoload.php";

    function authenticate($requireAdmin = false) {
        header("Content-Type: application/json; charset=UTF-8");
        $secret_key = "chave_secreta_super_segura";
        $headers = getallheaders();
        $jwt = $headers["Authorization"] ?? null;

        if (!$jwt) {
            http_response_code(401);
            echo json_encode(["message" => "Token não fornecido."]);
            exit;
        }

        try {
            // Remove o prefixo "Bearer " caso exista
            $jwt = str_replace("Bearer ", "", $jwt);
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $userData = $decoded->data;

            // Se for exigido admin, verifica o role
            if ($requireAdmin && ($userData->role ?? 'user') !== 'admin') {
                http_response_code(403);
                echo json_encode(["message" => "Acesso negado. Somente administradores."]);
                exit;
            }

            // Retorna dados do usuário autenticado
            return $userData;

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                "message" => "Token inválido ou expirado.",
                "error" => $e->getMessage()
            ]);
            exit;
        }
    }
?>