<?php
if ($acao == 'store' && $parametro == '') {

    // RECEBE UM JSON COM AS SEGUINTES CARACTERÍSTICAS:

    // {
    //     "nomeLista" : NOME DA TABELA,
    //     "turma": TURMA,
    //     "operadoresForaEscala" : [ARRAY DE MATRICULAS], 
    //     "escala" : [
    //         {
    //             "matricula": MATRICULA,
    //             "tag": TAG,
    //              "localizacao": LOCALIZAÇÃO
    //              "atividade": ATIVIDADE
    //              "transporte": TRANSPORTE   
    //         },
    //     ]
    // }

    // 1- adicionar os valores na tabela listaescala
    // 1.1- precisa receber um json com a lista de escala, um array com os operadores fora de escala, outro com os equipamentos fora de escala e outro com a escala.
    // 2 - adicionar os operadores fora de escala em sua respectiva tabela
    // 3 - adicionar os equipamentos fora de escala em sua respectiva tabela
    // 4 - adicionar a escala gerada em sua respectiva tabela

    // CONECTAR AO BANCO
    $db = DB::connect();

    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (!array_key_exists('turma', $dados)) {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Sem o parâmetro 'turma'"
        ]);
        exit;
    }

    if ($dados['turma'] == "") {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Parâmetro 'turma' está vazio"
        ]);
        exit;
    }

    if (!array_key_exists('gerencia', $dados)) {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Sem o parâmetro 'gerencia'"
        ]);
        exit;
    }

    if ($dados['gerencia'] == "") {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Parâmetro 'gerencia' está vazio"
        ]);
        exit;
    }

    date_default_timezone_set("America/Sao_Paulo");
    $dataCriacao = date("Y-m-d");
    $horarioCriacao = date("H:i:s");

    $nomeLista = "Escala da Turma " . $dados['turma'] . " - " . $dataCriacao;

    if (!array_key_exists('operadoresForaEscala', $dados)) {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Sem o parâmetro operadoresForaEscala"
        ]);
        exit;
    }

    if (!array_key_exists('escala', $dados)) {
        echo json_encode([
            "message" => "erro ao criar lista de escala. Sem o parâmetro escala"
        ]);
        exit;
    }

    foreach ($dados['escala'] as $escala) {
        if (!array_key_exists('matricula', $escala) || !array_key_exists('tagequipamento', $escala) || !array_key_exists('localtrabalho', $escala) || !array_key_exists('atividade', $escala) || !array_key_exists('tagtransporte', $escala)) {
            echo json_encode([
                "message" => "Parâmetros incompletos."
            ]);

            exit;
        }
    }




    // ---------------------------------------
    // VERIFICANDO SE APENAS OPERADORES VÁLIDOS ESTÃO FORA DE ESCALA
    // ---------------------------------------
    for ($i = 0; $i < count($dados['operadoresForaEscala']); $i++) {
        if ($dados['operadoresForaEscala'][$i] <= 5) {
            echo json_encode([
                "message" => "Apenas operadores válidos podem ficar fora de escala"
            ]);
            exit;
        }
    }

    // ---------------------------------------
    // VERIFICANDO SE O OPERADOR ESTÁ ESCALADO EM MÚLTIPLOS EQUIPAMENTOS
    // ---------------------------------------
    for ($i = 0; $i < count($dados['escala']); $i++) {
        if ($dados['escala'][$i]['matricula'] > 5) {
            if (count(array_values(array_filter($dados['escala'], fn ($element) => $element['matricula'] == $dados['escala'][$i]['matricula']))) > 1) {
                echo json_encode([
                    "message" => "Tentando inserir operador válido em múltiplos equipamentos"
                ]);
                exit;
            }
        }
    }



    // ---------------------------------------
    // VERIFICANDO SE O OPERADOR ESTA ESCALADO E FORA DE ESCALA SIMULTANEAMENTE
    // ---------------------------------------
    for ($i = 0; $i < count($dados['escala']); $i++) {
        if (array_search($dados['escala'][$i]['matricula'], $dados['operadoresForaEscala']) !== false) {
            echo json_encode([
                "message" => "Operador não pode está escalado e fora de escala simultaneamente"
            ]);
            exit;
        }
    }

    // ---------------------------------------
    // VERIFICANDO SE O EQUIPAMENTO ESTÁ EM MULTIPLOS CAMPOS
    // ---------------------------------------
    for ($i = 0; $i < count($dados['escala']); $i++) {
        if (count(array_values(array_filter($dados['escala'], fn ($element) => $element['tagequipamento'] == $dados['escala'][$i]['tagequipamento']))) > 1) {
            echo json_encode([
                "message" => "Equipamento escalado em múltiplos campos."
            ]);
            exit;
        }
    }


    // ---------------------------------------
    // VERIFICANDO SE O EQUIPAMENTO ESTÁ EM MULTIPLOS CAMPOS
    // ---------------------------------------
    for ($i = 0; $i < count($dados['operadoresForaEscala']); $i++) {
        if (count(array_values(array_filter($dados['operadoresForaEscala'], fn ($element) => $element == $dados['operadoresForaEscala'][$i]))) > 1) {
            echo json_encode([
                "message" => "Tentando inserir operador múltiplas vezes fora de escala"
            ]);
            exit;
        }
    }



    // INICIA A TRANSAÇÃO
    $db->beginTransaction();
    try {

        // ---------------------------------------
        // VERIFICANDO SE A GERENCIA EXISTE
        // ---------------------------------------

        $sql = $db->prepare('select * from gerencia where id = ?');
        $sql->execute([$dados['gerencia']]);
        $gerencia = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$gerencia) {
            echo json_encode([
                "message" => "Gerência {$dados['gerencia']} não encontrada.",
            ]);
            exit;
        }



        // ---------------------------------------
        // VERIFICANDO SE O OPERADOR EXISTE E É AUTORIZADO A OPERAR UM EQUIPAMENTO
        // ---------------------------------------

        for ($i = 0; $i < count($dados['escala']); $i++) {
            $escala = $dados['escala'][$i];
            $sql = $db->prepare('SELECT * from operadores where operadores.matricula = ?');
            $sql->execute([$dados['escala'][$i]['matricula']]);
            $operador = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$operador) {
                echo json_encode([
                    "message" => "Operador {$escala['matricula']} não encontrado.",
                ]);
                exit;
            }

            $sql = $db->prepare('SELECT * from equipamentos where equipamentos.tag = ?');
            $sql->execute([$escala['tagequipamento']]);
            $equipamento = $sql->fetch(PDO::FETCH_ASSOC);


            if (!$equipamento) {
                echo json_encode([
                    "message" => "Equipamento {$escala['tagequipamento']} não encontrado.",
                ]);
                exit;
            }

            $categoria = $equipamento['categoria'];

            if (!$operador[$categoria]) {
                echo json_encode([
                    "message" => "Operador {$operador['nome']} - {$operador['matricula']} não é autorizado a operar {$equipamento['tag']}",
                ]);
                exit;
            }

            $sql = $db->prepare('select * from transporte where tag = ?');
            $sql->execute([$escala['tagtransporte']]);
            $transporte = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$transporte) {
                echo json_encode([
                    "message" => "Equipamento de transporte não encontrado"
                ]);
                exit;
            }
        }

        // -----------------------------
        // VERIFICANDO SE OS OPERADORES FORA DE ESCALA SÃO VÁLIDOS
        // -----------------------------

        for ($i = 0; $i < count($dados['operadoresForaEscala']); $i++) {
            $operadorForaEscala = $dados['operadoresForaEscala'][$i];
            $sql = $db->prepare('SELECT * from operadores where operadores.matricula = ?');
            $sql->execute([$operadorForaEscala]);
            $operador = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$operador) {
                echo json_encode([
                    "message" => "Operador $operadorForaEscala não encontrado.",
                ]);
                exit;
            }
        }


        // Inserir na tabela listaescalas
        $comando = "INSERT INTO listaescalas (nome, horarioCriacao, dataCriacao, turma, idgerencia) VALUES (?,?,?,?,?)";
        $sql = $db->prepare($comando);
        // USANDO PREPARED STATEMENTS
        $sql->execute([$nomeLista, $horarioCriacao, $dataCriacao, $dados['turma'], $dados['gerencia']]);


        // PEGA O ULTIMO ID INSERIDO
        $idLista = $db->lastInsertId();


        // Inserir na tabela operadorforaescala
        $comando = "INSERT INTO operadoresforaescala (matricula, idLista) VALUES (?,?)";
        $sql = $db->prepare($comando);

        foreach (array_values($dados['operadoresForaEscala']) as $valores) {
            $sql->execute([$valores, $idLista]);
        }

        // Inserir na tabela operadorequipamento
        $comando = "INSERT INTO operadorequipamento (matricula, tagequipamento, idLista, localtrabalho, atividade, tagtransporte) VALUES (?,?,?,?,?,?)";
        $sql = $db->prepare($comando);

        foreach (array_values($dados['escala']) as $valores) {
            $sql->execute([$valores['matricula'], $valores['tagequipamento'], $idLista, $valores['localtrabalho'], $valores['atividade'], $valores['tagtransporte']]);
        }


        // Confirma as alterações no banco de dados
        $db->commit();



        echo json_encode([
            "message" => "Dados inseridos com sucesso!",
            "id" => $idLista,
        ]);
    } catch (Exception $e) {
        $db->rollBack();
        // Em caso de erro, reverte as alterações
        echo json_encode([
            "message" => "Erro ao inserir os dados.",
            "error" => $e->getMessage(),
        ]);
    }

    exit;
}
