
<?php
if ($acao == 'store' && $parametro == 'cadastrar') {
    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

   

    if (!array_key_exists('matricula', $dados)) {
        echo json_encode([
            "error" => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'matricula' está ausente."
        ]);
        exit;
    }

    if ($dados['matricula'] == "") {
        echo json_encode([
            "error" => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'matricula' não pode ser vazio."
        ]);
        exit;
    }

    if (!is_numeric($dados['matricula'])) {
        echo json_encode([
            'error' => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'matricula' deve ser um número."
        ]);
        exit;
    }


    $db = DB::connect();
    $sql = $db->prepare('SELECT * from autorizacao where matricula = ?');
    $sql->execute([$dados['matricula']]);

    if($sql->rowCount() > 0){
        echo json_encode([
            'error' => true, 
            'message' => 'Matrícula já cadastrada.'
        ]);
        exit; 
    }

    if (!array_key_exists('senha', $dados)) {
        echo json_encode([
            'error' => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'senha' está ausente."
        ]);
        exit;
    }

    if (strlen($dados['senha']) < 8) {
        echo json_encode([
            'error' => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'senha' deve ser igual ou maior a 8."
        ]);
        exit;
    }
    if (strlen($dados['senha']) > 16) {
        echo json_encode([
            'error' => true,
            "message" => "Erro ao cadastrar usuário. Parâmetro 'senha' deve ser igual ou menor a 16."
        ]);
        exit;
    }

    date_default_timezone_set("America/Sao_Paulo");
    $dataCriacao = date("Y-m-d");

    $responseGerarHash = Authorization::gerarHash($dados['senha']);

    // var_dump($responseGerarHash);
    // exit;
    if ($responseGerarHash['error']) {
        echo json_encode($responseGerarHash);
        exit;
    }

        // var_dump($responseGerarHash);
        // exit;

    // INICIA A TRANSAÇÃO
    $db->beginTransaction();
    try {

        $sql = $db->prepare("INSERT INTO autorizacao (matricula, senha, data_entrada) VALUES (?, ?, ?)");
        $sql->execute([$dados['matricula'], $responseGerarHash['data'], $dataCriacao]);
        $db->commit();

        echo json_encode([
            "error" => false,
            "message" => "Usuário cadastrado com sucesso!",
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        // Em caso de erro, reverte as alterações
        echo json_encode([
            "error" => true,
            "message" => "Erro ao cadastrar usuário. Erro: ". $e->getMessage(),
        ]);
    }

    exit;
}
