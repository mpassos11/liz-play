<?php
// Define constantes úteis (caminho raiz da aplicação)
define('APP_ROOT', dirname(__DIR__));

// 1. Carregar Autocarga e Funções
require_once APP_ROOT . '/core/Autocarga.php';
require_once APP_ROOT . '/core/FuncoesGerais.php'; // Para funções como loadJsonData

// 2. Instanciar o Roteador
$roteador = new Roteador();

// 3. Definir Rotas
// Rota Principal: /
$roteador->adicionarRota('/', 'Home', 'index');
// Rota de Categoria: /filmes, /series, /tv
$roteador->adicionarRota('/(\w+)', 'Home', 'categoria');
// Rota de Visualização: /assistir/{tipo}/{id} (ex: /assistir/filme/m1001)
$roteador->adicionarRota('/assistir/(\w+)/([\w\d]+)', 'Conteudo', 'assistir');
// Rota AJAX para salvar o progresso: /api/salvar-progresso
$roteador->adicionarRota('/api/salvar-progresso', 'Usuario', 'salvarProgresso', 'POST');


// 4. Despachar a Requisição
$uri = $_SERVER['REQUEST_URI'];
$roteador->despachar($uri);
?>