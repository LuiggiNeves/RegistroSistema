<?php

// Adicionar log de início
error_log('Iniciando index.php na raiz');

// Carregar o autoload do Composer
require_once __DIR__ . '/vendor/autoload.php';
error_log('Autoload do Composer carregado');

// Carregar configurações globais
require_once __DIR__ . '/app/config/global.php';
error_log('Configurações globais carregadas');

// Outras inicializações podem ser feitas aqui
error_log('Inicializações adicionais feitas');

// Redirecionar todas as solicitações para o index.php do public
if (file_exists(__DIR__ . '/app/public/index.php')) {
    error_log('Arquivo app/public/index.php encontrado, redirecionando...');
    require_once __DIR__ . '/app/public/index.php';
} else {
    error_log('ERRO: Arquivo app/public/index.php não encontrado!');
    echo 'Erro: Arquivo app/public/index.php não encontrado!';
}
