<?php
if ($api == 'validate-token') {
    if ($metodo == 'GET') {
        if ($acao == "usuarios" && $parametro == "") {
            if (empty($_GET['matricula'])) {
                echo json_encode([
                    'error' => true,
                    'message' => "Parâmetro 'matricula' está ausente!"
                ]);
                exit;
            }

            $matricula = $_GET['matricula'];
            $responseValidacao = Authorization::validarToken($matricula);
            echo json_encode($responseValidacao);
            exit;
        }
    }
}
