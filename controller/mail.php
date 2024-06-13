<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function enviarCorreo($destinatarioCorreo, $destinatarioNombre, $asunto, $contenidoHTML) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'localhost';  // Dirección del servidor SMTP
        $mail->SMTPAuth = false;    // No se requiere autenticación para localhost
        $mail->Port = 25;           // Puerto SMTP para Postfix

        // Configuración del remitente
        $mail->setFrom('no-reply@auroraswebs.es', 'Design Code');
         // Codificación UTF-8
         $mail->CharSet = 'UTF-8';
        
        // Configuración del destinatario
        $mail->addAddress($destinatarioCorreo, $destinatarioNombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $contenidoHTML;
        $mail->AltBody = strip_tags($contenidoHTML); // Contenido alternativo en texto plano

        $mail->send();
        return 'El mensaje ha sido enviado';
    } catch (Exception $e) {
        return "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}";
    }
}
?>
