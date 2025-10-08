<?php
/* Esse código insere mais de um usuário por vez */

header("Content-Type: application/json; charset=UTF-8");
include_once "../config/Database.php";

$db = (new Database())->getConnection();
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$response = [];

if (!$data) {
    http_response_code(400);
    echo json_encode(["message" => "JSON inválido ou vazio."]);
    exit;
}

// Se for um único objeto, transforma em array com 1 item
if (isset($data["email"])) {
    $data = [$data];
}

$query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
$stmt = $db->prepare($query);

foreach ($data as $user) {
    if (
        !empty($user["name"]) &&
        !empty($user["email"]) &&
        !empty($user["password"])
    ) {
        $stmt->bindParam(":name", $user["name"]);
        $stmt->bindParam(":email", $user["email"]);
        $pwd = password_hash($user["password"], PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $pwd);

        try {
            $stmt->execute();
            $response[] = [
                "email" => $user["email"],
                "status" => "sucesso"
            ];
        } catch (PDOException $e) {
            $response[] = [
                "email" => $user["email"],
                "status" => "erro",
                "motivo" => "E-mail já cadastrado ou erro no banco."
            ];
        }
    } else {
        $response[] = [
            "email" => $user["email"] ?? "desconhecido",
            "status" => "erro",
            "motivo" => "Campos faltando."
        ];
    }
}

http_response_code(201);
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

/* Código antigo (insere um usuário por vez)
header("Content-Type: application/json; charset=UTF-8");
include_once "../config/Database.php";

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    $db = (new Database())->getConnection();

    $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":password", password_hash($data->password, PASSWORD_DEFAULT));

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Usuário criado com sucesso!"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Erro ao criar usuário."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Dados incompletos."]);
}
*/
?>
