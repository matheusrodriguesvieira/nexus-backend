<?php
if ($api == 'validate-token') {
    if ($metodo == 'GET') {
        if ($acao == "usuarios" && $parametro == "") {
            if (empty($_GET['login'])) {
                echo json_encode([
                    'error' => true,
                    'message' => "Parâmetro 'login' está ausente!"
                ]);
                exit;
            }

            $login = $_GET['login'];
            $responseValidacao = Usuarios::validarToken($login);
            echo json_encode($responseValidacao);
            exit;
        }
    }
}
