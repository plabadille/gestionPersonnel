<?php
require_once('classes/PHPMailer/PHPMailerAutoload.php');

    #constantes de classe de mailing
    const EMAIL_EXPEDITEUR = "21101555@etu.unicaen.fr";
    const EMAIL_RETOUR = "21101555@etu.unicaen.fr";
    const EXPEDITEUR = "21101555";
    const EMAIL_SUJET = "[Armee du Congo]informations importantes";

    function sendMailWhenEligiblePromotion($contentHtml, $contentText, $attachment, $sendTo)
    {
        date_default_timezone_set('Etc/UTC');

        $mail = new PHPMailer();

        $mail->isSMTP();

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom(EMAIL_EXPEDITEUR, EXPEDITEUR);
        //Set an alternative reply-to address
        $mail->addReplyTo(EMAIL_RETOUR, EXPEDITEUR);
        //Set who the message is to be sent to
        $mail->addAddress($sendTo['email'], $sendTo['prenom'] . $sendTo['nom']);
        //Set the subject line
        $mail->Subject = EMAIL_SUJET;
        //content
        $mail->Body = $contentHtml;
        $mail->AltBody = $contentText;

        //Attach an image file
        $mail->addAttachment($attachment);

        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
    }

    function sendMailWhenEligibleRetraite($contentHtml, $contentText, $sendTo)
    {
        date_default_timezone_set('Etc/UTC');

        $mail = new PHPMailer();

        $mail->isSMTP();

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom(EMAIL_EXPEDITEUR, EXPEDITEUR);
        //Set an alternative reply-to address
        $mail->addReplyTo(EMAIL_RETOUR, EXPEDITEUR);
        //Set who the message is to be sent to
        $mail->addAddress($sendTo['email'], $sendTo['prenom'] . $sendTo['nom']);
        //Set the subject line
        $mail->Subject = EMAIL_SUJET;
        //content
        $mail->Body = $contentHtml;
        $mail->AltBody = $contentText;

        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
    }