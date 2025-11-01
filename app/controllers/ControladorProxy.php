<?php

class ControladorProxy extends ControladorBase
{

    private $modeloApi;

    public function __construct()
    {
        $this->modeloApi = new ModeloApiIptv();
    }

    public function stream()
    {
        // memoria
        ini_set('memory_limit', '-1');
        // tempo execucao
        ini_set('max_execution_time', '0');

        // --- LÓGICA DE URL ---
        if (!empty($_GET['url_proxy'])) {
            $urlInsegura = base64_decode($_GET['url_proxy']);
        } else {
            $streamId = $_GET['id'] ?? null;
            $tipo = $_GET['tipo'] ?? 'live';
            $formato = $_GET['formato'] ?? 'm3u8';

            if (!$streamId) {
                http_response_code(404);
                exit;
            }

            $urlInsegura = $this->modeloApi->construirUrlReproducao($streamId, $tipo, $formato);
        }

        if (empty($urlInsegura)) {
            http_response_code(500);
            echo "Erro: URL do stream inválida.";
            exit;
        }

        // ----------------------------------------------------
        // FASE DE REQUISIÇÃO C-URL (BACKEND FETCH)
        // ----------------------------------------------------

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlInsegura);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // sem timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Define como false em vez de 0

        $conteudo = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);

        error_log(print_r([
            $conteudo,
            curl_getinfo($ch),
            $error,
            $urlInsegura,
            $_GET,
            $_REQUEST,
            $_SERVER
        ], true));

        curl_close($ch);

        if ($httpCode !== 200) {
            // Se falhar, registra o erro e retorna Bad Gateway
            error_log("Proxy Falhou: URL {$urlInsegura} retornou erro {$httpCode}. cURL Error: " . $error);
            http_response_code(502);
            echo "Falha ao carregar o stream. (Erro do servidor de origem)";
            exit;
        }

        // ----------------------------------------------------
        // FASE DE REESCRITA E ENTREGA (REWRITE & DELIVERY)
        // ----------------------------------------------------

        $mimeType = $contentType;

        // *** CORRE��O PARA GARANTIR O MIME TYPE CORRETO (Resolve "Navegador n�o suporta") ***
        $urlPath = parse_url($urlInsegura, PHP_URL_PATH);
        $extension = pathinfo($urlPath, PATHINFO_EXTENSION);

        // Se o cURL n�o retornou um MIME type �til, tenta deduzir pela extens�o
        if (empty($mimeType) || strpos($mimeType, 'text/plain') !== false) {
            switch (strtolower($extension)) {
                case 'm3u8':
                case 'm3u':
                    $mimeType = 'application/x-mpegURL';
                    break;
                case 'ts':
                    $mimeType = 'video/mp2t'; // MIME Type correto para Transport Streams
                    break;
                case 'aac':
                    $mimeType = 'audio/aac';
                    break;
                default:
                    $mimeType = 'application/octet-stream'; // Default para arquivos bin�rios desconhecidos
                    break;
            }
        }
        // *********************************************************************************

        // Define o Content-Type: essencial para o player
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . strlen($conteudo));

        // Headers Anti-Cache
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Se for um M3U8 (playlist), PRECISAMOS reescrever os links internos
        if (strpos($mimeType, 'mpegurl') !== false || strpos($mimeType, 'm3u8') !== false || strtolower($extension) === 'm3u8') {

            $urlPartes = parse_url($urlInsegura);
            $pathInseguroCompleto = dirname($urlInsegura) . '/';

            // 1. Substitui a URL base para segmentos absolutos
            $conteudo = str_replace(
                $pathInseguroCompleto,
                URL_BASE . '/proxy?url_proxy=',
                $conteudo
            );

            // 2. Substitui URLs relativas (nomes de arquivos de segmento)
            $conteudo = preg_replace_callback(
                '/([a-zA-Z0-9\._-]+)\.(ts|aac|mp4|m3u8)/',
                function ($matches) use ($pathInseguroCompleto) {
                    $segmentoUrlInsegura = $pathInseguroCompleto . $matches[0];
                    $segmentoUrlSegura = baseUrl() . '/proxy?url_proxy=' . base64_encode($segmentoUrlInsegura);
                    return $segmentoUrlSegura;
                },
                $conteudo
            );

        }

        echo $conteudo;
        exit;
    }
}