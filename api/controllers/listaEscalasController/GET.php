<?php
if ($acao == 'index' && $parametro == '') {

    // 1 - PEGAR TODAS AS LISTAS DE ESCALAS E ADICIONAR AS PROPRIEDADES escala, operadoresForaEscala e equipamentosForaEscala como arrays vazios;
    // 2 - FAZER UM LAÇO DE REPETIÇÃO E A CADA LISTA, PEGAR TODOS AS ESCALAS CORRESPONDENTES E ADICIONAR AO ARRAU

    // RECEBE COMO PARÂMETRO UM JSON:
    // {
    //     "turma":TURMA
    // }
    // $json = file_get_contents("php://input");
    // $dados = json_decode($json, true);


    // if (!$dados) {
    //     exit;
    // }

    // if (!array_key_exists('turma', $dados)) {
    //     $response = array(
    //         "message" => 'Parâmetro \'turma\' não encontrado.'
    //     );
    //     echo json_encode($response);
    //     exit;
    // }

    if (!$_GET['turma']) {
        $response = array(
            "message" => 'Parâmetro \'turma\' não encontrado.'
        );
        echo json_encode($response);
        exit;
    }

    if (!$_GET['gerencia']) {
        $response = array(
            "message" => 'Parâmetro \'gerencia\' não encontrado.'
        );
        echo json_encode($response);
        exit;
    }

    $turma = $_GET['turma'];
    $gerencia = $_GET['gerencia'];

    $db = DB::connect();
    $sql = $db->prepare("SELECT * FROM listaescalas WHERE listaescalas.turma = ? and listaescalas.idgerencia = ?");
    $sql->execute([$turma, $gerencia]);
    $obj = $sql->fetchAll(PDO::FETCH_ASSOC);



    for ($i = 0; $i < count($obj); $i++) {
        $obj[$i]['escala'] = [];
        $obj[$i]['operadoresForaEscala'] = [];

        $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, operadorequipamento.tagequipamento, operadorequipamento.localtrabalho, operadorequipamento.atividade, operadorequipamento.tagtransporte FROM operadorequipamento, operadores, usuarios where operadores.matricula = usuarios.matricula and operadores.matricula = operadorequipamento.matricula and operadorequipamento.idlista = ?");
        $sql->execute([$obj[$i]['idlista']]);
        $escala = $sql->fetchAll(PDO::FETCH_ASSOC);



        for ($j = 0; $j < count($escala); $j++) {
            $obj[$i]['escala'][] = $escala[$j];
        }

        $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome FROM operadoresforaescala, operadores, usuarios where operadores.matricula = usuarios.matricula and operadores.matricula = operadoresforaescala.matricula and  operadoresforaescala.idlista = ?");
        $sql->execute([$obj[$i]['idlista']]);
        $operadorForaEscala = $sql->fetchAll(PDO::FETCH_ASSOC);

        for ($j = 0; $j < count($operadorForaEscala); $j++) {
            $obj[$i]['operadoresForaEscala'][] = $operadorForaEscala[$j];
        }
    }

    if ($obj) {
        echo json_encode($obj);
    } else {
        $response = array(
            "message" => "Nenhuma lista disponível."
        );
        echo json_encode($response);
    }
    exit;
}

if ($acao == 'show' && $parametro != '') {
    // RECEBE COMO PARÂMETRO UM JSON:
    // {
    //     "turma":TURMA
    // }
    $db = DB::connect();
    $sql = $db->prepare("SELECT * FROM listaescalas WHERE listaescalas.idlista = ?");
    $sql->execute([$parametro]);
    $obj = $sql->fetch(PDO::FETCH_ASSOC);


    if (!$obj) {
        $response = array(
            "message" => "Nenhuma lista disponível."
        );
        echo json_encode($response);
        exit;
    }

    $obj['escala'] = [];
    $obj['operadoresForaEscala'] = [];

    $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, operadorequipamento.tagequipamento, operadorequipamento.localtrabalho, operadorequipamento.atividade, operadorequipamento.tagtransporte FROM operadorequipamento, operadores, usuarios where operadores.matricula = usuarios.matricula and operadores.matricula = operadorequipamento.matricula and operadorequipamento.idlista = ?");
    $sql->execute([$parametro]);
    $escala = $sql->fetchAll(PDO::FETCH_ASSOC);

    for ($j = 0; $j < count($escala); $j++) {
        $obj['escala'][] = $escala[$j];
    }

    $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome FROM operadoresforaescala, usuarios, operadores where operadores.matricula = usuarios.matricula and operadores.matricula = operadoresforaescala.matricula and  operadoresforaescala.idlista = ?");
    $sql->execute([$parametro]);
    $operadorForaEscala = $sql->fetchAll(PDO::FETCH_ASSOC);

    for ($j = 0; $j < count($operadorForaEscala); $j++) {
        $obj['operadoresForaEscala'][] = $operadorForaEscala[$j];
    }

    echo json_encode($obj);

    exit;
}
