<?php
/**
 * MODELOCONTEUDO.PHP
 * * Lógica de negócios para acessar e manipular dados de Filmes, Séries e TV.
 */

class ModeloConteudo extends ModeloBase
{

    /**
     * Obtém todo o conteúdo de um tipo específico (Filmes, Séries ou TV).
     * @param string $arquivoJson O nome do arquivo JSON a ser lido (ex: 'filmes.json').
     * @return array Conteúdo do arquivo ou array vazio.
     */
    public function obterPorTipo(string $arquivoJson): array
    {
        return $this->carregarConteudoJson($arquivoJson);
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