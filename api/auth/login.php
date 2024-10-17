<?php

if ($api == 'supervisores') {
    if ($metodo == "POST") {
        if ($acao == "login" && $parametro == "") {


            $json = file_get_contents("php://input");
            $dados = json_decode($json, true);

            if (!array_key_exists("login", $dados) || !array_key_exists("senha", $dados)) {
                echo json_encode([
                    'error' => true,
                    "message" => "faltam informações de login ou senha."
                ]);
                exit;
            }


            $login = addslashes(htmlspecialchars($dados['login'])) ?? '';
            $senha = addslashes(htmlspecialchars($dados['senha'])) ?? '';


            $retorno = Usuarios::login($login, $senha);

            if ($retorno['error'] || !Usuarios::autorizar($api, $login)) {

                echo json_encode([
                    'error' => true,
                    "message" => "Acesso não autorizado!"
                ]);
            } else {
                echo json_encode($retorno);
                exit;
            }
        }
    }
} else if ($api == 'operadores') {
    if ($acao == "login" && $parametro == "") {
        if ($acao == "login" && $parametro == "") {


            $json = file_get_contents("php://input");
            $dados = json_decode($json, true);

            if (!array_key_exists("login", $dados) || !array_key_exists("senha", $dados)) {
                echo json_encode([
                    'error' => true,
                    "message" => "faltam informações de login ou senha."
                ]);
                exit;
            }


            $login = addslashes(htmlspecialchars($dados['login'])) ?? '';
            $senha = addslashes(htmlspecialchars($dados['senha'])) ?? '';


            $retorno = Usuarios::login($login, $senha);

            if ($retorno['error'] || !Usuarios::autorizar($api, $login)) {

                echo json_encode([
                    'error' => true,
                    "message" => "Acesso não autorizado!"
                ]);
            } else {
                echo json_encode($retorno);
                exit;
            }
        }
    }
}
