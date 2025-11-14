<?php
# forçar utf8
header('Access-Control-Allow-Origin: *');
header('Content-type: text/html; charset=utf-8');

// Permite requisições de qualquer origem. Para produção, é mais seguro especificar
// o domínio do seu app, ex: header("Access-Control-Allow-Origin: http://localhost:54528");
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache por 1 dia
}

// Trata a requisição "preflight" do tipo OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // Métodos permitidos
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        // Cabeçalhos permitidos
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

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
