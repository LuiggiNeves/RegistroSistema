<?php

namespace App\Controller\Api;

use App\Connection\Database;
use PDO;

class AuthController {
    private $db;
    private $nomeCliente; // Propriedade para armazenar o nome do cliente

    public function __construct($nomeCliente) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Inicia a sessão apenas se ainda não foi iniciada
        }

        $this->nomeCliente = $nomeCliente; // Armazena o nome do cliente na propriedade

        // Log para iniciar a validação
        error_log("Iniciando validação do cliente: '{$nomeCliente}'");

        // Conectar ao banco de dados central para validar o cliente e recuperar credenciais
        $centralDb = Database::connectCentralDB();
        if (!$centralDb) {
            error_log("Falha ao conectar ao banco de dados central.");
            die('Erro ao conectar ao banco de dados central.');
        }

        error_log("Conectado ao banco de dados central para verificar o cliente: '{$nomeCliente}'");

        // Usar LOWER para garantir que a comparação seja insensível a maiúsculas/minúsculas
        $sql = "SELECT * FROM clientes WHERE LOWER(nome) = LOWER(:nome) AND situacao_id = 1";
        $stmt = $centralDb->prepare($sql);
        $stmt->execute(['nome' => $nomeCliente]);

        // Verificar se a consulta retorna algum resultado
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Resultado da consulta SQL: " . print_r($cliente, true));

        if ($cliente) {
            error_log("Cliente encontrado no banco de dados central: " . print_r($cliente, true));

            // Cliente é válido, conectar ao banco de dados específico do cliente usando as credenciais
            $databaseName = $cliente['database_name'];  // Certifique-se de que esta coluna está correta
            $dbUser = $cliente['db_user'];              // Nome de usuário para o banco de dados do cliente
            $dbPassword = $cliente['db_password'];      // Senha para o banco de dados do cliente

            error_log("Tentando conectar ao banco de dados do cliente: {$databaseName} com usuário: {$dbUser}");

            try {
                $this->db = Database::connectToClientDB($databaseName, $dbUser, $dbPassword);
                error_log("Conexão com o banco de dados do cliente estabelecida com sucesso.");
            } catch (\PDOException $e) {
                error_log("Erro ao conectar ao banco de dados do cliente: " . $e->getMessage());
                die('Erro ao conectar ao banco de dados do cliente.');
            }
        } else {
            error_log("Cliente '{$nomeCliente}' não encontrado ou inativo.");
            die('Cliente não encontrado ou inativo.');
        }
    }

    public function validarLogin($login, $senha) {
        error_log("Iniciando validação de login para o usuário: $login");

        // A validação do operador agora é feita no banco de dados específico do cliente
        $sql = "SELECT * FROM operadores WHERE login = :login";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['login' => $login]);
        $operador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($operador) {
            error_log('Usuário encontrado no banco de dados: ' . print_r($operador, true));
            if (password_verify($senha, $operador['senha'])) {
                error_log('Senha verificada com sucesso.');

                // Definir variáveis de sessão para manter o estado de autenticação
                $_SESSION['usuario_id'] = $operador['id'];
                $_SESSION['login'] = $login;
                $_SESSION['nomeCliente'] = $this->nomeCliente; // Adiciona o nome do cliente à sessão

                return [
                    'status' => 'success',
                    'message' => 'Login bem-sucedido!',
                    'redirect' => "/RTKSistema/{$this->nomeCliente}/dashboard"  // Caminho completo para redirecionamento
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
                'message' => 'Operador não encontrado.'
            ];
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /RTKSistema/login');
        exit;
    }
}
