<?php
    // request_password_reset.php
    require "../vendor/autoload.php";
    include_once "../config/Database.php";
    include_once "mailer.php"; // já implementado
    header("Content-Type: application/json; charset=UTF-8");

    $data = json_decode(file_get_contents("php://input"));
    if (empty($data->email)) {
        http_response_code(400);
        echo json_encode(["message" => "Email não fornecido."]);
        exit;
    }

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, name, email FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Não vaze informação: responda ok mesmo se não existir
        echo json_encode(["message" => "Se o email existir, você receberá instruções para resetar a senha."]);
        exit;
    }

    // Gerar token seguro (texto que será enviado por email)
    $token = bin2hex(random_bytes(32)); // 64 hex chars
    $token_hash = password_hash($token, PASSWORD_DEFAULT); // armazena hash
    $expires_at = date('Y-m-d H:i:s', time() + 60*60); // expira em 1h

    // Limpar tokens antigos do usuário (opcional)
    $del = $db->prepare("DELETE FROM password_resets WHERE user_id = :uid");
    $del->bindParam(":uid", $user['id']);
    $del->execute();

    // Inserir novo
    $ins = $db->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:uid, :hash, :exp)");
    $ins->bindParam(":uid", $user['id']);
    $ins->bindParam(":hash", $token_hash);
    $ins->bindParam(":exp", $expires_at);
    $ins->execute();

    // Enviar email com link (ex: https://seusite.com/reset_password.php?token=...)
    $resetLink = "https://seusite.com/reset_password.php?token=" . $token;
    $subject = "Redefinição de senha";
    $body = "<p>Olá {$user['name']},</p>
            <p>Recebemos uma solicitação para redefinir sua senha. Clique no link abaixo para criar uma nova senha. O link expira em 1 hora.</p>
            <p><a href='{$resetLink}'>Redefinir minha senha</a></p>
            <p>Se você não solicitou, ignore este email.</p>";

    sendMail($user['email'], $subject, $body);

    echo json_encode(["message" => "Se o email existir, você receberá instruções para resetar a senha."]);
?>