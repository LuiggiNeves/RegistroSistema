<?php

// Definições globais e configurações
define('BASE_PATH', realpath(__DIR__ . '/../../')); // Vai 2 níveis acima de /config
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', APP_PATH . '/config');
define('CONNECTION_PATH', APP_PATH . '/connection'); // Definindo o caminho da pasta de conexão
error_log('Constantes globais definidas');

// Carregar o autoload do Composer
require BASE_PATH . '/vendor/autoload.php';
error_log('Autoload do Composer carregado no global.php');

// Carregar variáveis de ambiente usando vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();
error_log('Variáveis de ambiente carregadas com vlucas/phpdotenv');

// Definir a constante HOST_APP
define('HOST_APP', 'http://localhost/RTKSistema/');

error_log('Constante HOST_APP definida');

// Conectar ao banco de dados
require_once CONNECTION_PATH . '/DataBase.php';
error_log('Classe de conexão com o banco de dados incluída.');

// Funções utilitárias
function base_url($path = '') {
    return HOST_APP . ltrim($path, '/');
}

// Função para carregar configuração JWT

error_log('global.php carregado completamente');


