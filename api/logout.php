<?php
include_once "../config/Database.php";
include_once "auth.php";

header("Content-Type: application/json; charset=UTF-8");

$user = authenticate(); // precisa estar logado

$db = (new Database())->getConnection();

// Remove o refresh token do banco
$stmt = $db->prepare("UPDATE users SET refresh_token = NULL WHERE id = :id");
$stmt->bindParam(":id", $user->id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Logout realizado com sucesso."]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Erro ao realizar logout."]);
}
?>