<?php

namespace App\Controller\Api;

use App\Connection\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class AuthController {
    private $db;
    private $jwtSecret;

    public function __construct() {
        $this->db = Database::connect();
        $this->jwtSecret = getenv('JWT_SECRET') ?: 'sua_chave_secreta_aqui';
        error_log('AuthController inicializado com chave secreta: ' . $this->jwtSecret);
    }

    public function validarLogin($login, $senha, $id_empresa) {
        error_log("Iniciando validação de login para o usuário: $login, empresa: $id_empresa");
        $sql = "SELECT * FROM operadores WHERE login = :login AND id_empresa = :id_empresa";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['login' => $login, 'id_empresa' => $id_empresa]);
        $operador = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($operador) {
            error_log('Usuário encontrado no banco de dados: ' . print_r($operador, true));
            if (password_verify($senha, $operador['senha'])) {
                error_log('Senha verificada com sucesso. Gerando token JWT...');

                $jwtAlgo = 'HS256';
                $jwtIss = 'http://seusite.com';
                $jwtAud = 'http://seusite.com';
                $jwtExp = 3600;
    
                $payload = [
                    'iss' => $jwtIss,
                    'aud' => $jwtAud,
                    'sub' => $operador['id'],
                    'exp' => time() + $jwtExp,
                    'id_empresa' => $id_empresa,
                    'login' => $login
                ];
    
                $jwt = JWT::encode($payload, $this->jwtSecret, $jwtAlgo);
                error_log('Token JWT gerado com sucesso: ' . $jwt);
    
                return [
                    'status' => 'success',
                    'message' => 'Login bem-sucedido!',
                    'token' => $jwt
                ];
            } else {
                error_log('Senha incorreta para o usuário: ' . $login);
                return [
                    'status' => 'error',
                    'message' => 'Senha incorreta.'
                ];
            }
        } else {
            error_log('Usuário não encontrado para o login: ' . $login);
            return [
                'status' => 'error',
                'message' => 'Operador não encontrado para esta empresa.'
            ];
        }
    }
}

