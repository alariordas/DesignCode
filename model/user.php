<?php
// model/user.php

require_once 'database.php';

class User {
    private $id;
    private $userName;
    private $name;
    private $location;
    private $followers;
    private $follows;
    private $userPhotoUrl;
    private $mail;

    public function __construct($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail) {
        $this->id = $id;
        $this->userName = $userName;
        $this->name = $name;
        $this->location = $location;
        $this->followers = $followers;
        $this->follows = $follows;
        $this->userPhotoUrl = $userPhotoUrl;
        $this->mail = $mail;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getName() {
        return $this->name;
    }

    public function getLocation() {
        return $this->location;
    }

    public function getFollowers() {
        return $this->followers;
    }

    public function getFollows() {
        return $this->follows;
    }

    public function getUserPhotoUrl() {
        return $this->userPhotoUrl;
    }

    public function getMail() {
        return $this->mail;
    }

    public static function getAllUsers() {
        $conn = conectar();

        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = new User($row['id'], $row['UserName'], $row['Name'], $row['Location'], $row['Followers'], $row['Follows'], $row['UserPhotoUrl'], $row['mail']);
            }
        }

        desconectar($conn);

        return $users;
    }

    public static function getUsersById($userId) {
        $conn = conectar();

        if ($userId === null) {
            return null;
        }

        $safeUserId = $conn->real_escape_string($userId);

        $sql = "SELECT * FROM users WHERE id = '$safeUserId'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user = new User($row['id'], $row['UserName'], $row['Name'], $row['Location'], $row['Followers'], $row['Follows'], $row['UserPhotoUrl'], $row['mail']);
        } else {
            $user = null;
        }

        desconectar($conn);

        return $user;
    }

    public static function postUser($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail, $preferenceIds) {
        $conn = conectar();

        $existingUser = self::getUsersById($id);

        if ($existingUser) {
            desconectar($conn);
            return null;
        }

        $safeId = $conn->real_escape_string($id);
        $safeUserName = $conn->real_escape_string($userName);
        $safeName = $conn->real_escape_string($name);
        $safeLocation = $conn->real_escape_string($location);
        $safeFollowers = $conn->real_escape_string($followers);
        $safeFollows = $conn->real_escape_string($follows);
        $safeUserPhotoUrl = $conn->real_escape_string($userPhotoUrl);
        $safeMail = $conn->real_escape_string($mail);

        $sql = "INSERT INTO users (id, UserName, Name, Location, Followers, Follows, UserPhotoUrl, mail) VALUES ('$safeId', '$safeUserName', '$safeName', '$safeLocation', '$safeFollowers', '$safeFollows', '$safeUserPhotoUrl', '$safeMail')";
        $result = $conn->query($sql);

        if ($result) {
            $newUser = new User($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail);
            
            // Insert preferences into user_preferences
            foreach ($preferenceIds as $preferenceId) {
                $safePreferenceId = $conn->real_escape_string($preferenceId);
                $sql = "INSERT INTO user_preferences (user_id, preference_id) VALUES ('$safeId', '$safePreferenceId')";
                $conn->query($sql);
            }
        } else {
            $newUser = null;
        }

        desconectar($conn);

        return $newUser;
    }

    public static function updateUserById($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail) {
        $conn = conectar();

        $safeId = $conn->real_escape_string($id);
        $safeUserName = $conn->real_escape_string($userName);
        $safeName = $conn->real_escape_string($name);
        $safeLocation = $conn->real_escape_string($location);
        $safeFollowers = $conn->real_escape_string($followers);
        $safeFollows = $conn->real_escape_string($follows);
        $safeUserPhotoUrl = $conn->real_escape_string($userPhotoUrl);
        $safeMail = $conn->real_escape_string($mail);

        $sql = "UPDATE users SET UserName='$safeUserName', Name='$safeName', Location='$safeLocation', Followers='$safeFollowers', Follows='$safeFollows', UserPhotoUrl='$safeUserPhotoUrl', mail='$safeMail' WHERE id='$safeId'";
        $result = $conn->query($sql);

        if ($result) {
            $updatedUser = new User($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail);
        } else {
            $updatedUser = null;
        }

        desconectar($conn);

        return $updatedUser;
    }
    public static function lowUpdateUserById($id, $userName = null, $location = null, $followers = null, $follows = null, $userPhotoUrl = null) {
        $conn = conectar();
        $safeId = $conn->real_escape_string($id);
    
        // Inicializa un array para almacenar los pares de clave-valor de columnas y valores a actualizar
        $updates = array();
    
        // Verifica qué campos se han proporcionado y agrega los pares de clave-valor correspondientes al array
        if ($userName !== null) {
            $safeUserName = $conn->real_escape_string($userName);
            $updates[] = "UserName='$safeUserName'";
        }
        if ($location !== null) {
            $safeLocation = $conn->real_escape_string($location);
            $updates[] = "Location='$safeLocation'";
        }
        if ($followers !== null) {
            $safeFollowers = $conn->real_escape_string($followers);
            $updates[] = "Followers='$safeFollowers'";
        }
        if ($follows !== null) {
            $safeFollows = $conn->real_escape_string($follows);
            $updates[] = "Follows='$safeFollows'";
        }
        if ($userPhotoUrl !== null) {
            $safeUserPhotoUrl = $conn->real_escape_string($userPhotoUrl);
            $updates[] = "UserPhotoUrl='$safeUserPhotoUrl'";
        }
    
        // Verifica que haya al menos un campo para actualizar
        if (empty($updates)) {
            // No hay campos para actualizar, retornar algún tipo de error o mensaje
            return null; // o puedes lanzar una excepción o retornar un mensaje de error
        }
    
        // Construye la parte SET de la consulta SQL utilizando los pares de clave-valor
        $setClause = implode(', ', $updates);
    
        // Construye la consulta SQL con la parte SET dinámica
        $sql = "UPDATE users SET $setClause WHERE id='$safeId'";
    
        // Ejecuta la consulta SQL y maneja el resultado
        $result = $conn->query($sql);
    
        if ($result) {
            // Si la actualización fue exitosa, obtenemos el usuario actualizado de la base de datos
            $updatedUser = User::getUsersById($id);
        } else {
            // Si hubo un error en la actualización, establecemos updatedUser a null
            $updatedUser = null;
        }
    
        desconectar($conn);
    
        return $updatedUser;
    }
    
}

?>

