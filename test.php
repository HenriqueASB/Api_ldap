<?php

include_once __DIR__ . '/vendor/autoload.php';


require_once('config.php');
require_once('ldap.class.php');

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$ldap = new LDAP;

$user = $ldap->authenticate(getenv('TEST_USERNAME'), getenv('TEST_PASSWORD'));

var_dump($user);

?>
