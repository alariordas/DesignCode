<?php
// Redirigir al usuario a la página de inicio de sesión de GitHub
$login_url = "https://github.com/login/oauth/authorize?client_id=4ca87aec846e119258f4";
header("Location: $login_url");
exit();
?>
