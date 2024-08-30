<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $jwtSecret;
    private $jwtAlgo;
    private $jwtIss;
    private $jwtAud;
    private $jwtExp;

    public function __construct() {
        $this->jwtSecret = getenv('JWT_SECRET') ?: 'sua_chave_secreta_aqui';
        $this->jwtAlgo = 'HS256';
        $this->jwtIss = 'http://seusite.com';
        $this->jwtAud = 'http://seusite.com';
        $this->jwtExp = 3600;
        error_log('AuthMiddleware inicializado.');
    }

    public function verificarAutenticacao() {
        error_log('Iniciando verificação de autenticação...');

        $headers = getallheaders();
        error_log('Cabeçalhos recebidos: ' . print_r($headers, true));

        if (isset($headers['Authorization'])) {
            $jwt = str_replace('Bearer ', '', $headers['Authorization']);
            error_log('Token JWT recebido: ' . $jwt);

            try {
                error_log('Tentando decodificar o token JWT...');
                $decoded = JWT::decode($jwt, new Key($this->jwtSecret, $this->jwtAlgo));
                error_log('Token JWT decodificado com sucesso: ' . print_r($decoded, true));

                if ($decoded->iss !== $this->jwtIss) {
                    error_log('Falha na validação do emissor (iss). Esperado: ' . $this->jwtIss . ', Recebido: ' . $decoded->iss);
                    throw new \Exception('Token inválido: emissor incorreto.');
                }

                if ($decoded->aud !== $this->jwtAud) {
                    error_log('Falha na validação do público (aud). Esperado: ' . $this->jwtAud . ', Recebido: ' . $decoded->aud);
                    throw new \Exception('Token inválido: público incorreto.');
                }

                if ($decoded->exp < time()) {
                    error_log('Token expirado. Expiração: ' . date('Y-m-d H:i:s', $decoded->exp) . ', Agora: ' . date('Y-m-d H:i:s'));
                    throw new \Exception('Token expirado.');
                }

                error_log('Validação do token JWT concluída com sucesso.');
                return $decoded;
            } catch (\Exception $e) {
                error_log('Erro ao decodificar ou validar o token JWT: ' . $e->getMessage());
            }
        } else {
            error_log('Cabeçalho Authorization não encontrado ou malformado');
        }

        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['status' => 'error', 'message' => 'Acesso nao autorizado']);
        exit;
    }
}
