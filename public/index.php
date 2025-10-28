<?php
// Define constantes úteis (caminho raiz da aplicação)
define('APP_ROOT', dirname(__DIR__));

// 1. Carregar Autocarga e Funções
require_once APP_ROOT . '/core/Autocarga.php';
require_once APP_ROOT . '/core/FuncoesGerais.php';
require_once APP_ROOT . '/core/FuncoesConfig.php';

carregarEnv(APP_ROOT . '/.env');

const USUARIO_LOGADO_ID = '12345';

// --- NOVO TRATAMENTO DE URI E CAMINHO BASE ---

// Pega a URI completa solicitada pelo cliente
$uriCompleta = $_SERVER['REQUEST_URI'];
// Pega o caminho do script atual (ex: /liz-play/public/index.php)
$caminhoScript = $_SERVER['SCRIPT_NAME'];

// Calcula o caminho base que deve ser removido
// Exemplo: /liz-play/public/
$caminhoBase = str_replace(basename($caminhoScript), '', $caminhoScript);

// Remove o caminho base da URI e normaliza, garantindo que a raiz seja sempre '/'
// $uriCompleta: /liz-play/public/assistir/filme/123?q=teste
// $caminhoBase: /liz-play/public/
// Resultado: /assistir/filme/123?q=teste
$uriTratada = substr($uriCompleta, strlen($caminhoBase));

// Remove a query string para deixar a URI limpa para o roteador
$uriLimpa = strtok($uriTratada, '?');

// Garante que a URI limpa sempre comece com uma barra, ou seja apenas '/' se estiver na raiz.
$uriFinal = ($uriLimpa === false || $uriLimpa === '') ? '/' : '/' . ltrim($uriLimpa, '/');

// --- FIM DO NOVO TRATAMENTO ---

// 2. Instanciar o Roteador
$roteador = new Roteador();

// 3. Definir Rotas (mantidas como estão, esperando a URI limpa)
// Rota Principal: /
$roteador->adicionarRota('/', 'Home', 'index');
// Rota de Categoria: /filmes, /series, /tv
$roteador->adicionarRota('/(\w+)', 'Home', 'categoria');
// Rota de Visualização: /assistir/{tipo}/{id}
$roteador->adicionarRota('/assistir/(\w+)/([\w\d]+)', 'Conteudo', 'assistir');
// Rota AJAX para salvar o progresso: /api/salvar-progresso
$roteador->adicionarRota('/api/salvar-progresso', 'Usuario', 'salvarProgresso', 'POST');


// 4. Despachar a Requisição
// Passamos a URI limpa e tratada para o roteador.
$roteador->despachar($uriFinal);
