<?php

namespace App\Middleware;

class AuthMiddleware {
    public function verificarAutenticacao() {
        // Verifica se uma sessão já está ativa antes de chamar session_start()
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Inicia a sessão se ainda não foi iniciada
        }

        // Verificar se o usuário está autenticado
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nomeCliente'])) {
            // Usuário não autenticado ou nomeCliente não definido, redirecionar para a página de login
            header('Location: /RTKSistema/login'); // Ajuste o caminho conforme necessário
            exit;
        }

        // Obter o nome do cliente diretamente da URL
        $uriSegments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')); 
        $nomeClienteNaURL = isset($uriSegments[1]) ? $uriSegments[1] : ''; // Seguindo o padrão da URL para obter o nome do cliente corretamente

        // Verificar se o nome do cliente na sessão corresponde ao nome do cliente na URL
        if ($_SESSION['nomeCliente'] !== $nomeClienteNaURL) {
            // Nome do cliente na sessão não corresponde ao da URL, redirecionar para o login do cliente correto
            header('Location: /RTKSistema/' . $_SESSION['nomeCliente'] . '/login');
            exit;
        }
    }
}
?>
