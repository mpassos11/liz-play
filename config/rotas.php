<?php

include_once __DIR__ . '/../src/classes/Rotas.class.php';

$rota = new Rota();

// Home
$rota->rota('/', 'Home::index');
$rota->rota('/configuracao', 'Home::configuracao');
$rota->rota('/sair', 'Home::sair');
$rota->rota('/atualizar-dados', 'Home::atualizarDados');

// Proxy
$rota->rota('/proxy', 'Proxy::index');

// Navegação
$rota->rota('/conteudos/{tipo}', 'Navegacao::conteudos');
$rota->rota('/salvar-progresso', 'Usuarios::salvarProgresso');
$rota->rota('/pesquisa', 'Navegacao::pesquisa');
$rota->rota('/assistir/{tipo}/{id}', 'Navegacao::assistir');
$rota->rota('/pesquisar', 'Navegacao::pesquisar');
$rota->rota('/episodios/series/{id}', 'Navegacao::episodiosSeries');

// API
$rota->rota('/api/conteudos/{tipo}', 'API::conteudos');
$rota->rota('/api/assistir/{tipo}/{id}', 'API::assistir');
$rota->rota('/api/episodios/series/{id}', 'API::episodiosSeries');
$rota->rota('/api/salvar-progresso', 'API::salvarProgresso');
$rota->rota('/api/ultimos-assistidos', 'API::ultimosAssistidos');
$rota->rota('/api/pesquisar', 'API::pesquisar');


$rota->rota('/404', function () {
	echo 'Rota não encontrada';
});
