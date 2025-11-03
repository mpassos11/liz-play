<?php

class Home
{

    public function index()
	{
        if (!$_SESSION['perfil_id']) {
            if (!$_GET['id']) {
                include COMMON_PATH . '/views/perfis.php';
                exit;
            }

            $_SESSION['perfil_id'] = $_GET['id'];
        }

		view('home', [
			'ultimosAssistidos' => [],
			'seriesAleatorias' => [],
			'filmesAleatorios' => []
		]);
	}

    public function sair()
    {
        unset($_SESSION['perfil_id']);
        session_destroy();
        header('Location: ' . base_url());
        exit;
    }
	
	public function imagem($nome)
	{
		$imagem = COMMON_PATH . "public/imagens/$nome";
		$mimetype = mime_content_type($imagem);
		
		header("Content-Type: $mimetype");
		header("Content-Length: " . filesize($imagem));
		header("Content-Disposition: inline; filename=$nome");
		echo file_get_contents($imagem);
	}
	
	public function configuracao()
	{
        if ($_POST) {
            atualizarConfigs($_POST);
        }

		$config = [
            'IPTV_API_URL' => getenv('IPTV_API_URL'),
            'IPTV_USERNAME' => getenv('IPTV_USERNAME'),
            'IPTV_PASSWORD' => getenv('IPTV_PASSWORD'),
        ];
		
		view('configuracao', ['config' => $config ?: []]);
	}
}
