<?php
if ($api == 'validate-token') {

    if ($metodo == 'GET') {
        if ($acao == "supervisor" && $parametro == "") {
            if (empty($_GET['login'])) {
                echo json_encode([
                    'error' => true,
                    'message' => "Parâmetro 'login' está ausente!"
                ]);
                exit;
            }

            $login = $_GET['login'];

            if (Usuarios::validarToken($login)) {
                echo json_encode([
                    'error' => false,
                    'message' => 'Token válido.'
                ]);
                exit;
            } else {
                echo json_encode([
                    'error' => true,
                    'message' => 'Você não está logado, ou seu token é inválido.'
                ]);
                exit;
            }
        }

        if ($acao == "operador" && $parametro == "") {

            if (empty($_GET['login'])) {
                echo json_encode([
                    'error' => true,
                    'message' => "Parâmetro 'login' está ausente!"
                ]);
                exit;
            }

            $login = $_GET['login'];

            if (Usuarios::validarToken($login)) {
                echo json_encode([
                    'error' => false,
                    'message' => 'Token válido.'
                ]);
                exit;
            } else {
                echo json_encode([
                    'error' => true,
                    'message' => 'Você não está logado, ou seu token é inválido.'
                ]);
                exit;
            }
        }
    }
}
