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

        if ($tipoConteudo === TV_TIPO) {
            $conteudoAExibir = $this->iptv->ordenarCanaisTV($conteudo);
        } else {
            $conteudoAExibir = [];
            $categorias = $this->iptv->obterPorTipo('categorias.json');
            // ordenar o array aleatoriamente
            shuffle($conteudo);
            foreach ($conteudo as $item) {
                $categoria = array_search($item['category_id'], array_column($categorias, 'category_id'), true);
                $conteudoAExibir[$categorias[$categoria]['category_name']][] = $item;
            }

            foreach ($conteudoAExibir as $categoria => $itens) {
                // pegar 10 conteudos randomicos de cada categoria
                $min = 10;
                $total = count($itens);
                if ($min > $total) {
                    $min = $total;
                }

                $random = array_rand($itens, $min);
                if (is_array($random)) {
                    $novosItens = [];
                    foreach ($random as $r) {
                        $novosItens[] = $itens[$r];
                    }
                    $conteudoAExibir[$categoria] = $novosItens;
                }
            }
        }

        $dados = [
            'titulo' => ucwords($tipoConteudo),
            'conteudo' => $conteudoAExibir,
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

        $progresso = $this->modeloProgresso->buscarProgressoPorId($id) ?: [];

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

    public static function renderizarConteudo($categoria, $item): string
    {
        $html = '';
        if (empty($item['title']) && is_array($item)) {
            $html .= "<div class='col-md-12'><h4>". ucfirst($categoria) ."</h4><div class='slick-carousel row'>";
            foreach ($item as $value) {
                $html .= self::renderizarConteudo($categoria, $value);
            }
            $html .= "</div></div>";
        } else {
            $item['tipo'] = $item['tipo'] ?? $item['stream_type'] ?? 'tv';
            $html = '<div class="col">
                    <div class="card bg-dark text-white h-100 shadow-sm border-0">

                        <img data-lazy="' . $item['stream_icon'] . '"
                             class="card-img-top lazy"
                             style="object-fit: cover; height: 250px; padding: 20px"
                             loading="lazy">

                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate"
                                title="' . htmlspecialchars($item['title'] ?? 'Sem Título') . '">
                                ' . htmlspecialchars($item['title'] ?? 'Sem Título') . '
                            </h6>
                            <a href="' . base_url("assistir/{$item['tipo']}/{$item['stream_id']}") . '"
                               class="btn btn-sm btn-danger w-100 mt-2">
                                Assistir Agora
                            </a>
                        </div>
                    </div>
                </div>';
        }

        return $html;
    }
}
