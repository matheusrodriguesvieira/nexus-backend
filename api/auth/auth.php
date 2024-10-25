<?php

class Usuarios
{
    public static function login($login, $senha)
    {

        $secretJWT = $GLOBALS['secretJWT'];

        $db = DB::connect();
        // $rs = $db->prepare("SELECT usuarios.nome, usuarios.matricula, usuarios.senha FROM usuarios where usuarios.matricula = ?");
        $rs = $db->prepare("SELECT * FROM usuarios where usuarios.matricula = ?");
        $exec = $rs->execute([$login]);
        $obj = $rs->fetchObject();
        $rows = $rs->rowCount();

        if ($rows > 0) {
            $idDB          = $obj->matricula;
            $nameDB        = $obj->nome;
            $passDB        = $obj->senha;
            $validUsername = true;
            // $validPassword = password_verify($senha, $passDB) ? true : false;
            $validPassword = $passDB == $senha ? true : false;
        } else {
            $validUsername = false;
            $validPassword = false;
        }
        

        if ($validUsername and $validPassword) {
            //$nextWeek = time() + (7 * 24 * 60 * 60);
            $expire_in = time() + 3600;
            $token     = JWT::encode([
                'id'         => $idDB,
                'name'       => $nameDB,
                'expires_in' => $expire_in,
            ], $GLOBALS['secretJWT']);

            // var_dump($token);
            // exit;

            $sql = $db->prepare("UPDATE usuarios SET token = ? WHERE matricula = ?");
            $sql->execute([$token, $idDB]);
            $sql = $db->prepare("SELECT * from autorizacao where matricula = ?");
            $sql->execute([$idDB]);
            $obj2 = $sql->fetch(PDO::FETCH_ASSOC);

            $permissoes = array_slice($obj2, 1);
            return [
                'error' => false,
                'message' => "Usuário logado",
                'token' => $token,
                'data' => [
                    "matricula"     =>  $obj->matricula,
                    "nome"          =>  $obj->nome,
                    "turma"         =>  $obj->turma,
                    "email"         =>  $obj->email,
                    "url_perfil"    =>  $obj->url_perfil,
                    "url_status"    =>  $obj->url_status,
                    "gerencia"      =>  $obj->gerencia,
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

    // VERIFICA SE O TOKEN É VÁLIDO, CASO CONTRÁRIO, APAGA O TOKEN EXISTENTE
    public static function validarToken($login)
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
        $rs   = $db->prepare("SELECT * FROM usuarios WHERE token = ? and matricula = ?");
        $exec = $rs->execute([$token, $login]);
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
                $sql = $db->prepare("UPDATE usuarios SET token = '' WHERE matricula = ?");
                $sql->execute([$login]);
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

    public static function autorizar($acao, $login)
    {
        $db = DB::connect();
        $sql = $db->prepare("SELECT * FROM autorizacao WHERE matricula = ?");
        $sql->execute([$login]);
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
}
