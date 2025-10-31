<?php
/**
 * CONTROLADORHOME.PHP
 * * Gerencia a página inicial e a navegação por categorias (Filmes, Séries, TV).
 */

class ControladorHome extends ControladorBase
{
    private $modeloConteudo;
    private $modeloProgresso;

    // Simulação do ID do usuário logado (deve ser obtido de uma sessão real)
    private const USUARIO_ID_EXEMPLO = '12345';

    public function __construct()
    {
        // As classes ModeloConteudo e ModeloProgresso serão carregadas via Autocarga.php
        $this->modeloConteudo = new ModeloConteudo();
        $this->modeloProgresso = new ModeloProgresso();
    }

    /**
     * Rota: /
     * Carrega a página inicial com todas as categorias e o bloco "Continuar Assistindo".
     */
    public function index(): void
    {

        // 1. Carregar Dados Essenciais
        //$filmes = $this->modeloConteudo->obterPorTipo('filmes.json');
        //$series = $this->modeloConteudo->obterPorTipo('series.json');
        //$tv = $this->modeloConteudo->obterPorTipo('tv.json');
        $progresso = $this->modeloProgresso->obterProgresso(self::USUARIO_ID_EXEMPLO);

        $dados = [
            'tituloPagina' => 'Home - Liz Play',
            'filmes' => [],
            'series' => [],
            'tv' => [],
            'progressoUsuario' => $progresso['progressos'] ?? []
        ];

        // 2. Renderizar a View principal
        $this->renderizar('home/index', $dados);
    }

    /**
     * Rota: /(\w+) (ex: /filmes, /series)
     * Exibe uma categoria específica.
     * * @param string $tipoConteudo O tipo de conteúdo (filmes, series, tv).
     */
    public function categoria(string $tipoConteudo): void
    {

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
            $this->renderizar('erro/404', ['mensagem' => "Categoria '{$tipoConteudo}' não encontrada."]);
            return;
        }

        // Carrega o conteúdo específico
        $conteudo = $this->modeloConteudo->obterPorTipo($arquivo);

        // Define o número de canais por página
        $limitePorPagina = 25;

        // Pega o número da página da URL (padrão 1)
        $paginaAtual = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($paginaAtual - 1) * $limitePorPagina;

        // 2. Calcula o total de páginas
        $totalConteudo = count($conteudo);
        $totalPaginas = ceil($totalConteudo / $limitePorPagina);

        // 3. Aplica a paginação (limita o array)
        $conteudoAExebir = array_slice($conteudo, $offset, $limitePorPagina);

        $dados = [
            'tituloPagina' => ucwords($tipoConteudo),
            'conteudo' => $conteudoAExebir,
            'tipo' => $tipoConteudo,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas,
        ];

        // Renderiza uma view genérica de categoria ou a view home/index com a tab ativa
        $this->renderizar('home/categoria', $dados);
    }
}