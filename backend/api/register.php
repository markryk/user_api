<?php
    include_once "cors.php";

    header("Content-Type: application/json; charset=UTF-8");
    include_once "../config/Database.php";

    $db = (new Database())->getConnection();
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(["message" => "Preencha todos os campos"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["message" => "Email inválido"]);
        exit;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(["message" => "Senha muito curta"]);
        exit;
    }

    //Verifica se já existe email digitado
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(["message" => "Email já cadastrado"]);
        exit;
    }

    $response = [];

    if (!$data) {
        http_response_code(400);
        echo json_encode(["message" => "JSON inválido ou vazio."]);
        exit;
    }

    //Se for um único objeto, transforma em array com 1 item
    if (isset($data["email"])) {
        $data = [$data];
    }

    //Hash da senha
    $hash = password_hash($password, PASSWORD_DEFAULT);

    //Insere o usuário
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hash, $role]);

    echo json_encode(["success" => true]);

    http_response_code(201);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>