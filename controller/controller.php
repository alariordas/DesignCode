<?php
// controller.php

class Controller {
    public function handleRequest() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $accessToken = $this->getAccessToken($code);
            $userData = $this->getUserData($accessToken);
            
            // Aquí puedes procesar los datos del usuario como desees,
            // por ejemplo, guardarlos en una sesión o en una base de datos.
            
            // Ejemplo: Guardar el nombre de usuario en la sesión
            $_SESSION['username'] = $userData['login'];
            
            // Redirigir a la página home.php después del inicio de sesión exitoso
            header('Location: view/home.php');
            exit();
        } else {
            // Si no hay código en la URL, redirigir al usuario para iniciar sesión con GitHub
            $this->redirectToGithub();
        }
    }

    private function redirectToGithub() {
        $scopes = urlencode('repo user');
        $url = LOGIN_URL . '?client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&scope=' . $scopes;
        header('Location: ' . $url);
        exit();
    }
    
    

    private function getAccessToken($code) {
        $postParams = array(
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'code' => $code,
            'redirect_uri' => REDIRECT_URI
        );

        $ch = curl_init(TOKEN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'];
    }

    private function getUserData($accessToken) {
        $url = USER_URL . '?access_token=' . $accessToken;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: PHP'));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
