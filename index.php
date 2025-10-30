<?php
// exibir todos os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constantes úteis (caminho raiz da aplicação)
define('APP_ROOT', __DIR__);

// 1. Carregar Autocarga e Funções
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

// Verifica se a URI atual NÃO está na lista de rotas públicas E
// se o usuário NÃO está autenticado
if (!ModeloAuth::estaAutenticado() && !in_array($uriFinal, ['/login', '/logar', '/sair'])) {
    // Se a rota não é pública e não está logado, redireciona para o login
    header("Location: $caminhoBase/login");
    exit;
}

// 2. Instanciar o Roteador
$roteador = new Roteador();

$roteador->adicionarRota('/login', 'Login', 'index');
$roteador->adicionarRota('/logar', 'Login', 'logar', 'POST');
$roteador->adicionarRota('/sair', 'Login', 'sair');

// 3. Definir Rotas (mantidas como estão, esperando a URI limpa)
// Rota Principal: /
$roteador->adicionarRota('', 'Login', 'index');
$roteador->adicionarRota('/home', 'Home', 'index');
// Rota de Categoria: /filmes, /series, /tv
$roteador->adicionarRota('/(\w+)', 'Home', 'categoria');
// Rota de Visualização: /assistir/{tipo}/{id}
$roteador->adicionarRota('/assistir/(\w+)/([\w\d]+)', 'Conteudo', 'assistir');
// Rota AJAX para salvar o progresso: /api/salvar-progresso
$roteador->adicionarRota('/api/salvar-progresso', 'Usuario', 'salvarProgresso', 'POST');

// 4. Despachar a Requisição
// Passamos a URI limpa e tratada para o roteador.
$roteador->despachar($uriFinal);
