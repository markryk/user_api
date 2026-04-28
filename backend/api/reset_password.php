<?php
    // reset_password.php
    require "../vendor/autoload.php";
    include_once "../config/Database.php";
    header("Content-Type: application/json; charset=UTF-8");

    $data = json_decode(file_get_contents("php://input"));
    $token = $data->token ?? '';
    $newPassword = $data->new_password ?? '';

    if (!$token || !$newPassword) {
        http_response_code(400);
        echo json_encode(["message" => "Dados incompletos."]);
        exit;
    }

    $db = (new Database())->getConnection();

    // Procurar por entries de password_resets que ainda não expiraram.
    // Note: como armazenamos apenas hash, precisamos buscar todas as linhas não expiradas e verificar o hash.
    $stmt = $db->prepare("SELECT pr.id, pr.user_id, pr.token_hash, pr.expires_at, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.expires_at >= NOW()");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $found = null;
    foreach ($rows as $r) {
        if (password_verify($token, $r['token_hash'])) {
            $found = $r;
            break;
        }
    }

    if (!$found) {
        http_response_code(400);
        echo json_encode(["message" => "Token inválido ou expirado."]);
        exit;
    }

    // Atualizar senha do usuário
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $upd = $db->prepare("UPDATE users SET password = :pwd WHERE id = :uid");
    $upd->bindParam(":pwd", $newHash);
    $upd->bindParam(":uid", $found['user_id']);
    $upd->execute();

    // Remover todos os tokens desse usuário (invalida tokens)
    $del = $db->prepare("DELETE FROM password_resets WHERE user_id = :uid");
    $del->bindParam(":uid", $found['user_id']);
    $del->execute();

    // Opcional: notificar por email que senha foi alterada
    $subject = "Sua senha foi alterada";
    $body = "<p>Olá,</p><p>Sua senha foi alterada com sucesso. Se você não fez essa alteração, contate o suporte.</p>";
    sendMail($found['email'], $subject, $body);

    echo json_encode(["message" => "Senha alterada com sucesso."]);
?>