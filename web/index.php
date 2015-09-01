<?php
// fix basic auth headers for php running fpm/fast-cgi

$authorizationHeader = null;
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
} elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $authorizationHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

// Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW
if (null !== $authorizationHeader) {
    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
    if (count($exploded) == 2) {
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = $exploded;
    }
}

// PHP_AUTH_USER/PHP_AUTH_PW
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $_SERVER['AUTHORIZATION'] = 'Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);
}

// constants, setup of slim-app
require_once("../setup.php");