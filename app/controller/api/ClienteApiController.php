<?php

namespace App\Controller\Api;

use App\Connection\Database;
use PDO;

class ClienteApiController {
    private $db;

    public function __construct($nomeCliente = null) {
        // Conectar ao banco de dados central para validar o cliente
        $centralDb = Database::connectCentralDB();
        if (!$centralDb) {
            error_log("Erro ao conectar ao banco de dados central.");
            die('Erro ao conectar ao banco de dados central.');
        }

        $this->db = $centralDb; // Conecta ao banco central

        if ($nomeCliente) {
            error_log("ClienteApiController inicializado com banco central para cliente: {$nomeCliente}");
        }
    }

    public function getClientes() {
        // Obter todos os clientes do banco de dados central
        $sql = "SELECT * FROM clientes";
        $stmt = $this->db->query($sql);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Clientes obtidos: " . print_r($clientes, true));
        return $clientes;
    }

    public function validarCliente($nomeCliente) {
        $sql = "SELECT * FROM clientes WHERE LOWER(nome) = LOWER(:nome) AND situacao_id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nome' => $nomeCliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Validação de cliente para {$nomeCliente}: " . print_r($cliente, true));

        if ($cliente) {
            if (!empty($cliente['ico_img'])) {
                $cliente['logo_base64'] = $this->getImagemBase64($cliente['ico_img']);
            } else {
                error_log("Imagem de logotipo não encontrada para cliente: {$nomeCliente}");
            }
        }
        return $cliente;
    }

    public function getClienteById($idCliente) {
        $sql = "SELECT * FROM clientes WHERE id = :idCliente AND situacao_id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['idCliente' => $idCliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Cliente encontrado: " . print_r($cliente, true));
        return $cliente;
    }
    

    public function getImagemBase64($blob) {
        if (!empty($blob)) {
            return 'data:image/png;base64,' . base64_encode($blob);
        } else {
            error_log("Erro ao converter imagem BLOB para base64.");
            return null;
        }
    }
}
?>
