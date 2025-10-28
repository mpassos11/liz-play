<?php
/**
 * MODELOAPIIPTV.PHP
 * * Abstrai a comunicação com o painel de IPTV (ex: Xtream Codes API).
 * * Utiliza as credenciais do arquivo .env.
 */

class ModeloApiIptv
{

    private $baseUrl;
    private $username;
    private $password;

    public function __construct()
    {
        // Pega as variáveis definidas pelo FuncoesConfig.php
        $this->baseUrl = getenv('IPTV_API_URL');
        $this->username = getenv('IPTV_USERNAME');
        $this->password = getenv('IPTV_PASSWORD');

        if (!$this->baseUrl || !$this->username || !$this->password) {
            // Em um sistema real, isso deve lançar uma exceção ou parar a aplicação
            error_log("ERRO FATAL: Credenciais IPTV não configuradas no .env");
        }
    }

    /**
     * Constrói a URL de requisição base para a player_api.php.
     * @param string $action A ação da API (ex: get_live_streams).
     * @param array $params Parâmetros adicionais.
     * @return string A URL completa da API.
     */
    private function construirUrl(string $action, array $params = []): string
    {
        $url = $this->baseUrl . '/player_api.php?';
        $url .= 'username=' . urlencode($this->username);
        $url .= '&password=' . urlencode($this->password);
        $url .= '&action=' . urlencode($action);

        foreach ($params as $key => $value) {
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        return $url;
    }

    /**
     * Faz a requisição à API e retorna os dados decodificados.
     * @param string $url A URL completa da API.
     * @return array Array de dados JSON decodificado ou array vazio em caso de erro.
     */
    private function fazerRequisicao(string $url): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Opcional: Adicionar um User-Agent para evitar bloqueios
        curl_setopt($ch, CURLOPT_USERAGENT, 'LizPlay-App-Client');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("Erro cURL na API IPTV: " . curl_error($ch));
            curl_close($ch);
            return [];
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Erro HTTP {$httpCode} na API IPTV. Resposta: {$response}");
            return [];
        }

        $dados = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Erro ao decodificar JSON da API IPTV: " . json_last_error_msg());
            return [];
        }

        return $dados;
    }

    // ---------------------------------------------------------------------
    // MÉTODOS PÚBLICOS PARA ACESSAR OS CONTEÚDOS
    // ---------------------------------------------------------------------

    /**
     * Obtém a lista de canais de TV ao vivo.
     * @return array
     */
    public function obterCanaisAoVivo(): array
    {
        $url = $this->construirUrl('get_live_streams');
        return $this->fazerRequisicao($url);
    }

    /**
     * Obtém a lista de filmes (VOD).
     * @return array
     */
    public function obterFilmes(): array
    {
        $url = $this->construirUrl('get_vod_streams');
        return $this->fazerRequisicao($url);
    }

    /**
     * Obtém a lista de séries.
     * @return array
     */
    public function obterSeries(): array
    {
        $url = $this->construirUrl('get_series');
        return $this->fazerRequisicao($url);
    }

    /**
     * Constrói a URL direta de reprodução de um Stream (Canal, Filme, Episódio).
     * @param string $idStream ID do stream (stream_id, vod_id, etc.).
     * @param string $tipo Tipo de stream (live, movie, series).
     * @param string $formato Formato da saída (ts, m3u8).
     * @return string A URL de reprodução.
     */
    public function construirUrlReproducao(string $idStream, string $tipo = 'live', string $formato = 'ts'): string
    {

        // --- AJUSTE PHP 7.4: Substituindo 'match' por 'switch' ---
        $tipoApi = 'live';
        switch (strtolower($tipo)) {
            case 'live':
            case 'tv':
                $tipoApi = 'live';
                break;
            case 'movie':
            case 'filme':
                $tipoApi = 'movie';
                break;
            case 'series':
            case 'serie':
                $tipoApi = 'series';
                break;
        }

        return "{$this->baseUrl}/{$tipoApi}/{$this->username}/{$this->password}/{$idStream}.{$formato}";
    }
}