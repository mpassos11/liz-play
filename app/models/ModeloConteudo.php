<?php
/**
 * MODELOCONTEUDO.PHP
 * * Lógica de negócios para acessar e manipular dados de Filmes, Séries e TV.
 */

class ModeloConteudo extends ModeloBase
{

    private ModeloApiIptv $apiIptv;

    public function __construct()
    {
        parent::__construct();
        $this->apiIptv = new ModeloApiIptv(); // Carregado via Autocarga
    }

    public function obterPorTipo(string $tipo): array
    {
        switch ($tipo) {
            case 'filmes.json':
                return $this->apiIptv->obterFilmes();
            case 'series.json':
                return $this->apiIptv->obterSeries();
            case 'tv.json':
                return $this->apiIptv->obterCanaisAoVivo();
        }

        return [];
    }

    /**
     * Busca os detalhes de um item específico (filme, episódio de série, canal de TV).
     * Nota: Esta função simula a busca em JSON, que pode ser ineficiente para grandes catálogos.
     * @param string $tipo Tipo de conteúdo ('filme', 'serie', 'tv').
     * @param string $id ID único do item.
     * @return array|null Os detalhes do conteúdo ou null se não for encontrado.
     */
    public function obterDetalhes(string $tipo, string $id): ?array
    {

        switch ($tipo) {
            case 'filme':
                $arquivo = 'filmes.json';
                break;
            case 'serie':
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
            return null; // Tipo inválido
        }

        $catalogo = $this->carregarConteudoJson($arquivo);

        // Simulação de busca linear no array (deve ser otimizado para produção)
        foreach ($catalogo as $item) {
            // Assumindo que o ID está na chave 'id'
            if (isset($item['id']) && $item['id'] === $id) {
                // Adiciona o tipo de volta para que o Controller saiba lidar com ele
                $item['tipo_conteudo'] = $tipo;
                return $item;
            }
        }

        return null;
    }
}