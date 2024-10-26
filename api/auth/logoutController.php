<?php

if ($api == 'usuarios') {
    if ($metodo == "POST") {
        if ($acao == "logout" && $parametro == "") {

            if (empty($_GET['matricula'])) {
                echo json_encode([
                    'error' => true,
                    'message' => "Parâmetro 'matricula' está ausente!"
                ]);
                exit;
            }
            
            $matricula = $_GET['matricula'];
            $responseValidacao = Authorization::validarToken($matricula);

            $matricula = addslashes(htmlspecialchars($_GET['matricula'])) ?? '';

            if (!is_numeric($matricula)) {
                echo json_encode([
                    'error' => true,
                    "message" => "O valor do matricula deve ser um número."
                ]);
                exit;
            }

            $logoutResponse = Authorization::logout($matricula);
            echo json_encode($logoutResponse);
            exit;
            if ($logout['error']) {
                echo json_encode($logout);
                exit;
            } 
            if ($autorizacaoResponse['error']) {
                echo json_encode($autorizacaoResponse);
                exit;
            }
            
            $meses = [
                'Janeiro'   => 0,
                'Fevereiro' => 0,
                'Março'     => 0,
                'Abril'     => 0, 
                'Maio'      => 0,
                'Junho'     => 0,
                'Julho'     => 0,
                'Agosto'    => 0,
                'Setembro'  => 0,
                'Outubro'   => 0,
                'Novembro'  => 0, 
                'Dezembro'  => 0
            ];

            $db = DB::connect();
            $stringSQL = "SELECT matricula, COUNT(*) AS total_ocorrencias,
            CASE 
            WHEN mes = 1 THEN 'Janeiro'
            WHEN mes = 2 THEN 'Fevereiro'
            WHEN mes = 3 THEN 'Março'
            WHEN mes = 4 THEN 'Abril'
            WHEN mes = 5 THEN 'Maio'
            WHEN mes = 6 THEN 'Junho'
            WHEN mes = 7 THEN 'Julho'
            WHEN mes = 8 THEN 'Agosto'
            WHEN mes = 9 THEN 'Setembro'
            WHEN mes = 10 THEN 'Outubro'
            WHEN mes = 11 THEN 'Novembro'
            WHEN mes = 12 THEN 'Dezembro'
            END AS mes
            FROM ver_resolver
            where matricula = ?
            GROUP BY matricula, mes";
        
            $sql = $db->prepare($stringSQL);
            $sql->execute([$loginResponse['data']['matricula']]);
            $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($obj2 as $resultado){
                $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
            }
        
            $loginResponse['data']['ocorrenciasPorMes'] = $meses;
            echo json_encode($loginResponse);
            exit;
        }
    }
}
