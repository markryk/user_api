<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
    $dotenv->load();

    /**
     * Envia email usando PHPMailer
     */
    function sendMail($to, $subject, $body) {
        $mail = new PHPMailer(true);
        try {
            // Configuração do servidor SMTP (use seu provedor)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('EMAIL_USERNAME');
            $mail->Password = getenv('EMAIL_PWD'); // use senha de app do Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Configurações da mensagem
            $mail->setFrom(getenv('EMAIL_USERNAME'), 'API Exemplo');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }
?>