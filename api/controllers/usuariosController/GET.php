<?php
if ($acao == 'index' && $parametro == '') {

    // ---------------------------------------
    // PEGA TODOS OS OPERADORES
    // ---------------------------------------

    

    // if (!empty($_GET['codigos']) && $_GET['codigos'] === 'true') {
    //     $db = DB::connect();
    //     $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome from usuarios where usuarios.matricula = usuarios.matricula and usuarios.matricula < 6");
    //     $sql->execute();
    //     $obj = $sql->fetchAll(PDO::FETCH_ASSOC);
    //     if (!$obj) {
    //         $response = array(
    //             "message" => "Nenhum usuário encontrado!"
    //         );
    //         echo json_encode($response);
    //         exit;
    //     }


    //     echo json_encode($obj);
    //     exit;
    // }


    // if (!empty($_GET['turma']) && !empty($_GET['gerencia'])) {

    //     $turma = $_GET['turma'];
    //     $gerencia = $_GET['gerencia'];

    //     $db = DB::connect();

    //     // $sql = $db->prepare("SELECT * from operadores where operadores.matricula > 5");
    //     // $sql->execute();
    //     // $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, operadores.disponivel, usuarios.matriculasupervisor from operadores, usuarios");
    //     $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, usuarios.turma, gerencia.nome as gerencia, operadores.disponivel from usuarios, operadores, gerencia where operadores.matricula = usuarios.matricula and gerencia.id = usuarios.idgerencia and usuarios.matricula > 5 and usuarios.turma = ? and usuarios.idgerencia = ?");
    //     $sql->execute([$turma, $gerencia]);
    //     $obj = $sql->fetchAll(PDO::FETCH_ASSOC);

    //     if (!$obj) {
    //         $response = array(
    //             "message" => "Nenhum operador encontrado!"
    //         );
    //         echo json_encode($response);
    //         exit;
    //     }

    //     for ($i = 0; $i < count($obj); $i++) {


    //         $sql = $db->prepare("SELECT * from operadores where operadores.matricula = ?");
    //         $sql->execute([$obj[$i]['matricula']]);
    //         $operador = $sql->fetch(PDO::FETCH_ASSOC);

    //         unset($operador['matricula']);
    //         unset($operador['disponivel']);

    //         $obj[$i]['autorizadoOperar'] = $operador;
    //     }

    //     echo json_encode($obj);
    //     exit;
    // }

    if (empty($_GET['turma']) && empty($_GET['gerencia'])) {
        $db = DB::connect();
        $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.gerencia, usuarios.turma, usuarios.url_perfil, usuarios.url_status from usuarios");
        $sql->execute();

        $obj = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$obj) {
            $response = array(
                "error" => true,
                "message" => "Nenhum usuário encontrado!"
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

    // if (empty($_GET['gerencia'])) {

    //     $turma = $_GET['turma'];

    //     $db = DB::connect();
    //     $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, usuarios.turma, gerencia.nome as gerencia, usuarios.matriculasupervisor, operadores.disponivel from usuarios, operadores, gerencia where operadores.matricula = usuarios.matricula and gerencia.id = usuarios.idgerencia and usuarios.matricula > 5 and usuarios.turma = ?");
    //     $sql->execute([$turma]);
    //     $obj = $sql->fetchAll(PDO::FETCH_ASSOC);


    //     if (!$obj) {
    //         $response = array(
    //             "message" => "Nenhum operador encontrado!"
    //         );
    //         echo json_encode($response);
    //         exit;
    //     }

    //     for ($i = 0; $i < count($obj); $i++) {

    //         $sql = $db->prepare("SELECT * from operadores where operadores.matricula = ?");
    //         $sql->execute([$obj[$i]['matricula']]);
    //         $operador = $sql->fetch(PDO::FETCH_ASSOC);

    //         unset($operador['matricula']);
    //         unset($operador['disponivel']);

    //         $obj[$i]['autorizadoOperar'] = $operador;
    //     }

    //     echo json_encode($obj);
    //     exit;
    // }

    // if (empty($_GET['turma'])) {

    //     $gerencia = $_GET['gerencia'];

    //     $db = DB::connect();
    //     $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, usuarios.turma, gerencia.nome as gerencia, usuarios.matriculasupervisor, operadores.disponivel from usuarios, operadores, gerencia where operadores.matricula = usuarios.matricula and gerencia.id = usuarios.idgerencia and usuarios.matricula > 5 and usuarios.idgerencia = ?");
    //     $sql->execute([$gerencia]);
    //     $obj = $sql->fetchAll(PDO::FETCH_ASSOC);

    //     if (!$obj) {
    //         $response = array(
    //             "message" => "Nenhum operador encontrado!"
    //         );
    //         echo json_encode($response);
    //         exit;
    //     }

    //     for ($i = 0; $i < count($obj); $i++) {

    //         $sql = $db->prepare("SELECT * from operadores where operadores.matricula = ?");
    //         $sql->execute([$obj[$i]['matricula']]);
    //         $operador = $sql->fetch(PDO::FETCH_ASSOC);

    //         unset($operador['matricula']);
    //         unset($operador['disponivel']);

    //         $obj[$i]['autorizadoOperar'] = $operador;
    //     }

    //     echo json_encode($obj);
    //     exit;
    // }
}

if ($acao == 'show' && $parametro != '') {
    // ---------------------------------------
    // PEGA UM OPERADOR ESPECÍFICO
    // ---------------------------------------


    $db = DB::connect();
    $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.gerencia, usuarios.turma, usuarios.url_perfil, usuarios.url_status from usuarios where usuarios.matricula = ?");
    $sql->execute([$parametro]);
    $obj = $sql->fetchObject();

    if (!$obj) {
        $response = array(
            "error" => true,
            "message" => "Nenhum usuário encontrado!"
        );
        echo json_encode($response);
        exit;
    } else{
        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($response);
    }
    exit;
}
