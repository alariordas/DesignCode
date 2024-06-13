<?php
// end.php
include 'config/config.php';
include 'controller/encrypt.php';

if (isset($_GET['data'])) {
    $encryptedData = $_GET['data'];
    $decryptedData = decryptData($encryptedData, SECRET_KEY);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>End Page</title>
</head>
<body>
    <p>
        <?php
        if (isset($decryptedData)) {
            echo "Datos desencriptados: " . $decryptedData;
        } else {
            echo "No se recibieron datos o los datos no se pudieron desencriptar.";
        }
        ?>
    </p>
</body>
</html>
