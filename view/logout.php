<?php
session_start();
logout();

// Función para cerrar la sesión y redirigir al usuario a la página de inicio
function logout() {
    // Eliminar todas las variables de sesión
    $_SESSION = array();

    // Si se desea eliminar la cookie de sesión, es posible que también se necesiten más ajustes
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir la sesión
    session_destroy();

    // Redirigir al usuario a la página de inicio o a donde sea necesario
    redirectToHome();
}

// Función para redirigir al usuario a la página de inicio
function redirectToHome() {
    $home_url = "https://dam107.auroraswebs.es/";
    header("Location: $home_url");
    exit();
}

// Llamar a la función de logout si se ha enviado un parámetro 'logout' por GET
if (isset($_GET['logout'])) {
    logout();
}
?>
