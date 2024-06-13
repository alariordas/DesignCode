<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verificar si ya tenemos un token de acceso en la sesión
if (isset($_SESSION['github_access_token'])) {
    updateUserInfo();
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

// Definir la función que maneja las opciones
function manejarOpciones($opcion, $numero) {
    return "<div class=\"$opcion\"><h1>La palabra generada es....</h1><p>$opcion</p><p>$numero</p></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  
  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
  <link rel="stylesheet" href="/styles/home.css">
  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.filter-button');
            // limpiarGeneradas();

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    button.classList.toggle('active'); // Alternar clase 'active' al hacer clic
                    ejecutarEjemplo();
                });
            });

            function distribuirSuma(total, partes) {
                const resultado = new Array(partes).fill(Math.floor(total / partes));
                let resto = total % partes;
                for (let i = 0; i < resto; i++) {
                    resultado[i]++;
                }
                return resultado;
            }

            function ejecutarEjemplo() {
                var opciones = [];
                var todasOpciones = ['item1', 'item2', 'item3', 'item4', 'item5', 'item6'];

                buttons.forEach(button => {
                    if (button.classList.contains('active')) {
                        opciones.push(button.dataset.filter); // Usar data-filter en lugar de textContent
                    }
                });

                // Obtener las opciones ya generadas desde localStorage
                var generadas = JSON.parse(localStorage.getItem('generadas')) || {};

                // Calcular la distribución de la suma de 30
                var incrementos = distribuirSuma(30, opciones.length);

                opciones.forEach(function(opcion, index) {
                    var contador = generadas[opcion] || 0;
                    contador += incrementos[index];

                    // Verificar si el elemento ya está visible y quitar el display none
                    var elementos = document.getElementsByClassName(opcion);
                    for (var i = 0; i < elementos.length; i++) {
                        elementos[i].style.display = '';
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "home.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var nuevoContenido = document.createElement("div");
                            nuevoContenido.innerHTML = xhr.responseText;
                            document.getElementById("resultado").appendChild(nuevoContenido);

                            // Actualizar el contador en localStorage
                            generadas[opcion] = contador;
                            localStorage.setItem('generadas', JSON.stringify(generadas));
                        }
                    };

                    var data = "opcion=" + encodeURIComponent(opcion) + "&numero=" + contador;
                    xhr.send(data);
                });

                // Ocultar las opciones deseleccionadas
                todasOpciones.forEach(function(opcion) {
                    if (!opciones.includes(opcion)) {
                        var elementos = document.getElementsByClassName(opcion);
                        for (var i = 0; i < elementos.length; i++) {
                            elementos[i].style.display = 'none';
                        }
                    }
                });
            }

            // Limpiar las opciones generadas en localStorage
            function limpiarGeneradas() {
                localStorage.removeItem('generadas');
                document.getElementById("resultado").innerHTML = '';
            }
        });
    </script>
</head>
<body>
  <header>
    <nav>
    <div>
<h2><span>Design</span><span style="color: #d14cff">Code</span></h2>
<a href="https://dam107.auroraswebs.es/view/crea.php"><p>Crea un proyecto</p></a>
    </div>
    <div>
      <div class="search">
        <input type="text" id="externalInput" placeholder="Proyectos chulos...">
        <!-- Botón para enviar el formulario -->
        <button type="button" id="submitButton"><img src="   https://cdn-icons-png.flaticon.com/512/622/622669.png " alt="" srcset=""></button>
      </div>
      <!-- Campo de texto fuera del formulario principal -->
    
  
  <div class="action">
    <div class="profile">
        <?php
      echo "<img src='" . $avatarUrl . "' alt='profile-img'>";
      ?>
    </div>
   
    <div class="menu">
        <?php
      echo "<h3>". $username ."<br/><span>Website Designer</span></h3>";
      ?>
      <ul>
        <li>
          <i class="far fa-user-circle"></i>
          <a href="#">My Profile</a>
        </li>
        <li>
          <i class="far fa-edit"></i>
          <a href="#">Edit Profile</a>
        </li>
        <li>
          <i class="far fa-envelope"></i>
          <a href="#">Inbox</a>
        </li>
        <li>
          <i class="fas fa-user-cog"></i>
          <a href="#">Settings</a>
        </li>
        <li>
          <i class="far fa-question-circle"></i>
          <a href="#">Help</a>
        </li>
        <li>
          <i class="far fa-envelope"></i>
          <a href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>


    </div>
  </nav>
