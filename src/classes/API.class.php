<?php

class API
{
	
	public function getCategorias()
	{
		$categoria = get_post('categoria');
		if (empty($categoria)) {
			api([], 400);
		}
		
		$categorias = $GLOBALS['IPTV']->getCategorias($categoria);
		
		if ($categoria == 'ultimos-assistidos') {
			api(['conteudos' => $categorias]);
		}
		
		api(['categorias' => $categorias]);
	}
	
	public function getConteudos()
	{
		$tipoCategoria = get_post('tipo');
		$idCategoria = get_post('id');
		if (empty($tipoCategoria) || empty($idCategoria)) {
			api([], 400);
		}
		
		$conteudos = $GLOBALS['IPTV']->getConteudos($tipoCategoria, $idCategoria);
		api(['conteudos' => $conteudos['conteudo']]);
	}
	
	public function getEpisodios()
	{
		$idSerie = get_post('id');
		if (empty($idSerie)) {
			api([], 400);
		}
		
		$episodios = $GLOBALS['IPTV']->getEpisodiosSerie($idSerie);
		api(['episodios' => $episodios]);
	}
	
	public function salvarProgresso()
	{
		$stream_id = get_post('stream_id');
		$tempo = get_post('tempo');
		if (empty($stream_id) || empty($tempo)) {
			api([], 400);
		}
		
		$tempo = intval($tempo);
		$GLOBALS['IPTV']->salvarProgresso($stream_id, $tempo);
		
		api(['success' => true]);
	}
	
	public function concluirSerie()
	{
		$serie_id = get_post('idserie');
		if (empty($serie_id)) {
			api([], 400);
		}
		
		$GLOBALS['IPTV']->concluirSerie($serie_id);
		
		api(['success' => true]);
	}
	
	public function pesquisar()
	{
		$busca = get_post('busca');
		if (empty($busca)) {
			api([], 400);
		}
		
		$titulos = $GLOBALS['IPTV']->pesquisar($busca);
		
		if (empty($titulos['series'])) {
			unset($titulos['series']);
		}
		
		if (empty($titulos['filmes'])) {
			unset($titulos['filmes']);
		}
		
		api(['titulos' => $titulos]);
	}
	
}