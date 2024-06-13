<?php
// config.php

//github
define('CLIENT_ID', '4ca87aec846e119258f4');
define('CLIENT_SECRET', '1bb35a027bf0a51875b9e04061b71985599acc9e');
define('REDIRECT_URI', 'https://dam107.auroraswebs.es/view/home.php');

define('LOGIN_URL', 'https://github.com/login/oauth/authorize');
define('TOKEN_URL', 'https://github.com/login/oauth/access_token');
define('USER_URL', 'https://api.github.com/user');

// DATABASE
define('DB', 'designcode');
define('DB_LOCATION', 'localhost');
define('DBUSER', 'angel');
define('DBPASSWD', '09102004_09102004');

// ENCRYPT
define('SECRET_KEY', '00f45933e72f1f2295efeebb9739583507ba2383695bf8ac2dfd575fa6410957');
?>