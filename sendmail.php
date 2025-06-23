<?php
// Envoi d'email professionnel avec PHPMailer et Gmail SMTP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    $gmailUser = 'votre.email@gmail.com'; // Remplacez par votre adresse Gmail
    $gmailPass = 'votre_mot_de_passe_application'; // Mot de passe d’application Gmail

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $gmailUser;
        $mail->Password = $gmailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // L'expéditeur doit être le compte Gmail utilisé pour l'envoi
        $mail->setFrom($gmailUser, 'Site Architecture');
        $mail->addAddress('azizhicham136@gmail.com');
        $mail->addReplyTo($email, $name);
        $mail->Subject = "Nouveau message de $name";
        $mail->Body = "Nom: $name\nEmail: $email\nMessage:\n$message";

        if ($mail->send()) {
            echo "Votre message a bien été envoyé.";
        } else {
            echo "Erreur lors de l'envoi : ".$mail->ErrorInfo;
        }
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi : ".$mail->ErrorInfo;
    }
} else {
    echo "Méthode non autorisée.";
}
?>
