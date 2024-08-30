<?php

namespace App\Controller\Api;

use App\Connection\Database;

class ClienteApiController {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Carrega todos os clientes do banco de dados
    public function getClientes() {
        $sql = "SELECT c.*, s.descricao AS situacao_descricao 
                FROM clientes c 
                JOIN situacoes s ON c.situacao_id = s.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Valida o cliente e verifica a situação
    public function validarCliente($nomeCliente) {
        $sql = "SELECT c.*, s.descricao AS situacao_descricao 
                FROM clientes c 
                JOIN situacoes s ON c.situacao_id = s.id 
                WHERE c.nome = :nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nome' => $nomeCliente]);
        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($cliente) {
            if ($cliente['situacao_descricao'] === 'ativo') { // Verificar se a situação é 'ativo'
                return $cliente; // Retorna o cliente se estiver ativo
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Cliente não está ativo.'
                ]);
                return false;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Cliente não encontrado.'
            ]);
            return false;
        }
    }
    
    // Método para obter a imagem em base64
    public function getImagemBase64($blob)
    {
        return 'data:image/png;base64,' . base64_encode($blob);
    }
}
