<?php

include_once __DIR__ . '/../src/classes/Rotas.class.php';

$rota = new Rota();

// Home
$rota->rota('/', 'Home::index');
$rota->rota('/imagens/{nome}', 'Home::imagem');
$rota->rota('/configuracao', 'Home::configuracao');
$rota->rota('/sair', 'Home::sair');

// Navegação
$rota->rota('/conteudos/{tipo}', 'Navegacao::conteudos');
$rota->rota('/salvar-progresso', 'Usuarios::salvarProgresso');
$rota->rota('/pesquisa', 'Navegacao::pesquisa');
$rota->rota('/assistir/{tipo}/{id}', 'Navegacao::assistir');

// API
$rota->rota('/api/categorias', 'API::getCategorias');
$rota->rota('/api/conteudos', 'API::getConteudos');
$rota->rota('/api/episodios', 'API::getEpisodios');
$rota->rota('/api/salvar-progresso', 'API::salvarProgresso');
$rota->rota('/api/concluir', 'API::concluirSerie');
$rota->rota('/api/pesquisar', 'API::pesquisar');

$rota->rota('/404', function () {
	echo 'Rota não encontrada';
});
