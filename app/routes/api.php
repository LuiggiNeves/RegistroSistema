<?php
use Core\Routing\Router;
use App\Controller\Api\AuthController;

// Rota para validar login para cada cliente usando o nome do cliente (API)
Router::add('POST', '/api/{nomeCliente}/login', function ($nomeCliente) {
    // Iniciar o buffer de saída para capturar qualquer saída inesperada
    ob_start();

    $authController = new AuthController($nomeCliente);  // Conectar ao banco de dados específico do cliente
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $response = $authController->validarLogin($login, $senha);

    // Limpar qualquer saída existente no buffer antes de retornar a resposta JSON
    ob_end_clean();

    // Definir o cabeçalho para JSON e retornar a resposta
    header('Content-Type: application/json');
    echo json_encode($response);
    error_log('Resposta de login enviada: ' . json_encode($response));
    exit;
});

// Rota para verificar a sessão do usuário
Router::add('GET', '/api/verificarSessao', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['usuario_id'])) {
        echo json_encode(['status' => 'success', 'message' => 'Sessão válida']);
    } else {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida ou expirada']);
    }
    exit;
});

Router::add('GET', '/api/teste', function () {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Rota de teste funcionando!']);
    exit;
});
?>
