<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../controller/projectcontroller.php';
require_once '../controller/usercontroller.php';
session_start();
// Función para redirigir al usuario a la página de inicio de sesión de GitHub
function redirectToGitHubLoginPage() {
  $login_url = "https://github.com/login/oauth/authorize?client_id=4ca87aec846e119258f4";
  header("Location: $login_url");
  exit();
}


// Verificar si ya tenemos un token de acceso en la sesión
if (isset($_SESSION['github_access_token'])) {
} elseif (isset($_GET['code'])) {
    // Si recibimos un código de autorización de GitHub, intercambiamos por un token de acceso
    exchangeCodeForAccessToken($_GET['code']);
} else {
    // Si no hay un token de acceso, redirigir al usuario a la página de inicio de sesión de GitHub
    redirectToGitHubLoginPage();
}



// Función para listar los repositorios del usuario con botones para crear proyectos
function listRepositories() {
    // Obtener el token de acceso de la sesión
    $access_token = $_SESSION['github_access_token'];
    // Construir la URL para obtener la lista de repositorios del usuario, incluyendo los privados
    $repos_url = "https://api.github.com/user/repos?type=all";

    // Realizar la solicitud GET a la API de GitHub
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $repos_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'User-Agent: AgileDesignDev', 'Authorization: Bearer ' . $access_token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decodificar la respuesta JSON
    $repos = json_decode($response, true);

    // Verificar si la decodificación fue exitosa y si la estructura de datos es la esperada
    if (is_array($repos) && isset($repos[0]['name'])) {
        // Mostrar la lista de repositorios
        echo "<h2>Lista de Repositorios:</h2>";
        echo "<ul>";
        foreach ($repos as $repo) {
            echo "<li>{$repo['name']}</li>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='repoId' value='{$repo['id']}'>";
            echo "<input type='hidden' name='repoName' value='{$repo['name']}'>";
            echo "<input type='hidden' name='ownerId' value='{$repo['owner']['id']}'>";
            echo "<button type='submit' name='crearProyectoBtn'>Crear Proyecto en {$repo['name']}</button>";
            echo "</form>";
        }
        echo "</ul>";
    } else {
        echo "No se pudo obtener la lista de repositorios o la respuesta no tiene el formato esperado.";
        echo "Respuesta de la API de GitHub: " . $response;
    }
}

// Procesar la creación de un proyecto al hacer clic en un botón
if (isset($_POST['crearProyectoBtn'])) {
    include '../config/config.php';
    include '../controller/encrypt.php';

    // Verificar si se han enviado los datos necesarios
    if (isset($_POST['repoId'], $_POST['repoName'], $_POST['ownerId'])) {
        $repoId = $_POST['repoId'];
        $repoName = $_POST['repoName'];
        $ownerId = $_POST['ownerId'];

        $data = $repoId .','. $repoName .','. $ownerId;
        $encryptedData = encryptData($data, SECRET_KEY);
        $url = "creator.php?data=" . urlencode($encryptedData);
        
        // Redirigir automáticamente a la URL
        header("Location: $url");
        exit(); // Asegurarse de que no se ejecute más código después de la redirección
        
    } else {
        echo "Error: Datos insuficientes para crear el proyecto.";
    }
}

listRepositories()
?>