<?php
# forçar utf8
header('Access-Control-Allow-Origin: *');
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/config/bootstrap.php';

// Obter a URI atual
$uri = $_SERVER['REQUEST_URI'];

$rota->resolver($uri);
