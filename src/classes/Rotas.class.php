<?php

class Rota
{
	private $rotas = [];
	private $middleware = [];
	
	public function __construct()
	{
	}
	
	public function rota($path, $acao, $middleware = false)
	{
		// Converter {param} em uma regex para capturar parâmetros dinâmicos
		$path = preg_replace('/{(\w+)}/', '(?P<\1>[^/]+)', $path);
		$path = '#^' . $path . '$#';  // Define o padr�o da rota
		
		$this->rotas[$path] = $acao;
		if ($middleware) {
			$this->middleware[$path] = $middleware;
		}
	}
	
	public function resolver($uri)
	{
		
		if (stripos(COMMON_URL, 'liz-play') !== false) {
			$uri = str_ireplace('liz-play/', '', $uri);
		}
		
		$uri = parse_url($uri, PHP_URL_PATH);
		
		foreach ($this->rotas as $path => $acao) {
			// Verificar se a URI corresponde ao padrão da rota
			if (preg_match($path, $uri, $matches)) {
				// Filtrar somente os par�metros nomeados
				$params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
				
				// Verificar se há middleware
				if (isset($this->middleware[$path])) {
					$middlewareFunc = $this->middleware[$path];
					if (is_callable($middlewareFunc) && !$middlewareFunc()) {
						return;
					}
				}

				// Chamar a função associada à rota
                if (is_string($acao)) {
                    list($classe, $metodo) = explode('::', $acao);
                    if (class_exists($classe) && method_exists($classe, $metodo)) {
                        $class = new $classe();

                        if ($params) {
                            call_user_func_array([$class, $metodo], $params);
                            return;
                        }

                        $class->$metodo();
                    }
                }
				return;
			}
		}
		
		redirect('404');
	}
}
