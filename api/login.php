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
        "data" => ["id" => $user['id'], "email" => $user['email'], "role" => $user['role']]
    ];

    $access_token = JWT::encode($access_payload, $secret_key, 'HS256');

    // Gerar Refresh Token (expira em 7 dias)
    $refresh_token = bin2hex(random_bytes(32));

    // Salvar refresh token no banco
    $stmt_update = $db->prepare("UPDATE users SET refresh_token = :refresh WHERE id = :id");
    $stmt_update->bindParam(":refresh", $refresh_token);
    $stmt_update->bindParam(":id", $user['id']);
    $stmt_update->execute();

    echo json_encode([
        "message" => "Login bem-sucedido.",
        "access_token" => $access_token,
        "refresh_token" => $refresh_token
    ]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Credenciais inválidas."]);
}
?>
