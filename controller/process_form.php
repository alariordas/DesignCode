<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'usercontroller.php';

// Verificar si ya tenemos un token de acceso en la sesión
if (isset($_SESSION['github_access_token'])) {

} elseif (isset($_GET['code'])) {
  // Si recibimos un código de autorización de GitHub, intercambiamos por un token de acceso
  exchangeCodeForAccessToken($_GET['code']);
} else {
  // Si no hay un token de acceso, redirigir al usuario a la página de inicio de sesión de GitHub
  redirectToGitHubLoginPage();
}
$userInfo = getUserInfo();

$userId = $userInfo['id'];
$avatarUrl = $userInfo['avatar_url'];
$username = $userInfo['login'];
$name = $userInfo['name'];
$location = $userInfo['location'];
$publicRepos = $userInfo['public_repos'];
$publicGists = $userInfo['public_gists'];
$followers = $userInfo['followers'];
$following = $userInfo['following'];
$type = $userInfo['type'];
$createdAt = $userInfo['created_at'];
$updatedAt = $userInfo['updated_at'];
$planName = $userInfo['plan']['name'];





if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $email = $_POST['email'];
    $terminos = isset($_POST['terminos']) ? 1 : 0;
    $preferences = isset($_POST['preferences']) ? $_POST['preferences'] : [];

    // Crear un nuevo usuario utilizando el controlador
    $userController = new UserController();
    $newUser = $userController->createUser($userId, $username, $name, $location, $followers, $following, $avatarUrl, $email, $preferences);

    if ($newUser) {
        echo "Usuario creado exitosamente con sus preferencias.";
// Asumiendo que la función enviarCorreo está definida en otro archivo, por ejemplo, correo.php
require 'mail.php';

// Ejemplo de ejecución de la función

// Datos del destinatario
$destinatarioCorreo = $email;
$destinatarioNombre = $username;
$asunto = '¡Bienvenido a nuestra plataforma de gestión de proyectos!';
$contenidoHTML = '
    <h1>Bienvenido a nuestra plataforma de gestión de proyectos</h1>
    <p>Hola'.$username.',</p>
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


    } else {
        echo "Error al crear el usuario.";
    }
}









?>



<?php
// Función para intercambiar el código de autorización por un token de acceso
function exchangeCodeForAccessToken($code) {
  $token_url = "https://github.com/login/oauth/access_token";
  require_once "../config/config.php";
  $params = array(
      'client_id' => CLIENT_ID,
      'client_secret' => CLIENT_SECRET,
      'code' => $code
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $token_url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);

  // Verificar si hay errores en la solicitud cURL
  if (curl_errno($ch)) {
      echo 'Error al realizar la solicitud cURL: ' . curl_error($ch);
      return;
  }

  curl_close($ch);

  $data = json_decode($response, true);

  // Verificar si se recibió un token de acceso
  if (isset($data['access_token'])) {
      $_SESSION['github_access_token'] = $data['access_token'];
      echo "<script>console.log('" . $_SESSION['github_access_token'] . "');</script>";
  
  

  } else {
      echo "Error al obtener el token de acceso.";
      redirectToHome();
  }
}
// Función para redirigir al usuario a la página de inicio de sesión de GitHub
function redirectToHome() {
  $login_url = "https://dam107.auroraswebs.es/view/home.php";
  header("Location: $login_url");
  exit();
}



// Función para obtener la información del usuario desde la API de GitHub
function getUserInfo() {
  // Obtener el token de acceso de la sesión
  $accessToken = $_SESSION['github_access_token'];

  // Construir la URL para obtener la información del usuario desde la API de GitHub
  $userUrl = "https://api.github.com/user";

  // Inicializar la solicitud CURL
  $ch = curl_init();

  // Establecer las opciones para la solicitud CURL
  curl_setopt($ch, CURLOPT_URL, $userUrl);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/vnd.github.v3+json', // Indica la versión de la API que queremos usar
      'Authorization: Bearer ' . $accessToken, // Incluye el token de acceso en el encabezado de autorización
      'User-Agent: My-App' // Puedes especificar el nombre de tu aplicación aquí
  ]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Indica que queremos recibir la respuesta como una cadena

  // Ejecutar la solicitud CURL y obtener la respuesta
  $response = curl_exec($ch);

  // Verificar si la solicitud fue exitosa
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
      curl_close($ch);
      return null; // Retorna null si hubo un error al obtener la información del usuario
  }

  // Decodificar la respuesta JSON en un array asociativo
  $userInfo = json_decode($response, true);

  // Cerrar la sesión CURL
  curl_close($ch);

  return $userInfo; // Retorna los datos del usuario obtenidos de la API de GitHub
}
?>