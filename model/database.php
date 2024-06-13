<?php
// model/database.php

function conectar() {
    // Incluir el archivo de configuración
require_once dirname(__DIR__) . '/config/config.php';

    $conn = new mysqli(DB_LOCATION, DBUSER, DBPASSWD, DB);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    return $conn;
}

function desconectar($conn) {
    $conn->close();
}

