<?php

class Navegacao
{
    private IPTV $iptv;
    private ModeloProgresso $modeloProgresso;

    public function __construct()
    {
        $this->iptv = new IPTV();
        $this->modeloProgresso = new ModeloProgresso();
    }

    /**
     * Rota: /(\w+) (ex: /filmes, /series)
     * Exibe uma categoria específica.
     * * @param string $tipoConteudo O tipo de conteúdo (filmes, series, tv).
     */
    public function conteudos(string $tipoConteudo): void
    {
        if (!isset($_SESSION['perfil_id'])) {
            header('Location: /');
            exit;
        }

        // Mapeia o nome amigável da rota para o nome do arquivo JSON
        switch ($tipoConteudo) {
            case 'filmes':
                $arquivo = 'filmes.json';
                break;
            case 'series':
                $arquivo = 'series.json';
                break;
            case 'tv':
                $arquivo = 'tv.json';
                break;
            default:
                $arquivo = null;
                break;
        }

        if (!$arquivo) {
            // Se o tipo de conteúdo não for válido, exibe 404
            http_response_code(404);
            view('erro/404', ['mensagem' => "Categoria '{$tipoConteudo}' não encontrada."]);
            return;
        }

        // Carrega o conteúdo específico
        $conteudo = $this->iptv->obterPorTipo($arquivo);

        // Define o número de canais por página
        $limitePorPagina = 10;

        // Pega o número da página da URL (padrão 1)
        $paginaAtual = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($paginaAtual - 1) * $limitePorPagina;

        // 2. Calcula o total de páginas
        $totalConteudo = count($conteudo);
        $totalPaginas = ceil($totalConteudo / $limitePorPagina);

        // 3. Aplica a paginação (limita o array)
        $conteudoAExebir = array_slice($conteudo, $offset, $limitePorPagina);

        $dados = [
            'titulo' => ucwords($tipoConteudo),
            'conteudo' => $conteudoAExebir,
            'tipo' => $tipoConteudo,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas,
        ];

        // Renderiza uma view genérica de categoria ou a view home/index com a tab ativa
        view('conteudos', $dados);
    }

    public function assistir($tipo, $id)
    {
        $stream = $this->iptv->obterDetalhes($tipo, $id);
        $stream['tipo'] = $tipo;

        $episodios = [];
        if ($tipo === SERIES_TIPO) {
            // buscar episodios
            $episodios = $this->iptv->obterEpisodiosSeries($id);
            if (empty($episodios)) {
                http_response_code(404);
                view('erro/404', ['mensagem' => "Episódios não encontrados."]);
                return;
            }
        }

        $progresso = $this->modeloProgresso->buscarProgressoPorId($stream['stream_id']) ?: [];

        if ($tipo === TV_TIPO) {
            $stream['stream_link'] = str_replace('.ts', '.m3u8', $stream['stream_link']);
        }

        view('assistir', [
            'stream' => $stream,
            'titulo' => $stream['title'],
            'episodios' => $episodios,
            'progresso' => $progresso
        ], ['reprodutor', 'player'], ['player']);
    }
}
