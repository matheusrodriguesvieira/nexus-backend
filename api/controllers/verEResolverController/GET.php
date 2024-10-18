<?php
if ($acao == 'index' && $parametro == '') {

    

    if (empty($_GET['matricula'])) {
        $response = array(
            "error" => true,
            "message" => 'Parâmetro \'matrícula\' não encontrado.'
        );
        echo json_encode($response);
        exit;
    }

    $matricula = $_GET['matricula'];

    $db = DB::connect();
    $sql = $db->prepare("SELECT * FROM ver_resolver WHERE ver_resolver.matricula = ?");
    $sql->execute([$matricula]);
    $obj = $sql->fetchAll(PDO::FETCH_ASSOC);


    if (!$obj) {
        $response = array(
            "error" => false,
            "message" => "Nenhuma ocorrência disponível."
        );
        echo json_encode($response);
    } else {
        $response = array(
            "error" => false,
            "message" => $obj
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
    $sql = $db->prepare("SELECT * FROM ver_resolver WHERE ver_resolver.id = ?");
    $sql->execute([$parametro]);
    $obj = $sql->fetch(PDO::FETCH_ASSOC);


    if (!$obj) {
        $response = array(
            "error" => true,
            "message" => "Nenhum registro disponível."
        );
        echo json_encode($response);
    } else {
        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($response);
    }
    exit;
}
