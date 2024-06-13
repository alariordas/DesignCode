<?php
// start.php
include 'config/config.php';
include 'controller/encrypt.php';

$data = "123456789";
$encryptedData = encryptData($data, SECRET_KEY);
$url = "end.php?data=" . urlencode($encryptedData);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Start Page</title>
</head>
<body>
    <a href="<?php echo $url; ?>">Go to End Page</a>
</body>
</html>
