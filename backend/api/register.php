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

    // verificar email existente
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

    // Se for um único objeto, transforma em array com 1 item
    if (isset($data["email"])) {
        $data = [$data];
    }

    //Hash da senha
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // inserir
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hash, $role]);

    echo json_encode(["success" => true]);
    
    /*$query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $db->prepare($query);

    //O código abaixo insere mais de um usuário por vez
    foreach ($data as $user) {
        if (!empty($user["name"]) && !empty($user["email"]) && !empty($user["password"])) {
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
    }*/
    //até aqui

    //Código antigo (insere um usuário por vez)
    /*if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {

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
    //até aqui
    */

    http_response_code(201);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
