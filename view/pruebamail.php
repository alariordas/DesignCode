<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Asumiendo que la función enviarCorreo está definida en otro archivo, por ejemplo, correo.php
require '../controller/mail.php';

// Ejemplo de ejecución de la función

// Datos del destinatario
$destinatarioCorreo = 'alariordas@gmail.com';
$destinatarioNombre = 'Angel';
$asunto = '¡Bienvenido a nuestra plataforma de gestión de proyectos!';
$contenidoHTML = '
    <h1>Bienvenido a nuestra plataforma de gestión de proyectos</h1>
    <p>Hola Angel,</p>
    <p>Nos complace darte la bienvenida a nuestra plataforma. Aquí podrás gestionar todos tus proyectos de manera eficiente y colaborativa.</p>
    <p>Estamos seguros de que encontrarás muchas herramientas útiles para facilitar tu trabajo diario.</p>
    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
    <p>Saludos cordiales,</p>
    <p>El equipo de Gestión de Proyectos</p>
';

// Llamada a la función enviarCorreo
$resultado = enviarCorreo($destinatarioCorreo, $destinatarioNombre, $asunto, $contenidoHTML);

// Mostrar el resultado
echo $resultado;
?>
