<?php
/**
 * MODELOCONTEUDO.PHP
 * * Lógica de negócios para acessar e manipular dados de Filmes, Séries e TV.
 * * Implementa Cache local com TTL (Time To Live) de 24 horas.
 */

class ModeloConteudo extends ModeloBase {

    private $apiIptv;

    // Define o tempo de vida do cache em segundos (24 horas)
    private const CACHE_TTL = 86400; // 24 * 60 * 60

    // Novo diretório para salvar o cache da API, fora de 'content/' que era estático
    private $diretorioCache = APP_ROOT . '/cache/';

    public function __construct() {
        parent::__construct();
        // ModeloApiIptv será carregado via Autocarga
        $this->apiIptv = new ModeloApiIptv();

        // Garante que o diretório de cache exista
        if (!is_dir($this->diretorioCache)) {
            // Cria o diretório recursivamente com permissão 0777 (para fins de desenvolvimento)
            @mkdir($this->diretorioCache, 0777, true);
        }
    }

    /**
     * Obtém todo o conteúdo de um tipo específico (Filmes, Séries ou TV).
     * Usa o arquivo local como cache com TTL.
     * @param string $nomeCache Nome do arquivo de cache (ex: 'filmes.json').
     * @return array Conteúdo do arquivo ou array vazio.
     */
    public function obterPorTipo(string $nomeCache): array {

        $caminhoCache = $this->diretorioCache . $nomeCache;
        $conteudo = [];
        $deveAtualizar = true;

        // 1. Verificar a existência e a validade do cache
        if (file_exists($caminhoCache)) {
            $tempoModificacao = filemtime($caminhoCache);
            $tempoExpiracao = $tempoModificacao + self::CACHE_TTL;

            if (time() < $tempoExpiracao) {
                // Cache válido: Tenta carregar os dados
                $conteudo = carregarDadosJson($caminhoCache);

                // Se o JSON foi lido com sucesso, não precisamos atualizar
                if (!empty($conteudo)) {
                    $deveAtualizar = false;
                }
            }
        }

        // 2. Se for necessário atualizar (cache expirado ou inexistente/inválido)
        if ($deveAtualizar) {
            error_log("Atualizando cache para: " . $nomeCache); // Log para saber quando ocorre

            // Determina qual função da API chamar com base no nome do cache
            switch ($nomeCache) {
                case 'filmes.json':
                    $conteudo = $this->apiIptv->obterFilmes();
                    break;
                case 'series.json':
                    $conteudo = $this->apiIptv->obterSeries();
                    break;
                case 'tv.json':
                    $conteudo = $this->apiIptv->obterCanaisAoVivo();
                    break;
                default:
                    // Se for um tipo desconhecido, retorna vazio
                    return [];
            }

            // 3. Salvar o novo conteúdo da API no cache
            if (!empty($conteudo)) {
                // A função salvarDadosJson está em FuncoesGerais.php
                salvarDadosJson($caminhoCache, $conteudo);
            }
        }

        return $conteudo ?? [];
    }

    /**
     * Busca os detalhes de um item específico (filme, episódio de série, canal de TV).
     * @param string $tipo Tipo de conteúdo ('filme', 'serie', 'tv').
     * @param string $id ID único do item.
     * @return array|null Os detalhes do conteúdo ou null se não for encontrado.
     */
    public function obterDetalhes(string $tipo, string $id): ?array {

        // Mapeia o tipo para o nome do arquivo de cache
        $arquivoCache = null;
        switch ($tipo) {
            case 'filme':
                $arquivoCache = 'filmes.json';
                break;
            case 'serie':
                $arquivoCache = 'series.json';
                break;
            case 'tv':
                $arquivoCache = 'tv.json';
                break;
        }

        if (!$arquivoCache) {
            return null; // Tipo inválido
        }

        // Usa a função otimizada para buscar do cache (ou atualizar se necessário)
        $catalogo = $this->obterPorTipo($arquivoCache);

        // Simulação de busca linear no array (pode ser otimizado)
        foreach ($catalogo as $item) {
            if (isset($item['stream_id']) && (string)$item['stream_id'] === $id) {
                // Assumindo que o ID principal da API Xtream é 'stream_id'
                $item['tipo_conteudo'] = $tipo;
                return $item;
            }
            // Adiciona fallback para outras chaves de ID se necessário
            if (isset($item['id']) && (string)$item['id'] === $id) {
                $item['tipo_conteudo'] = $tipo;
                return $item;
            }
        }

        return null;
    }
}