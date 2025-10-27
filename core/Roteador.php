<?php

class Roteador
{
    private Array $rotas = [];

    // Adiciona uma rota ao mapa
    public function adicionarRota(string $uri, string $controlador, string $acao, string $metodo = 'GET'): void
    {
        // Converte a URI para um padrão de expressão regular
        $padrao = '#^' . str_replace(['/', '(\w+)', '([\w\d]+)'], ['\/', '([^\/]+)', '([^\/]+)'], $uri) . '$#';

        $this->rotas[$metodo][$padrao] = [
            'controlador' => 'Controlador' . $controlador,
            'acao' => $acao
        ];
    }

    // Processa a URI e executa a ação correspondente
    public function despachar(string $uri): void
    {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $uri_limpa = strtok($uri, '?'); // Remove query string

        if (!isset($this->rotas[$metodo])) {
            // Lidar com método não permitido (405)
            $this->lidarErro(405);
            return;
        }

        foreach ($this->rotas[$metodo] as $padrao => $destino) {
            if (preg_match($padrao, $uri_limpa, $matches)) {

                // Remove o primeiro item (que é a URI completa)
                array_shift($matches);

                // Formatar o nome do controlador (ex: ControladorHome)
                $nomeControlador = $destino['controlador'];
                $nomeAcao = $destino['acao'];

                // Verificar e instanciar o controlador
                $caminhoControlador = APP_ROOT . '/app/Controllers/' . $nomeControlador . '.php';

                if (file_exists($caminhoControlador)) {
                    require_once $caminhoControlador;

                    $controlador = new $nomeControlador();

                    // Verificar e chamar a ação (método)
                    if (method_exists($controlador, $nomeAcao)) {
                        // Chama o método do controlador passando os parâmetros da rota
                        call_user_func_array([$controlador, $nomeAcao], $matches);
                        return;
                    }
                }
            }
        }

        // Se nenhuma rota foi encontrada (404)
        $this->lidarErro(404);
    }

    private function lidarErro(int $codigo): void
    {
        http_response_code($codigo);
        // Implemente a renderização de uma página de erro (ex: 404.php) aqui
        echo "Erro {$codigo}";
    }
}

?>