<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: index.html');
    exit;
}

$name    = trim($_POST['Imię']         ?? '');
$email   = trim($_POST['email']        ?? '');
$type    = trim($_POST['Typ projektu'] ?? '');
$message = trim($_POST['Wiadomość']    ?? '');

if (!$name || !$email || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html?status=error');
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'h22.seohost.pl';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kontakt@reelmistrz.pl';
    $mail->Password   = 'TWOJE_HASŁO';            // ← uzupełnij hasło do skrzynki
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('kontakt@reelmistrz.pl', 'Reel Mistrz');
    $mail->addAddress('kontakt@reelmistrz.pl', 'Reel Mistrz');
    $mail->addReplyTo($email, $name);

    $mail->Subject = "Nowe zapytanie od {$name}";
    $mail->Body    =
        "Imię:           {$name}\n" .
        "Email:          {$email}\n" .
        "Typ projektu:   {$type}\n\n" .
        "Wiadomość:\n{$message}";

    $mail->send();
    header('Location: index.html?status=success');
    exit;

} catch (Exception $e) {
    error_log('PHPMailer: ' . $mail->ErrorInfo);
    header('Location: index.html?status=error');
    exit;
}
