<?php
if ($api == 'usuarios') {

    // echo('parametro usuario');
    // exit;

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

        // if (!Usuarios::autorizar($funcao, $login)) {
        //     echo json_encode([
        //         'error' => true,
        //         "message" => "Acesso não autorizado!"
        //     ]);
        //     exit;
        // }


        require_once(realpath(dirname(__FILE__) . '/GET.php'));
    }

    // if ($metodo == 'PUT') {
    //     if (empty($_GET['login'])) {
    //         echo json_encode([
    //             'error' => true,
    //             'message' => "Parâmetro 'login' está ausente!"
    //         ]);
    //         exit;
    //     }

    //     $login = $_GET['login'];
    //     $funcao = 'supervisores';

    //     if (!Usuarios::validarToken($login)) {
    //         echo json_encode([
    //             'error' => true,
    //             'message' => 'Você não está logado, ou seu token é inválido.'
    //         ]);
    //         exit;
    //     }

    //     if (!Usuarios::autorizar($funcao, $login)) {
    //         echo json_encode([
    //             'error' => true,
    //             "message" => "Acesso não autorizado!"
    //         ]);
    //         exit;
    //     }

    //     require_once(realpath(dirname(__FILE__) . '/PUT.php'));
    // }
}
