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
        } 

        for($i = 0; $i < count($obj); $i++) {
            $obj[$i]['ocorrenciasPorMes'] = [];

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
            $sql->execute([$obj[$i]['matricula']]);
            $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($obj2 as $resultado){
                $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
            }
        
            $obj[$i]['ocorrenciasPorMes'] = $meses;
        }
// var_dump($obj);
        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($response);

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
        } 

        for($i = 0; $i < count($obj); $i++) {
            $obj[$i]['ocorrenciasPorMes'] = [];

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
            $sql->execute([$obj[$i]['matricula']]);
            $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($obj2 as $resultado){
                $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
            }
        
            $obj[$i]['ocorrenciasPorMes'] = $meses;
        }

        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($response);

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
        } 

        for($i = 0; $i < count($obj); $i++) {
            $obj[$i]['ocorrenciasPorMes'] = [];

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
            $sql->execute([$obj[$i]['matricula']]);
            $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($obj2 as $resultado){
                $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
            }
        
            $obj[$i]['ocorrenciasPorMes'] = $meses;
        }

        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($obj);
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
        }

        for($i = 0; $i < count($obj); $i++) {
            $obj[$i]['ocorrenciasPorMes'] = [];

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
            $sql->execute([$obj[$i]['matricula']]);
            $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($obj2 as $resultado){
                $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
            }
        
            $obj[$i]['ocorrenciasPorMes'] = $meses;
        }

        $response = array(
            "error" => false,
            "message" => $obj
        );
        echo json_encode($obj);
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
    $obj = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$obj) {
        $response = array(
            "error" => true,
            "message" => "Nenhum usuário encontrado!"
        );
        echo json_encode($response);
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
    $sql->execute([$parametro]);
    $obj2 = $sql->fetchAll(PDO::FETCH_ASSOC);

    foreach ($obj2 as $resultado){
        $meses[$resultado['mes']] = $resultado['total_ocorrencias'];
    }

    $obj['ocorrenciasPorMes'] = $meses;
    $response = array(
        "error" => false,
        "message" => $obj
    );
    echo json_encode($response);

    exit;
}
