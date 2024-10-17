<?php
if ($acao == 'index' && $parametro == '') {

    // ---------------------------------------
    // PEGA TODOS OS usuários
    // ---------------------------------------


    if (!empty($_GET['turma']) && !empty($_GET['gerencia'])) {

        $turma = $_GET['turma'];
        $gerencia = $_GET['gerencia'];

        $db = DB::connect();

        // $sql = $db->prepare("SELECT * from operadores where operadores.matricula > 5");
        // $sql->execute();
        // $sql = $db->prepare("SELECT operadores.matricula, usuarios.nome, operadores.disponivel, usuarios.matriculasupervisor from operadores, usuarios");
        $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.gerencia, usuarios.turma, usuarios.url_perfil, usuarios.url_status from usuarios where usuarios.turma ilike ? and usuarios.gerencia ilike ?");
        $sql->execute([$turma, $gerencia]);
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

    if (empty($_GET['gerencia'])) {

        $turma = $_GET['turma'];

        $db = DB::connect();
        $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.gerencia, usuarios.turma, usuarios.url_perfil, usuarios.url_status from usuarios where usuarios.turma ilike ?");
        $sql->execute([$turma]);
        $obj = $sql->fetchAll(PDO::FETCH_ASSOC);


        if (!$obj) {
            $response = array(
                "error" => true,
                "message" => "Nenhum usuário encontrado!"
            );
            echo json_encode($response);
        } else{
            $response = array(
                "error" => false,
                "message" => $obj
            );
            echo json_encode($obj);
        }
        exit;
    }

    if (empty($_GET['turma'])) {

        $gerencia = $_GET['gerencia'];

        $db = DB::connect();
        $sql = $db->prepare("SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.gerencia, usuarios.turma, usuarios.url_perfil, usuarios.url_status from usuarios where usuarios.gerencia ilike ?");
        $sql->execute([$gerencia]);
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
            echo json_encode($obj);
        }
        exit;
    }
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
