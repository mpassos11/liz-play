<?php
// exibir todos os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constantes �teis (caminho raiz da aplica��o)
define('APP_ROOT', __DIR__);

// 1. Carregar Autocarga e Fun��es
require_once APP_ROOT . '/core/Autocarga.php';
require_once APP_ROOT . '/core/FuncoesGerais.php';
require_once APP_ROOT . '/core/FuncoesConfig.php';

carregarEnv(APP_ROOT . '/.env');

const USUARIO_LOGADO_ID = '12345';

// Pega a URI completa solicitada pelo cliente
$uriCompleta = $_SERVER['REQUEST_URI'];
// Pega o caminho do script atual (ex: /liz-play/public/index.php)
$caminhoScript = $_SERVER['SCRIPT_NAME'];

$caminhoBase = baseUrl();

// Garante que a URI limpa sempre comece com uma barra, ou seja apenas '/' se estiver na raiz.
$uriFinal = isset($_GET['url']) ? "/{$_GET['url']}" : '';

require_once APP_ROOT . '/core/Rotas.php';

// Verifica se a URI atual N�O est� na lista de rotas p�blicas E
// se o usu�rio N�O est� autenticado
if (!ModeloAuth::estaAutenticado() && !in_array($uriFinal, ['/login', '/logar', '/sair'])) {
    // Se a rota n�o � p�blica e n�o est� logado, redireciona para o login
    echo "<script>window.location.href = $caminhoBase/login</script>";
    exit;
}
