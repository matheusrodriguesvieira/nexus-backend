<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE, POST, PUT");
header("Access-Control-Allow-Headers:Authorization, X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version");



require_once(realpath(dirname(__FILE__) . '/config/loadenv.php'));


$path = explode('/', $_GET['path']);

if (isset($path[0])) {
    $api = $path[0];
} else {
    echo 'caminho não existe';
    exit;
}

$GLOBALS['secretJWT'] = getenv('SECRET_JWT');


if (isset($path[1])) {
    $acao = $path[1];
} else {
    $acao = '';
}

if (isset($path[2])) {
    $parametro = $path[2];
} else {
    $parametro = '';
}

$metodo = $_SERVER['REQUEST_METHOD'];

require_once(realpath(dirname(__FILE__) . '/database/DB.php'));
require_once(realpath(dirname(__FILE__) . '/jwt/JWT.php'));
require_once(realpath(dirname(__FILE__) . '/auth/auth.php'));
require_once(realpath(dirname(__FILE__) . '/auth/loginController.php'));
require_once(realpath(dirname(__FILE__) . '/auth/logoutController.php'));
require_once(realpath(dirname(__FILE__) . '/auth/validateTokenController.php'));
// require_once(realpath(dirname(__FILE__) . '/controllers/verEResolverController/verEResolverController.php'));
require_once(realpath(dirname(__FILE__) . '/controllers/usuariosController/usuariosController.php'));