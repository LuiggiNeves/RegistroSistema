<?php
use Core\Routing\Router;
use App\Controller\Api\AuthController;
use App\Middleware\AuthMiddleware;

$authController = new AuthController();
$authMiddleware = new AuthMiddleware();

// Rota para validar login
Router::add('POST', '/api/login', function () use ($authController) {
    error_log('Rota /api/login acessada');
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $id_empresa = $_POST['id_empresa'] ?? '';

    error_log('Dados de login recebidos: ' . print_r(['login' => $login, 'id_empresa' => $id_empresa], true));

    $response = $authController->validarLogin($login, $senha, $id_empresa);

    header('Content-Type: application/json');
    echo json_encode($response);
    error_log('Resposta de login enviada: ' . json_encode($response));
});

// Exemplo de rota protegida
Router::add('GET', "/{nomeCliente}/dashboard", function () use ($authMiddleware, $clienteApiController, $nomeCliente) {
    error_log("Rota protegida /{$nomeCliente}/dashboard acessada");
    $authMiddleware->verificarAutenticacao();
    $cliente = $clienteApiController->validarCliente($nomeCliente);
    if ($cliente) {
        require_once __DIR__ . "../../public/Dashboard.php";
    } else {
        error_log("Cliente {$nomeCliente} não encontrado.");
    }
});
