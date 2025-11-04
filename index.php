<?php
# forçar utf8
header('Access-Control-Allow-Origin: *');
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/config/bootstrap.php';

const SERIES_TIPO = 'series';
const FILMES_TIPO = 'filmes';
const TV_TIPO = 'tv';

// Obter a URI atual
$uri = $_SERVER['REQUEST_URI'];

$rota->resolver($uri);