</header>

<nav class="filters">
  <form action="#" method="post" class="filters">
    <div>
            <button type="button" class="filter-button" data-filter="item1">Item 1</button>
            <button type="button" class="filter-button" data-filter="item2">Item 2</button>
            <button type="button" class="filter-button" data-filter="item3">Item 3</button>
            <button type="button" class="filter-button" data-filter="item4">Item 4</button>
            <button type="button" class="filter-button" data-filter="item5">Item 5</button>
            <button type="button" class="filter-button" data-filter="item6">Item 6</button>
        </div>
    </form>
  <div style="background: black;"></div>
  <div style="background: white;"></div>
  <div style="background: black;"></div>
   <div style="background: black;"></div>
  <div style="background: white;"></div>
  <div style="background: black;"></div>
</nav>
<section class="grid" id="resultado">
    <?php

    require_once '../controller/projectcontroller.php';


  // Crear una instancia del controlador
$projectController = new ProjectController();


// Obtener todos los proyectos
$projects = $projectController->getAllProjects();
  require_once '../controller/usercontroller.php';
$userController = new UserController();
// Verificar si hay proyectos para procesar
if (!empty($projects)) {
    // Iterar sobre cada proyecto obtenido
    foreach ($projects as $project) {
       
        $user = $userController->getUserById($project->getOwnerId());

    
      
        // Verificar si se encontró el usuario
        echo "<article>";
        echo "<div class='imgContainer'>";
        echo "<button class='gradient' onclick=\"mostrarDialogo('hola', '14', 'pedro')\"><p>" . $project->getName() . "</p></button>";
        $images = $project->getImages();
        $coverImages = [];
        foreach ($images as $image) {
            if ($project->getImagesByProjectAndCategory($project->getRepoId(), "cover")) {
                $coverImages[] = $image;
            }
        }
        
        if (!empty($coverImages)) {
            foreach ($coverImages as $image) {
                echo "<img src='../uploads/{$image}' alt='Imagen del proyecto'>";
            }
        } else {
            echo "<img src='../imageResources/NoImage.webp' alt='Imagen del proyecto'>";
        }
        
        
        echo "</div>";
        echo "<div class='card-container'>";
        echo "<div class='left'>";
        echo "<img src='" . $user->getUserPhotoUrl() . "' alt='Foto de perfil de usuario'>";
        echo "<a href='asdasd'>" . $user->getUserName() . "</a>";
        echo "</div>";
        echo "<div class='right'>";
        echo "</div>";
        echo "</div>";
        echo "</article>";
           
       
    }
} else {
    // No se encontraron proyectos
    echo "No projects found.";
}




    ?>



</section>
</body>
<!-- Definir el diálogo -->
<dialog id="miDialogo">
  <div>
    <p id="mensajeDialogo"></p>
    <button id="cerrarDialogoBtn" onclick="cerrarDialogo()">Cerrar</button>
  </div>
</dialog>

<script>
  // Función para mostrar el diálogo con datos dinámicos
        function mostrarDialogo(nombre, edad, ciudad) {
            const mensaje = `Hola ${nombre}! Tienes ${edad} años y vives en ${ciudad}.`;
            const dialogo = document.getElementById('miDialogo');
            const mensajeDialogo = document.getElementById('mensajeDialogo');

            // Establecer el mensaje en el diálogo
            mensajeDialogo.textContent = mensaje;

            // Mostrar el diálogo
            dialogo.showModal();

            // Agregar un event listener para cerrar el diálogo al hacer clic en el backdrop
            dialogo.addEventListener('click', cerrarDialogoEnBackdrop);
        }
          // Función para mostrar el diálogo con datos dinámicos
        function mostrarDialogoSesionNoIniciada() {
          const dialogo = document.getElementById("miDialogoNoIniciada");
            // Mostrar el diálogo
            dialogo.showModal();

            // Agregar un event listener para cerrar el diálogo al hacer clic en el backdrop
            dialogo.addEventListener('click', cerrarDialogoEnBackdrop);
        }

        // Función para cerrar el diálogo
        function cerrarDialogo() {
            const dialogo = document.getElementById('miDialogo');

            // Cerrar el diálogo
            dialogo.close();

            // Remover el event listener
            dialogo.removeEventListener('click', cerrarDialogoEnBackdrop);
        }

        // Función para cerrar el diálogo al hacer clic en el backdrop
        function cerrarDialogoEnBackdrop(event) {
            const dialogo = document.getElementById('miDialogo');

            // Verificar si el clic ocurrió en el backdrop
            if (event.target === dialogo) {
                // Cerrar el diálogo
                dialogo.close();

                // Remover el event listener
                dialogo.removeEventListener('click', cerrarDialogoEnBackdrop);
            }
        }

        
