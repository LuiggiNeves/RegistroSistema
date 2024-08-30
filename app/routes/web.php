<?php
use Core\Routing\Router;
use App\Controller\Api\ClienteApiController;

$clienteApiController = new ClienteApiController();

// Obter todos os clientes do banco de dados
$clientes = $clienteApiController->getClientes();

// Criar rotas dinamicamente para cada cliente baseado no nome carregado do banco de dados
foreach ($clientes as $cliente) {
    $nomeCliente = $cliente['nome'];
    $idEmpresa = $cliente['id'];

    Router::add('GET', "/{$nomeCliente}/login", function () use ($clienteApiController, $nomeCliente, $idEmpresa) {
        $cliente = $clienteApiController->validarCliente($nomeCliente);
        if ($cliente) {
            $logoBase64 = $clienteApiController->getImagemBase64($cliente['ico_img']);
            require_once __DIR__ . "../../public/login.php";
        }
    });

    // Remover a verificação de autenticação por middleware
    Router::add('GET', "/{$nomeCliente}/dashboard", function () use ($clienteApiController, $nomeCliente) {
        $cliente = $clienteApiController->validarCliente($nomeCliente);
        if ($cliente) {
            require_once __DIR__ . "../../public/Dashboard.php";
        }
    });

    Router::add('GET', "/{$nomeCliente}/backoffice", function () use ($clienteApiController, $nomeCliente) {
        $cliente = $clienteApiController->validarCliente($nomeCliente);
        if ($cliente) {
            require_once __DIR__ . "../../public/Backoffice.php";
        }
    });
}

// Outras rotas existentes
Router::add('GET', '/', function () {
    echo '<h1>Bem-vindo ao site!</h1>';
});

Router::add('GET', '/sobre', function () {
    echo '<h1>Sobre Nós</h1>';
});
