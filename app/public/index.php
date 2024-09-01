<?php

// Inicializar o ambiente
require_once __DIR__ . '/../config/global.php';
error_log('Ambiente inicializado no app/public/index.php');

// Inicializar o sistema de roteamento
require_once APP_PATH . '/core/routing/Router.php';
error_log('Sistema de roteamento inicializado');

// Carregar as rotas da Web e da API

require_once APP_PATH . '/routes/web.php';
require_once APP_PATH . '/routes/api.php';
error_log('Rotas carregadas');

// Despachar a rota correspondente
Core\Routing\Router::dispatch();
error_log('Rota despachada');
