<?php

ini_set('display_errors', TRUE);
date_default_timezone_set('America/Sao_Paulo');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 604800');
header('Content-type: application/json');

if (getenv('APP_ENV') == 'PROD') {
    error_reporting(E_ERROR | E_PARSE);
} else {
    error_reporting(E_ALL ^ (E_NOTICE|E_WARNING));
}

?>