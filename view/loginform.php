<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


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


require_once '../controller/usercontroller.php';

$userController = new UserController();

if ($userController->getUserById($userId) !== null) {
  header("Location: http://dam107.auroraswebs.es/view/home.php");
  exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" type="text/css" href="../styles/login.css" media="print" onload="this.media='all'" >
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
  <div class="background"></div>
  <div class="container">
    <div class="left-side">
      <h2><span>Design</span><span style="color: #d14cff">Code</span></h2>
    </div>
    <div class="right-side">
      <div class="login-container">
        <div class="login-box">
          <h2>Hola <?= $username?>,</h2>
          <p style="font-weight: 200;">Necesitamos confimar unos datos antes de crear tu cuenta </p>
          <form action="../controller/process_form.php" method="post">
  <h3>¿Cuál es tu correo electronico?</h3>
  <input type="email" name="email" placeholder="Email" required id="email">
  <label for="terminos" style="color: white; margin-bottom:5px;">
    <input type="checkbox" name="terminos" id="terminos" required> Acepto los <a href="">términos y condiciones</a>
  </label>
  
  <h3>¿Cuáles son tus favoritos?</h3>
  <div class="button-grid">
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="1" id="btn1" class="checkable-input">
      <label for="btn1" class="checkable-label">
        <img src="/imageResources/svgs/html.svg" alt="icon" width="32" height="32">
      </label>
    </div>
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="2" id="btn2" class="checkable-input">
      <label for="btn2" class="checkable-label">
        <img src="/imageResources/svgs/nextjs.svg" alt="icon" width="32" height="32">
      </label>
    </div>
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="3" id="btn3" class="checkable-input">
      <label for="btn3" class="checkable-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="27.624999999999996" viewBox="0 0 256 221"><path fill="#41B883" d="M204.8 0H256L128 220.8L0 0h97.92L128 51.2L157.44 0z"/><path fill="#41B883" d="m0 0l128 220.8L256 0h-51.2L128 132.48L50.56 0z"/><path fill="#35495E" d="M50.56 0L128 133.12L204.8 0h-47.36L128 51.2L97.92 0z"/></svg>
      </label>
    </div>
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="4" id="btn4" class="checkable-input">
      <label for="btn4" class="checkable-label">
        <img src="/imageResources/svgs/python.svg" alt="icon" width="32" height="32">
      </label>
    </div>
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="5" id="btn5" class="checkable-input">
      <label for="btn5" class="checkable-label">
        <img src="/imageResources/svgs/angular.svg" alt="icon" width="32" height="32">
      </label>
    </div>
    <div class="checkable-button">
      <input type="checkbox" name="preferences[]" value="6" id="btn6" class="checkable-input">
      <label for="btn6" class="checkable-label">
        <img src="/imageResources/svgs/rust.svg" alt="icon" width="32" height="32">
      </label>
    </div>
  </div>
  <br>
  <button type="submit" class="login-submit" id="btn-submit">Inicia Sesión</button>
</form>

        </div>
      </div>
    </div>
  </div>
</body>

<script>
 document.getElementById('btn-submit').addEventListener('click', function(event) {


  mostrarDatos(
    document.getElementById('email').value,
    document.getElementById('terminos').checked,
    [
      document.getElementById('btn1').checked,
      document.getElementById('btn2').checked,
      document.getElementById('btn3').checked,
      document.getElementById('btn4').checked,
      document.getElementById('btn5').checked,
      document.getElementById('btn6').checked
    ]
  );
});

function mostrarDatos(email, terminos, favoritos) {
  const favoritosText = favoritos.map((fav, index) => fav ? `Opción ${index + 1}: Sí` : `Opción ${index + 1}: No`).join('\n');
  
  alert(`Email: ${email}\nTérminos y Condiciones: ${terminos ? 'Aceptados' : 'No Aceptados'}\nFavoritos:\n${favoritosText}`);
}

var buttons = document.getElementsByClassName('checkable-button');
for (var i = 0; i < buttons.length; i++) {
  var input = buttons[i].querySelector('input');
  // Añadir un evento de clic al div contenedor
  buttons[i].addEventListener('click', function() {
    var input = this.querySelector('input');
    input.checked = !input.checked; // Alternar el estado del checkbox
    this.classList.toggle('checked', input.checked); // Actualizar la clase del div basado en el estado del checkbox
  });

  // Añadir un evento de cambio al checkbox
  input.addEventListener('change', function() {
    this.parentNode.classList.toggle('checked', this.checked); // Actualizar la clase del div basado en el estado del checkbox
  });
}

</script>

</html>
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