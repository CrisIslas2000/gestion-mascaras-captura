<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    function enviarCorreo($destinatario, $asunto, $mensaje) {
        $mail = new PHPMailer(true); // Pasa true para habilitar excepciones
        $mensaje .= '<br><br><br> Favor de revisar la plataforma. <br><br><br> No contestar correo.'; 

        try {

            // Configuración del servidor SMTP
            // $mail->SMTPDebug = 2;   
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com.';
            $mail->SMTPAuth = true;
            $mail->Username = 'cristian.morales@hidalgo.gob.mx';
            $mail->Password = 'Morales2000#';
            $mail->CharSet = "UTF-8";
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Configuración de destinatarios y contenido del correo
            $mail->setFrom('cristian.morales@hidalgo.gob.mx', 'Gestión de Trámite');
            $mail->addAddress($destinatario);
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            // // Añadir adjunto
            // if (!empty($adjunto) && file_exists($adjunto)) {
            //     $mail->addAttachment($adjunto);
            // }

            // Envío del correo
            $mail->send();
            return true;

        } catch (Exception $e) {
            // Captura el error y muestra un mensaje
            // echo $mail->ErrorInfo;
            return false;
        }
    }
?>