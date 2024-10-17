<?php
if ($acao == 'update') {
    if ($parametro != "") {

        // 1- VERIFICAR SE EXISTE O DADO PARA MODIFICAR;
        // 2- MODIFICAR 
        // 3 - RETORNAR A MENSAGEM DE ERRO OU SUCESSOR

        // RECEBE UM JSON COM O SEGUINTE FORMATO:
        // {
        //     "disponivel": BOOLEANO 
        // }

        $db = DB::connect();
        $sql = $db->prepare("SELECT * FROM operadores WHERE operadores.matricula = ?");
        $sql->execute([$parametro]);
        $obj = $sql->fetchObject();

        if (!$obj) {
            echo json_encode(["message" => "Não foi possível encontrar o operador"]);
            exit;
        }


        $json = file_get_contents("php://input");
        $dados = json_decode($json, true);

        if (!array_key_exists('disponivel', $dados)) {
            echo json_encode([
                "message" => "Parâmetros ausentes"
            ]);
            exit;
        }

        if ($dados['disponivel'] < 0 || $dados['disponivel'] > 1 || $dados['disponivel'] == null) {
            echo json_encode([
                "message" => '"disponivel" precisa ser do tipo booleano'
            ]);
            exit;
        }


        // echo $sql;
        $exec = $db->prepare("UPDATE operadores set disponivel = ? where operadores.matricula = ?");

        try {
            $response = $exec->execute([$dados['disponivel'], $parametro]);
            echo json_encode(["message" => "Dados atualizados com sucesso!"]);
        } catch (Exception $e) {
            echo json_encode([
                "message" => "Erro ao atualizar os dados!",
                "error" => $e->getMessage(),
            ]);
        }

        exit;
    }
}
