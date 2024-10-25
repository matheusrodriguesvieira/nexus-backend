<?php
if ($api == 'usuarios') {
    if ($metodo == 'GET') {
        
        if (empty($_GET['login'])) {
            echo json_encode([
                'error' => true,
                'message' => "Parâmetro 'login' está ausente!"
            ]);
            exit;
        }
        
        $login = $_GET['login'];
        $responseValidacao = Usuarios::validarToken($login);

        if ($responseValidacao['error']) {
            echo json_encode($responseValidacao);
            exit;
        }

        $responseAutorizacao = Usuarios::autorizar('acesso_tela_principal', $login);

        if ($responseAutorizacao['error']) {
            echo json_encode($responseAutorizacao);
            exit;
        }


        require_once(realpath(dirname(__FILE__) . '/GET.php'));
    }
}
