<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require "../vendor/autoload.php";
include_once "../config/Database.php";

header("Content-Type: application/json; charset=UTF-8");

$headers = getallheaders();
$jwt = $headers["Authorization"] ?? null;
$secret_key = "chave_secreta_super_segura";

if ($jwt) {
    try {
        $decoded = JWT::decode(str_replace("Bearer ", "", $jwt), new Key($secret_key, 'HS256'));

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT id, name, email, created_at FROM users");
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Token inválido."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Token não fornecido."]);
}
?>
