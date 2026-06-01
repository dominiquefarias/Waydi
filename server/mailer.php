<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function sendPasswordReset(string $to, string $name, string $resetUrl): void {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST']      ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER']      ?? '';
    $mail->Password   = $_ENV['SMTP_PASS']      ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
    $mail->CharSet    = 'UTF-8';

    $fromMail = $_ENV['SMTP_FROM_MAIL'] ?? $_ENV['SMTP_USER'] ?? '';
    $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'waydi';
    $mail->setFrom($fromMail, $fromName);
    $mail->addAddress($to, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Recupera tu contraseña · waydi';
    $mail->Body    = "
    <div style=\"font-family:'Plus Jakarta Sans',system-ui,sans-serif;max-width:480px;margin:auto;padding:32px;background:#F6F4FC;border-radius:24px;\">
      <h1 style=\"font-size:24px;color:#332E45;margin-bottom:8px;\">Hola, {$name} 👋</h1>
      <p style=\"color:#807A95;font-size:15px;line-height:1.6;\">
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>waydi</strong>.
      </p>
      <a href=\"{$resetUrl}\"
         style=\"display:inline-block;margin:24px 0;padding:14px 28px;background:#332E45;color:#DCD0FF;
                border-radius:14px;text-decoration:none;font-weight:700;font-size:15px;\">
        Restablecer contraseña
      </a>
      <p style=\"color:#A9A3BC;font-size:13px;\">
        Este enlace expira en <strong>1 hora</strong>. Si no solicitaste el cambio, ignora este email.
      </p>
      <hr style=\"border:none;border-top:1px solid #ECE6FB;margin:24px 0;\" />
      <p style=\"color:#A9A3BC;font-size:11px;text-align:center;\">
        waydi · planifica suave, viaja ligero
      </p>
    </div>";

    $mail->send();
}

function sendTripInvitation(string $to, string $name, string $ownerName, string $tripTitle, string $role): void {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST']      ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER']      ?? '';
    $mail->Password   = $_ENV['SMTP_PASS']      ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
    $mail->CharSet    = 'UTF-8';

    $fromMail  = $_ENV['SMTP_FROM_MAIL'] ?? $_ENV['SMTP_USER'] ?? '';
    $fromName  = $_ENV['SMTP_FROM_NAME'] ?? 'waydi';
    $appUrl    = $_ENV['FRONTEND_URL']   ?? 'http://localhost:5173';
    $roleLabel = $role === 'editor' ? 'editar' : 'ver';

    $mail->setFrom($fromMail, $fromName);
    $mail->addAddress($to, $name);
    $mail->isHTML(true);
    $mail->Subject = "$ownerName te invitó a un viaje · waydi";
    $mail->Body    = "
    <div style=\"font-family:'Plus Jakarta Sans',system-ui,sans-serif;max-width:480px;margin:auto;padding:32px;background:#F6F4FC;border-radius:24px;\">
      <h1 style=\"font-size:24px;color:#332E45;margin-bottom:8px;\">¡Hola, {$name}! ✈️</h1>
      <p style=\"color:#807A95;font-size:15px;line-height:1.6;\">
        <strong>{$ownerName}</strong> te ha invitado a {$roleLabel} el viaje
        <strong style=\"color:#332E45;\">{$tripTitle}</strong> en waydi.
      </p>
      <a href=\"{$appUrl}\"
         style=\"display:inline-block;margin:24px 0;padding:14px 28px;background:#332E45;color:#DCD0FF;
                border-radius:14px;text-decoration:none;font-weight:700;font-size:15px;\">
        Ver invitación en waydi
      </a>
      <p style=\"color:#A9A3BC;font-size:13px;\">
        Inicia sesión y acepta la invitación desde tu panel de viajes.
      </p>
      <hr style=\"border:none;border-top:1px solid #ECE6FB;margin:24px 0;\" />
      <p style=\"color:#A9A3BC;font-size:11px;text-align:center;\">
        waydi · planifica suave, viaja ligero
      </p>
    </div>";

    $mail->send();
}
