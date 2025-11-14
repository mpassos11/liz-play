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

$rota->rota('/404', function () {
	echo 'Rota não encontrada';
});
