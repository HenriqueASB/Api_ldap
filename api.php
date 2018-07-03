<?php
include_once __DIR__ . '/vendor/autoload.php';


require_once('config.php');
require_once('api.class.php');
require_once('ldap.class.php');

$api = new Api;
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if($api->post('auth')) {

    $api->check('username');
    $api->check('password');
    $api->check('token');

    $ldap = new LDAP;

    $user = $ldap->authenticate($api->req['username'], $api->req['password']);

    if(!$user) {
        $api->send("Usuário e/ou senha inválidos!", 401);
    } else {
        $api->send($user);
    }
}

$api->send('Erro 404: conteúdo não encontrado!', 404);

?>
