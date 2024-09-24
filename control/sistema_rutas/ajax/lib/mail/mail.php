<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

/**
 * EnviarMail
 *
 * @param  mixed $bodyHtml
 * @param  mixed $subject
 * @param  mixed $addres
 * @param  mixed $ccs
 * @return void
 */
function EnviarMail($bodyHtml, $subject, $address, $ccs = null,$fromname="Vida Afiliacion")  
{   
   
    $configuracion = [
        "host" => "smtp.gmail.com",
        "port" => 587,
        "username" => "no-responder@vida.com.uy",
        "password" => "2k8.vida",
        "from" => "no-responder@vida.com.uy",
        "fromname" => $fromname,
    ];
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $configuracion["host"];
    $mail->SMTPAuth = true;
    $mail->Username = $configuracion["username"];
    $mail->Password = $configuracion["password"];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $configuracion["port"];
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->setFrom($configuracion["from"], $configuracion["fromname"]);
    $mail->addReplyTo($configuracion["from"], $configuracion["fromname"]);
    $mail->addAddress($address["email"], $address["nombre"]);
    if ($ccs != null) {
        foreach ($ccs as $cc) {
            $mail->addCC($cc["email"], $cc["nombre"]);
        }
    }

    $mail->Body = $bodyHtml;

    if ($mail->send()) {
        return true;
    } else {
        return $mail->ErrorInfo;
    }
}