</script>
<script src="/scripts/home.js"></script>

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
    
    
        // Llama a updateUserInfo() aquí para verificar si se ejecuta
        updateUserInfo();

  
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

// Función para mostrar los detalles del perfil del usuario de GitHub en HTML
function displayUserProfile() {
    $userInfo = getUserInfo();

    // Mostrar los detalles del perfil del usuario en HTML
    echo "<h2>Detalles del Perfil de GitHub:</h2>";
    echo "<p><strong>Id de usuario:</strong> {$userInfo['id']}</p>";
    echo "<img src='{$userInfo['avatar_url']}' alt='Avatar'>";
    echo "<p><strong>Nombre de Usuario:</strong> {$userInfo['login']}</p>";
    echo "<p><strong>Nombre:</strong> {$userInfo['name']}</p>";
    echo "<p><strong>Ubicación:</strong> {$userInfo['location']}</p>";
    echo "<p><strong>Repositorios Públicos:</strong> {$userInfo['public_repos']}</p>";
    echo "<p><strong>Gists Públicos:</strong> {$userInfo['public_gists']}</p>";
    echo "<p><strong>Seguidores:</strong> {$userInfo['followers']}</p>";
    echo "<p><strong>Siguiendo:</strong> {$userInfo['following']}</p>";
    echo "<p><strong>Tipo:</strong> {$userInfo['type']}</p>";
    echo "<p><strong>Fecha de Creación:</strong> {$userInfo['created_at']}</p>";
    echo "<p><strong>Última Actualización:</strong> {$userInfo['updated_at']}</p>";
    echo "<p><strong>Plan:</strong> {$userInfo['plan']['name']}</p>";
}



// Función para redirigir al usuario a la página de inicio de sesión de GitHub
function redirectToGitHubLoginPage() {
    $login_url = "https://github.com/login/oauth/authorize?client_id=4ca87aec846e119258f4";
    header("Location: $login_url");
    exit();
}

function updateUserInfo() {
    $userInfo = getUserInfo();

    $id = $userInfo['id'];
    $userName = $userInfo['login'];
    $name = $userInfo['name'];
    $location = $userInfo['location'];
    $followers = $userInfo['followers'];
    $follows = $userInfo['following'];
    $userPhotoUrl = $userInfo['avatar_url'];
    
    require_once '../controller/usercontroller.php';
    
    // Crear una instancia del UserController
    $userController = new UserController();
    
    // Obtener el usuario por su ID
    $existingUser = $userController->getUserById($id);
    
    if ($existingUser) {
        // Si el usuario existe, actualizar la información
        $updatedUser = $userController->lowUpdateUser($id, $userName, $location, $followers, $follows, $userPhotoUrl);
        if ($updatedUser) {
            // Imprimir mensaje en la consola del navegador
            echo '<script>console.log("Usuario actualizado exitosamente.");</script>';
        } else {
            // Imprimir mensaje en la consola del navegador
            echo '<script>console.error("Error al actualizar el usuario.");</script>';
        }
    } else {
        // Si el usuario no existe, crear uno nuevo con la información proporcionada
        $newUser = $userController->createUser($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl);
        
        if ($newUser) {
            // Imprimir mensaje en la consola del navegador
            echo '<script>console.log("Usuario creado exitosamente.");</script>';
        } else {
            // Imprimir mensaje en la consola del navegador
            echo '<script>console.error("Error al crear el usuario.");</script>';
        }
    }
}


?>
