<?php

class Authorization
{
    public static function login($matricula, $senha)
    {

        $secretJWT = $GLOBALS['secretJWT'];

        $db = DB::connect();
        // $rs = $db->prepare("SELECT usuarios.nome, usuarios.matricula, usuarios.senha FROM usuarios where usuarios.matricula = ?");
        $rs = $db->prepare("SELECT * FROM autorizacao where matricula = ?");
        $exec = $rs->execute([$matricula]);
        $obj = $rs->fetchObject();
        $rows = $rs->rowCount();

        $validUsername = false;
        $validPassword = false;
        if ($rows > 0) {
            $hash          = $obj->senha;
            $validUsername = true;
            // $validPassword = password_verify($senha, $passDB) ? true : false;
            $resposeVerificarHash = self::verificarHash($senha, $hash);
            $validPassword = !$resposeVerificarHash['error'];
            
        } else {
            return [
                'error' => true,
                'message' => 'Invalid user name or password.'
            ];
            exit;
        }
        // if ($rows > 0) {
        //     $idDB          = $obj->matricula;
        //     $nameDB        = $obj->nome;
        //     $passDB        = $obj->senha;
        //     $validUsername = true;
        //     // $validPassword = password_verify($senha, $passDB) ? true : false;
        //     $validPassword = $passDB == $senha ? true : false;
        // } else {
        //     return [
        //         'error' => true,
        //         'message' => 'Invalid user name or password.'
        //     ];
        //     exit;
        // }

        // echo json_encode($resposeVerificarHash);
        // exit;

        if ($validUsername and $validPassword) {
            $sql = $db->prepare("SELECT * from usuarios where matricula = ?");
            $sql->execute([$matricula]);
            $obj2 = $sql->fetch(PDO::FETCH_ASSOC);



            //$nextWeek = time() + (7 * 24 * 60 * 60);
            $expire_in = time() + 3600;
            $token     = JWT::encode([
                'id'         => $obj2['matricula'],
                'name'       => $obj2['nome'],
                'expires_in' => $expire_in,
            ], $GLOBALS['secretJWT']);

            $sql = $db->prepare("UPDATE autorizacao SET token = ? WHERE matricula = ?");
            $sql->execute([$token, $matricula]);
            

            $permissoes = array_slice($obj2, 4);

            return [
                'error' => false,
                'message' => "Usuário logado",
                'token' => $token,
                'data' => [
                    "matricula"     =>  $obj2['matricula'],
                    "nome"          =>  $obj2['nome'],
                    "turma"         =>  $obj2['turma'],
                    "email"         =>  $obj2['email'],
                    "url_perfil"    =>  $obj2['url_perfil'],
                    "url_status"    =>  $obj2['url_status'],
                    "gerencia"      =>  $obj2['gerencia'],
                    "permissoes"    => $permissoes
                ]
            ];
        } else if (!$validPassword) {
            return [
                'error' => true,
                'message' => 'Invalid user name or password.'
            ];
        }
    }

    public static function logout($matricula){
        $headers = getallheaders();
        if (!isset($headers['authorization'])) {
            return [
                "error" => true,
                "message" => "Header authorization está ausente!"
            ];
        }
        
        $token = $headers['authorization'];

        $db = DB::connect();

        $sql = $db->prepare('SELECT * FROM autorizacao WHERE token = ? and matricula = ?');
        $sql->execute([$token, $matricula]);
        $rows = $sql->rowCount();

        if ($rows > 0) {
            $sql = $db->prepare("UPDATE autorizacao SET token = '' WHERE matricula = ?");
            $sql->execute([$matricula]);
            return [
                "error" => false,
                "message" => "Logout realizado com sucesso!"
            ];
            exit;
        } else {
            return [
                "error" => true,
                "message" => "Usuário não está logado ou o token é inválido!"
            ];
        }
    }

    // VERIFICA SE O TOKEN É VÁLIDO, CASO CONTRÁRIO, APAGA O TOKEN EXISTENTE
    public static function validarToken($matricula)
    {
        $headers = getallheaders();
        if (!isset($headers['authorization'])) {
            return [
                "error" => true,
                "message" => "Header authorization está ausente!"
            ];
        }
        
        $token = $headers['authorization'];


        $db   = DB::connect();
        $rs   = $db->prepare("SELECT * FROM autorizacao WHERE token = ? and matricula = ?");
        $exec = $rs->execute([$token, $matricula]);
        $obj  = $rs->fetchObject();
        $rows = $rs->rowCount();
        $secretJWT = $GLOBALS['secretJWT'];

        
        if ($rows > 0) {
            $tokenDB = $obj->token;

            $decodedJWT = JWT::decode($tokenDB, $secretJWT);
            if ($decodedJWT->expires_in > time()) {
                return [
                    "error" => false,
                    "message" => "Usuário autenticado."
                ];
            } else {
                $sql = $db->prepare("UPDATE autorizacao SET token = '' WHERE matricula = ?");
                $sql->execute([$matricula]);
                return [
                    "error" => true,
                    "message" => "Login expirado!"
                ];
            }
        } else {
            return [
                "error" => true,
                "message" => "Usuário não está logado ou o token é inválido!"
            ];
        }
    }

    public static function autorizar($acao, $matricula)
    {
        $db = DB::connect();
        $sql = $db->prepare("SELECT * FROM autorizacao WHERE matricula = ?");
        $sql->execute([$matricula]);
        $obj = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$obj) {
            return [
                'error' => true,
                'message' => 'Usuário não encontrado.'
            ];
        } else if($obj[$acao]){
            return [
                'error' => false,
                'message' => 'Usuário autorizado',
            ];
        } 
        else {
            return [
                'error' => true,
                'message' => 'Usuário não autorizado.'
            ];
        }
    }

    public static function gerarHash($senha) {
        try {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
        
            if ($hash === false) {
                throw new Exception("Falha ao gerar o hash da senha.");
            }

            return [
                'error' => false,
                'message' => "Hash gerado com sucesso!",
                "data" => $hash
            ];
        
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => "Erro: " . $e->getMessage(),
            ];
        }
    }

    public static function verificarHash($senha, $hash) {
        if (password_verify($senha, $hash)) {
            return [
                'error' => false,
                'message' => "Senha correta!",
            ];
        } else {
            return [
                'error' => true,
                'message' => "Senha incorreta ou hash inválido.",
            ];
        }
    }
}
