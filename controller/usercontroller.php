<?php
// Construye la ruta absoluta al archivo model/user.php usando __DIR__
require_once __DIR__ . '/../model/user.php';

class UserController {
    public function getAllUsers() {
        $users = User::getAllUsers();

        if ($users) {
            // Si se encontraron usuarios, retornar el listado
            return $users;
        } else {
            // Si no se encontraron usuarios, retornar un mensaje de error o una lista vacía
            return [];
        }
    }

    public function getUserById($userId) {
        $user = User::getUsersById($userId);

        if ($user) {
            // Si se encontró el usuario, retornar el objeto User
            return $user;
        } else {
            // Si no se encontró el usuario, retornar null o manejar el error según sea necesario
            return null;
        }
    }


        public function createUser($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail, $preferenceIds) {
            // El usuario no existe, procede con la inserción
            $newUser = User::postUser($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail, $preferenceIds);
    
            return $newUser;
        }
    
    
    public function updateUser($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail) {
        // El usuario existe, procede con la actualización
        $updatedUser = User::updateUserById($id, $userName, $name, $location, $followers, $follows, $userPhotoUrl, $mail);

        return $updatedUser;
    }

    public function lowupdateUser($id, $userName, $location, $followers, $follows, $userPhotoUrl) {
        // El usuario existe, procede con la actualización
        $updatedUser = User::lowUpdateUserById($id, $userName, $location, $followers, $follows, $userPhotoUrl);

        return $updatedUser;
    }

}
?>
