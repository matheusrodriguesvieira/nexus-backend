<?php
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("O arquivo .env não foi encontrado.");
    }

    $lines = file($file);
    foreach ($lines as $line) {
        // Ignora linhas vazias e comentários
        if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
            continue;
        }

        // Divide a linha em chave e valor
        list($key, $value) = explode('=', trim($line), 2);
        $key = trim($key);
        $value = trim($value);

        // Define a variável de ambiente
        putenv("$key=$value");
    }
}

loadEnv(realpath( dirname(__FILE__) . '/../../.env'));
