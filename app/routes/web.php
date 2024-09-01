<?php

use App\Connection\Database;
use Core\Routing\Router;
use App\Controller\Api\AuthController;
use App\Middleware\AuthMiddleware;
use App\Controller\Api\ClienteApiController;

// Instância do middleware de autenticação
$authMiddleware = new AuthMiddleware();

// Instância do controlador de clientes para acessar informações do banco central
$clienteApiController = new ClienteApiController('central_db');

// Obter todos os clientes do banco de dados central
$clientes = $clienteApiController->getClientes();

if (!$clientes) {
    error_log("Nenhum cliente encontrado no banco de dados central.");
    die('Nenhum cliente encontrado.');
}

// Criar rotas dinamicamente para cada cliente baseado no nome carregado do banco de dados
foreach ($clientes as $cliente) {
    $nomeCliente = $cliente['nome'];
    $idEmpresa = $cliente['id']; // Obter o ID da empresa do cliente
    error_log("Registrando rotas para cliente: {$nomeCliente}");

    // Rota para página de login do cliente
    Router::add('GET', "/{$nomeCliente}/login", function () use ($nomeCliente, $idEmpresa) {
        $clienteApiController = new ClienteApiController($nomeCliente);  // Conecta ao banco de dados específico do cliente

        // Validar cliente e obter dados adicionais
        $cliente = $clienteApiController->validarCliente($nomeCliente);

        // Obter a imagem em base64
        $logoBase64 = $cliente['logo_base64'] ?? null;

        // Passar os dados para o template
        $GLOBALS['logoBase64'] = $logoBase64;
        $GLOBALS['cliente'] = $cliente;
        $GLOBALS['idEmpresa'] = $idEmpresa;

        include __DIR__ . "../../public/login.php";
    });

    // Rota para processar o login via POST
    Router::add('POST', "/{$nomeCliente}/login", function () use ($nomeCliente, $idEmpresa) {
        $authController = new AuthController($nomeCliente);
        $login = $_POST['nameEnter'];
        $senha = $_POST['passEnter'];

        $resultado = $authController->validarLogin($login, $senha);

        // Verificar o resultado e redirecionar conforme necessário
        if ($resultado['status'] === 'success') {
            header('Location: ' . "/RTKSistema/{$nomeCliente}/dashboard");  // Redirecionar para o dashboard com caminho completo
            exit;
        } else {
            echo json_encode($resultado);
        }
    });

    // Rota protegida para o dashboard do cliente
    Router::add('GET', "/{$nomeCliente}/dashboard", function () use ($nomeCliente, $authMiddleware) {
        $authMiddleware->verificarAutenticacao();  // Verifica se o usuário está autenticado
        $clienteApiController = new ClienteApiController($nomeCliente);  // Conecta ao banco de dados específico do cliente
    
        $cliente = $clienteApiController->validarCliente($nomeCliente);
        if ($cliente) {
            require_once __DIR__ . "../../public/Dashboard.php";
        } else {
            error_log("Cliente {$nomeCliente} não encontrado.");
            die('Cliente não encontrado.');
        }
    });

    // Outras rotas para o cliente, como backoffice, etc.
}

// Rota para a página inicial
Router::add('GET', '/', function () {
    echo '<h1>Bem-vindo ao site!</h1>';
});

// Rota para a página "Sobre"
Router::add('GET', '/sobre', function () {
    echo '<h1>Sobre Nós</h1>';
});
?>
