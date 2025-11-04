<?php
# forçar utf8
header('Access-Control-Allow-Origin: *');
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/config/bootstrap.php';

const SERIES_TIPO = 'series';
const FILMES_TIPO = 'filmes';
const TV_TIPO = 'tv';

const PRINCIPAL_PRIORITY = [
    "GLOBO SP", "SBT NACIONAL", "RECORD SP", "BAND SP",
];

const FILMES_CATEGORY_ID = '2000'; // Substitua pelo ID real de Filmes

const CANAIS_24H_CATEGORY_ID = '2001'; // Substitua pelo ID real de Canais 24h

// Obter a URI atual
$uri = $_SERVER['REQUEST_URI'];

$rota->resolver($uri);
