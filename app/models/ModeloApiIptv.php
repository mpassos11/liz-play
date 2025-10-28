<?php
/**
 * MODELOAPIIPTV.PHP
 * * Adaptado para consumir listas M3U do IPTV-ORG.
 * * NOTA: O IPTV-ORG foca em TV AO VIVO. Filmes (VOD) e Séries não serão implementados.
 */

class ModeloApiIptv
{

    private $m3uUrl;

    public function __construct()
    {
        // Pega a URL do M3U
        $this->m3uUrl = getenv('IPTV_API_URL');

        if (!$this->m3uUrl) {
            error_log("ERRO FATAL: URL IPTV_API_URL não configurada no .env");
        }

        // As credenciais de login e senha são ignoradas para listas M3U públicas
    }

    /**
     * Faz a requisição HTTP (cURL) para obter o conteúdo RAW do M3U.
     * @param string $url A URL completa do M3U.
     * @return string O conteúdo do arquivo M3U ou string vazia em caso de erro.
     */
    private function fazerRequisicaoM3U(string $url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'LizPlay-App-Client');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("Erro cURL ao buscar M3U: " . curl_error($ch));
            curl_close($ch);
            return '';
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Erro HTTP {$httpCode} ao buscar M3U.");
            return '';
        }

        return (string)$response;
    }

    /**
     * Analisa (parseia) o conteúdo M3U e o converte para um array JSON estruturado.
     * @param string $m3uContent Conteúdo de texto do arquivo M3U.
     * @return array Array de canais formatado.
     */
    private function parseM3U(string $m3uContent): array
    {
        $linhas = explode("\n", $m3uContent);
        $canais = [];
        $item = [];

        foreach ($linhas as $linha) {
            $linha = trim($linha);

            if (strpos($linha, '#EXTINF:') === 0) {
                // Linha de informações do canal (#EXTINF)

                // 1. Extrai o nome do canal (após a última vírgula)
                $nomeCanal = trim(substr($linha, strrpos($linha, ',') + 1));

                // 2. Extrai tags (tvg-id, tvg-logo, group-title)
                $tags = [];
                if (preg_match_all('/([a-z0-9-]+)="([^"]*)"/', $linha, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $tags[$match[1]] = $match[2];
                    }
                }

                // Prepara o array do item para a próxima URL
                $item = [
                    'id' => $tags['tvg-id'] ?? uniqid(), // Usa tvg-id como ID ou gera um único
                    'stream_id' => $tags['tvg-id'] ?? uniqid(), // Compatibilidade com Xtream Codes
                    'title' => $nomeCanal,
                    'name' => $nomeCanal,
                    'stream_icon' => $tags['tvg-logo'] ?? '',
                    'category_name' => $tags['group-title'] ?? 'Sem Categoria',
                    'stream_type' => 'live', // Tipo fixo para IPTV-ORG
                    'live_source' => '', // URL será adicionada na próxima linha
                ];

            } elseif (!empty($linha) && strpos($linha, 'http') === 0) {
                // Linha da URL do Stream (que vem após a linha #EXTINF)

                if (!empty($item)) {
                    $item['live_source'] = $linha;
                    $canais[] = $item;
                    $item = []; // Zera o item para o próximo canal
                }
            }
        }

        return $canais;
    }

    // ---------------------------------------------------------------------
    // MÉTODOS PÚBLICOS PARA ACESSAR OS CONTEÚDOS
    // ---------------------------------------------------------------------

    /**
     * Obtém a lista de canais de TV ao vivo do M3U.
     * Retorna um array no formato esperado pelo sistema.
     * @return array
     */
    public function obterCanaisAoVivo(): array
    {
        $m3uContent = $this->fazerRequisicaoM3U($this->m3uUrl);

        if (empty($m3uContent)) {
            return [];
        }

        return $this->parseM3U($m3uContent);
    }

    // ---------------------------------------------------------------------
    // MÉTODOS PARA FILMES E SÉRIES: Não suportados pelo IPTV-ORG
    // ---------------------------------------------------------------------

    public function obterFilmes(): array
    {
        return [];
    }

    public function obterSeries(): array
    {
        return [];
    }

    /**
     * Constrói a URL direta de reprodução.
     * Para M3U, a URL de reprodução já está no campo 'live_source' do item.
     * Este método é simplificado para retornar a URL de teste (live_source).
     */
    public function construirUrlReproducao(string $idStream, string $tipo = 'live', string $formato = 'ts'): string
    {
        // Como o IPTV-ORG não usa a estrutura "live/user/pass/streamid.ts",
        // esta função deve ser chamada de outra forma no ControladorAssistir.

        // Em um cenário real de M3U, você precisa buscar o item pelo $idStream
        // e retornar o campo 'live_source' que foi salvo no cache.

        // Retornamos um placeholder para evitar erros se não for usado.
        error_log("ATENÇÃO: A função construirUrlReproducao não é ideal para M3U. Use a URL salva em cache.");

        return $this->m3uUrl; // Retorno de fallback.
    }
}