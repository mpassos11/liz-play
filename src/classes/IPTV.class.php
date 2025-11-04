<?php

class IPTV
{
    private $apiUrlBase;
    private $username;
    private $password;
    private $diretorioCache = COMMON_PATH . '/content/';
    private const CACHE_TTL = 86400; // 24 * 60 * 6

    public function __construct()
    {
        $this->apiUrlBase = getenv('IPTV_API_URL');
        $this->username = getenv('IPTV_USERNAME');
        $this->password = getenv('IPTV_PASSWORD');

        if (!$this->apiUrlBase || !$this->username || !$this->password) {
            error_log("ERRO FATAL: Credenciais Xtream Codes (URL, User, Pass) não configuradas no .env");
        }

        if (!is_dir($this->diretorioCache)) {
            // Cria o diretório recursivamente com permissão 0777 (para fins de desenvolvimento)
            @mkdir($this->diretorioCache, 0777, true);
        }
    }

    /* Faz uma requisição HTTP para a API e retorna o resultado em JSON.
    * @param string $action A ação específica da API (ex: get_live_streams).
    * @param array $params Parâmetros adicionais para a requisição.
    * @return array|null O array decodificado do JSON ou null em caso de falha.
    */
    private function fazerRequisicaoAPI(string $action, array $params = []): ?array
    {
        // 1. Constrói a URL base com autenticação e ação
        $url = $this->apiUrlBase . '/player_api.php';
        $query = [
            'username' => $this->username,
            'password' => $this->password,
            'action' => $action
        ];

        // 2. Adiciona parâmetros específicos da ação (como category_id, etc.)
        $query = array_merge($query, $params);
        $urlCompleta = $url . '?' . http_build_query($query);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $urlCompleta);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'LizPlay-App-Client');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Necessário se o servidor usar SSL autoassinado
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("Erro cURL ao buscar API: " . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Erro HTTP {$httpCode} ao buscar API. URL: " . $urlCompleta);
            return null;
        }

        $json = json_decode($response, true);

        if ($json === null) {
            error_log("Falha ao decodificar JSON da API.");
            return null;
        }

        return $json;
    }

    /**
     * Obtém todo o conteúdo de um tipo específico (Filmes, Séries ou TV).
     * Usa o arquivo local como cache com TTL.
     * @param string $nomeCache Nome do arquivo de cache (ex: 'filmes.json').
     * @return array Conteúdo do arquivo ou array vazio.
     */
    public function obterPorTipo(string $nomeCache): array
    {

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
                    $conteudo = $this->obterFilmes();
                    break;
                case 'series.json':
                    $conteudo = $this->obterSeries();
                    break;
                case 'tv.json':
                    $conteudo = $this->obterCanaisAoVivo();
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

    public function ordenarCanaisTV(array $canais): array
    {
        // 1. FILTRAGEM E SELEÇÃO DA MELHOR QUALIDADE (FHD > HD)
        $preferredChannels = [];
        $qualityRank = ['FHD' => 3, 'HD' => 2, 'SD' => 1, '4K' => 0];

        // tirar tudo com categoria 24
        $canais = array_filter($canais, function ($canal) {
            return !in_array(24, $canal['category_ids'], true);
        });

        foreach ($canais as $channel) {
            $title = strtoupper($channel['title']);
            $quality = 'SD';

            // Identificar a qualidade
            if (strpos($title, 'FHD') !== false || strpos($title, 'FULL HD') !== false) {
                $quality = 'FHD';
            } elseif (strpos($title, 'HD') !== false) {
                $quality = 'HD';
            } elseif (strpos($title, '4K') !== false) {
                $quality = '4K';
            }

            $currentRank = $qualityRank[$quality];

            // Ignorar 4K e SD
            if ($currentRank <= $qualityRank['SD'] && stripos($title, '24h') === false) {
                continue;
            }

            // Extrair o nome base e definir a chave única
            $baseName = preg_replace('/\s(FHD|FULL\sHD|HD|SD|4K|\d)\s*$/', '', $title);
            $uniqueKey = $channel['epg_channel_id'] ?? $baseName;

            $channel['quality_rank'] = $currentRank;
            $channel['base_name'] = $baseName;

            // Comparar com o canal já armazenado
            if (
                !isset($preferredChannels[$uniqueKey]) ||
                $currentRank > $preferredChannels[$uniqueKey]['quality_rank']
            ) {
                $preferredChannels[$uniqueKey] = $channel;
            }
        }

        // Converte de volta para um array sequencial para as funções de ordenação
        $filteredList = array_values($preferredChannels);


        // ----------------------------------------------------------------------
        // 2. CATEGORIZAÇÃO (Separar os canais nas 3 categorias)
        // ----------------------------------------------------------------------

        // Obter constantes de forma segura, presumindo que estão definidas no escopo
        $principalPriority = PRINCIPAL_PRIORITY;
        $canais24HCategoryId = CANAIS_24H_CATEGORY_ID;
        $filmesCategoryId = FILMES_CATEGORY_ID;

        $categorized = [
            'principais' => [],
            'canais 24hrs' => [],
            'demais canais' => [],
        ];

        foreach ($filteredList as $channel) {
            $baseName = $channel['base_name'];
            $isPrincipal = false;

            // Verifica se é um canal principal
            foreach ($principalPriority as $principalName) {
                if (strpos($baseName, $principalName) !== false) {
                    $isPrincipal = true;
                    break;
                }
            }

            if ($isPrincipal) {
                $categorized['principais'][] = $channel;
            } elseif (stripos($channel['base_name'], '24h') !== false) {
                $categorized['canais 24hrs'][] = $channel;
            } else {
                $categorized['demais canais'][] = $channel;
            }
        }

        // ----------------------------------------------------------------------
        // 3. ORDENAÇÃO POR CATEGORIA (Incluindo a regra de "visto" para os principais)
        // ----------------------------------------------------------------------

        // Prepara o mapa de prioridade para a ordenação (rank menor = maior prioridade)
        $priorityMap = array_flip(array_values($principalPriority));

        // Visto rastreia qual canal principal já foi colocado na frente
        $seen = [];

        // --- Ordenação para a categoria 'principais' (com regra de repetição) ---
        $sortPrincipais = function ($a, $b) use ($priorityMap, &$seen) {
            $nameA = $a['base_name'];
            $nameB = $b['base_name'];

            $rankA = 1000;
            $rankB = 1000;

            $principalNameA = null;
            $principalNameB = null;

            // 1. Atribuir Rank e Identificar o nome principal
            foreach ($priorityMap as $name => $rank) {
                if (strpos($nameA, $name) !== false) {
                    $rankA = $rank;
                    $principalNameA = $name;
                }
                if (strpos($nameB, $name) !== false) {
                    $rankB = $rank;
                    $principalNameB = $name;
                }
            }

            // 2. Aplicar a Regra de "Visto" (Se ambos são canais prioritários)
            if ($rankA < 1000 && $rankB < 1000) {
                $isSeenA = isset($seen[$principalNameA]);
                $isSeenB = isset($seen[$principalNameB]);

                // Se A foi visto e B não, B vem primeiro (e marca B como visto)
                if ($isSeenA && !$isSeenB) {
                    $seen[$principalNameB] = true;
                    return 1; // A (visto) vai para o final
                }

                // Se B foi visto e A não, A vem primeiro (e marca A como visto)
                if (!$isSeenA && $isSeenB) {
                    $seen[$principalNameA] = true;
                    return -1; // A (não visto) vem para frente
                }

                // Se nenhum foi visto, marca o canal que virá primeiro como visto
                if (!$isSeenA && !$isSeenB) {
                    if ($rankA <= $rankB) {
                        $seen[$principalNameA] = true;
                    } else {
                        $seen[$principalNameB] = true;
                    }
                }
            }

            // 3. Ordenação por Rank Principal
            if ($rankA !== $rankB) {
                return $rankA <=> $rankB;
            }

            // 4. Ordenação por Número e Alfabética
            preg_match('/\d+/', $nameA, $matchesA);
            preg_match('/\d+/', $nameB, $matchesB);

            $numA = $matchesA[0] ?? 0;
            $numB = $matchesB[0] ?? 0;

            return ($numA <=> $numB) ?: $nameA <=> $nameB;
        };

        // --- Ordenação para a categoria 'demais canais' (Filmes primeiro) ---
        $sortDemaisCanais = function ($a, $b) use ($filmesCategoryId) {
            $isFilmesA = $a['category_id'] === $filmesCategoryId;
            $isFilmesB = $b['category_id'] === $filmesCategoryId;

            if ($isFilmesA && !$isFilmesB) return -1; // Filmes vem antes
            if (!$isFilmesA && $isFilmesB) return 1;  // Filmes vem antes

            // Se ambos são Filmes ou nenhum é, ordena alfabeticamente
            return $a['title'] <=> $b['title'];
        };

        // Aplicar a ordenação em cada categoria
        usort($categorized['principais'], $sortPrincipais);
        usort($categorized['canais 24hrs'], function ($a, $b) { return $a['title'] <=> $b['title']; }); // Alfabético
        usort($categorized['demais canais'], $sortDemaisCanais);

        return $categorized;
    }

    // ---------------------------------------------------------------------
    // MÉTODOS PÚBLICOS PARA CONTEÚDO
    // ---------------------------------------------------------------------

    /**
     * Obtém a lista de canais de TV ao vivo.
     * Endpoint: player_api.php?action=get_live_streams
     * @return array Lista de canais.
     */
    public function obterCanaisAoVivo(): array
    {
        $response = $this->fazerRequisicaoAPI('get_live_streams');

        if (empty($response)) {
            return [];
        }

        return $this->tratarLinksReproducao($response);
    }

    /**
     * Obtém a lista de Filmes (VOD).
     * Endpoint: player_api.php?action=get_vod_streams
     * @return array Lista de filmes.
     */
    public function obterFilmes(): array
    {
        $response = $this->fazerRequisicaoAPI('get_vod_streams');

        if (empty($response)) {
            return [];
        }

        return $this->tratarLinksReproducao($response);
    }

    /**
     * Obtém a lista de Séries.
     * Endpoint: player_api.php?action=get_series
     * @return array Lista de séries.
     */
    public function obterSeries(): array
    {
        $response = $this->fazerRequisicaoAPI('get_series');

        if (empty($response)) {
            return [];
        }

        return $this->tratarLinksReproducao($response);
    }

    public function obterEpisodiosSeries(string $stream_id): array
    {
        $caminhoJsonEpisodios = COMMON_PATH . '/content/series_episodios.json';
        $episodios = carregarDadosJson($caminhoJsonEpisodios);

        $buscarConteudoAPI = false;
        if (empty($episodios) || $episodios[$stream_id] === null) {
            $buscarConteudoAPI = true;
        }

        if ($buscarConteudoAPI) {
            $response = $this->fazerRequisicaoAPI('get_series_info', ['series_id' => $stream_id]);

            if (empty($response)) {
                return [];
            }

            $response = $response['episodes'];

            foreach ($response as $k => $temporada) {
                $response[$k] = $this->tratarLinksReproducao($temporada);
            }

            $episodios[$stream_id] = $response;
            salvarDadosJson($caminhoJsonEpisodios, $episodios);
        }

        return $episodios[$stream_id];
    }

    private function tratarLinksReproducao(array $itens): array
    {
        foreach ($itens as &$item) {
            $item['title'] = $item['title'] ?? $item['name'];
            $item['stream_id'] = $item['stream_id'] ?? $item['vod_id'] ?? $item['series_id'] ?? $item['id'];
            $item['stream_icon'] = $item['stream_icon'] ?? $item['cover'];
            $item['stream_type'] = $item['stream_type'] ?? 'series';
            $item['stream_link'] = $this->construirUrlReproducao($item['stream_id'], $item['stream_type']);
        }

        return $itens;
    }

    // ---------------------------------------------------------------------
    // CONSTRUÇÃO DA URL DE REPRODUÇÃO
    // ---------------------------------------------------------------------

    /**
     * Constrói a URL direta de reprodução.
     * Padrão Xtream Codes: http://server:port/tipo/usuario/senha/stream_id.ts
     * * @param string $idStream O ID do stream (stream_id, vod_id ou series_id).
     * @param string $tipo O tipo de conteúdo ('live', 'vod' ou 'series').
     * @param string $formato O formato de arquivo desejado (geralmente 'ts' ou 'm3u8').
     * @return string A URL de reprodução completa e direta.
     */
    public function construirUrlReproducao(string $idStream, string $tipo = 'live', string $formato = 'ts'): string
    {
        // 1. Garante que a URL base termine sem barra
        $baseUrl = rtrim($this->apiUrlBase, '/');

        // 2. Constrói a URL
        $urlReproducao = sprintf(
            '%s/%s/%s/%s/%s.%s',
            $baseUrl,
            $tipo, // live, vod, series
            $this->username,
            $this->password,
            $idStream,
            $formato
        );

        return $urlReproducao;
    }

    public function obterDetalhes(string $tipo, string $id): ?array
    {

        // Mapeia o tipo para o nome do arquivo de cache
        $arquivoCache = null;
        switch ($tipo) {
            case 'filmes':
                $arquivoCache = 'filmes.json';
                break;
            case 'series':
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
