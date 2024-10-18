<?php
if ($api == 'ver_e_resolver') {
    if ($metodo == 'GET') {
        // if (empty($_GET['login'])) {
        //     echo json_encode([
        //         'error' => true,
        //         'message' => "Parâmetro 'login' está ausente!"
        //     ]);
        //     exit;
        // }

        // $login = $_GET['login'];
        // $funcao = 'supervisores';

        // if (!Usuarios::validarToken($login)) {
        //     echo json_encode([
        //         'error' => true,
        //         'message' => 'Você não está logado, ou seu token é inválido.'
        //     ]);
        //     exit;
        // }

        require_once(realpath(dirname(__FILE__) . '/GET.php'));
    }
}
