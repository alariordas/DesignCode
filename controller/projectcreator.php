<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'projectcontroller.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $repoId = $_POST['repoId'] ?? '';
    $name = $_POST['name'] ?? '';
    $ownerId = $_POST['ownerId'] ?? '';
    $tags = $_POST['tags'] ?? [];

    // Debug: Mostrar los datos recibidos
    echo "Datos recibidos:<br>";
    echo "repoId: " . htmlspecialchars($repoId, ENT_QUOTES, 'UTF-8') . "<br>";
    echo "name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "<br>";
    echo "ownerId: " . htmlspecialchars($ownerId, ENT_QUOTES, 'UTF-8') . "<br>";

    // Mostrar los tags si son un array
    echo "tags: " . implode(', ', array_map(function($tag) {
        return htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
    }, $tags)) . "<br>";

    // Verificar existencia de la carpeta de uploads
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Procesar imágenes
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $totalFiles = count($_FILES['images']['name']);
        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = $_FILES['images']['name'][$i];
            $fileTmpName = $_FILES['images']['tmp_name'][$i];
            $fileError = $_FILES['images']['error'][$i];

            if ($fileError === 0) {
                if (move_uploaded_file($fileTmpName, $uploadDir . $fileName)) {
                    $images[] = $fileName;
                } else {
                    echo "Error al mover la imagen: " . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo "Error al cargar la imagen: " . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "<br>";
            }
        }
    }

    // Debug: Mostrar las imágenes procesadas
    echo "Imágenes: " . implode(', ', array_map(function($image) {
        return htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
    }, $images)) . "<br>";

    // Crear instancia de ProjectController
    $projectController = new ProjectController();

    // Crear proyecto con imágenes y tags
    $projectController->crearProyecto($repoId, $name, $ownerId, $images, $tags);
    // Debug: Mostrar las imágenes procesadas
echo "Imágenes: " . implode(', ', array_map(function($image) {
    return htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
}, $images)) . "<br>";

// Crear instancia de ProjectController
$projectController = new ProjectController();

// Crear proyecto con imágenes y tags
$projectController->crearProyecto($repoId, $name, $ownerId, $images, $tags);

// Redirigir a la página de inicio
header("Location: https://dam107.auroraswebs.es/view/home.php");
exit();

}
?>
