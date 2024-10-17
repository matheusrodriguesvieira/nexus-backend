<?php

class Usuarios
{
    public static function login($login, $senha)
    {

        $secretJWT = $GLOBALS['secretJWT'];

        $db = DB::connect();
        $rs = $db->prepare("SELECT usuarios.nome, usuarios.matricula, autenticacao.senha, usuarios.idgerencia FROM usuarios, autenticacao WHERE usuarios.matricula = autenticacao.matricula and  usuarios.matricula = ?");
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

            $sql = $db->prepare("UPDATE autenticacao SET token = ? WHERE matricula = ?");
            $sql->execute([$token, $idDB]);
            return [
                'error' => false,
                'token' => $token,
                'data' => [
                    "matricula" =>  $obj->matricula,
                    "nome"      =>  $obj->nome,
                    "gerencia"  =>  $obj->idgerencia
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
        if (isset($headers['authorization'])) {
            $token = $headers['authorization'];
        } else {
            return false;
        }



        $db   = DB::connect();
        $rs   = $db->prepare("SELECT * FROM autenticacao WHERE token = ? and matricula = ?");
        $exec = $rs->execute([$token, $login]);
        $obj  = $rs->fetchObject();
        $rows = $rs->rowCount();
        $secretJWT = $GLOBALS['secretJWT'];

        if ($rows > 0) {
            $tokenDB = $obj->token;

            $decodedJWT = JWT::decode($tokenDB, $secretJWT);
            if ($decodedJWT->expires_in > time()) {
                return true;
            } else {
                $sql = $db->prepare("UPDATE autenticacao SET token = '' WHERE matricula = ?");
                $sql->execute([$login]);
                return false;
            }
        } else {
            return false;
        }
    }

    public static function autorizar($funcao, $login)
    {
        // $headers = getallheaders();
        // if (isset($headers['authorization'])) {
        //     $token = $headers['authorization'];
        // } else {
        //     return false;
        // }
        // $secretJWT = $GLOBALS['secretJWT'];
        // $decodedJWT = JWT::decode($token, $secretJWT);

        if ($funcao == 'supervisores') {
            $db = DB::connect();
            $sql = $db->prepare("SELECT * FROM autorizacao WHERE matricula = ? and gerarescala = true");
            $sql->execute([$login]);
            $obj = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$obj) {
                return false;
            } else {
                return true;
            }
        } else if ($funcao == 'operadores') {
            $db = DB::connect();
            $sql = $db->prepare("SELECT * FROM autorizacao WHERE matricula = ? and visualizarescala = true");
            $sql->execute([$login]);
            $obj = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$obj) {
                return false;
            } else {
                return true;
            }
        }
    }
}
