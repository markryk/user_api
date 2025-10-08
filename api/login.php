<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require "../vendor/autoload.php";
include_once "../config/Database.php";

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"));
$secret_key = "chave_secreta_super_segura";

$db = (new Database())->getConnection();

$query = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $data->email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data->password, $user['password'])) {
    $payload = [
        "iss" => "localhost",
        "iat" => time(),
        "exp" => time() + (60 * 60),
        "data" => ["id" => $user['id'], "email" => $user['email']]
    ];

    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    echo json_encode([
        "message" => "Login bem-sucedido.",
        "jwt" => $jwt
    ]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Credenciais inválidas."]);
}
?>
