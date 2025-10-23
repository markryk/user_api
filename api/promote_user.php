<?php
    include_once "../config/Database.php";
    include_once "auth.php";
    include_once "mailer.php";
    include_once "logger.php";

    header("Content-Type: application/json; charset=UTF-8");

    // Exige autenticação de administrador
    $admin = authenticate(true);

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(["message" => "ID do usuário não fornecido."]);
        exit;
    }

    $db = (new Database())->getConnection();

    // Verifica se o usuário existe
    $stmt = $db->prepare("SELECT id, role FROM users WHERE id = :id");
    $stmt->bindParam(":id", $data->id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["message" => "Usuário não encontrado."]);
        exit;
    }

    if ($user['role'] === 'admin') {
        echo json_encode(["message" => "Usuário já é administrador."]);
        exit;
    }

    if ($stmt->execute()) {
        //Executa a atividade de promover user à admin
        doActivity($data->id, "Promoveu usuário à admin");

        //Registra a atividade
        logActivity($admin->id, "Promoveu usuário à admin", $data->id);

        // Busca email do promovido
        $stmtMail = $db->prepare("SELECT email, name FROM users WHERE id = :id");
        $stmtMail->bindParam(":id", $data->id);
        $stmtMail->execute();
        $promoted = $stmtMail->fetch(PDO::FETCH_ASSOC);

        // Envia email de notificação
        $subject = "Você foi promovido a administrador!";
        $body = "<p>Olá <strong>{$promoted['name']}</strong>,</p>
                <p>Parabéns! Você foi promovido a <b>administrador</b> do sistema.</p>
                <p>— Equipe API</p>";

        sendMail($promoted['email'], $subject, $body);

        echo json_encode(["message" => "Usuário promovido e notificado com sucesso."]);
    }

    // Código antigo, implementado antes da task de envio de email
    // Atualiza o role
    /*$stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
    $stmt->bindParam(":id", $data->id);
    $stmt->execute();

    // Registra log de atividade
    $log = $db->prepare("INSERT INTO activity_logs (admin_id, action, target_user_id) VALUES (:admin, 'Promoveu usuário a admin', :target)");
    // Registra que foi promovido à admin
    //logActivity($user->id, "Logout realizado", null, "Email: {$user->email}");
    $log->bindParam(":admin", $admin->id);
    $log->bindParam(":target", $data->id);
    $log->execute();

    echo json_encode(["message" => "Usuário promovido a admin com sucesso e log registrado."]);*/

    //Código antigo, implementado antes da task de logs
    /*if ($stmt->execute()) {
        echo json_encode(["message" => "Usuário promovido a admin com sucesso."]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Erro ao promover usuário."]);
    }*/
?>