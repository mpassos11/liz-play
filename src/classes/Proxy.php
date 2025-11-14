<?php

class Proxy
{
    public function index()
    {
        // 1. Obter a URL de destino a partir do parâmetro 'url'
        if (!isset($_GET['url'])) {
            http_response_code(400);
            echo "Erro: O parâmetro 'url' é obrigatório.";
            return;
        }

        $remoteUrl = $_GET['url'];

        // 2. Validar a URL para segurança básica
        if (!filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo "Erro: URL inválida fornecida.";
            return;
        }

        // 3. Iniciar a sessão cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Não retorna, mas exibe diretamente
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // Segue redirecionamentos
        curl_setopt($ch, CURLOPT_HEADER, false);          // Não inclui cabeçalhos na saída do corpo
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Liz-Play-Proxy/1.0');

        // 4. Função para encaminhar os cabeçalhos da resposta remota para o cliente
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) {
            // Encaminha o cabeçalho para o cliente, exceto os que não queremos duplicar
            if (!preg_match('/^(Transfer-Encoding|Content-Length|Set-Cookie)/i', $header)) {
                header($header);
            }
            return strlen($header);
        });

        // 5. Executar a requisição cURL (o conteúdo será transmitido diretamente para a saída)
        curl_exec($ch);

        // 6. Verificar por erros e fechar a conexão
        if (curl_errno($ch)) {
            // Se os cabeçalhos ainda não foram enviados, podemos enviar um código de erro
            if (!headers_sent()) {
                http_response_code(502); // Bad Gateway
                echo 'Erro no proxy cURL: ' . curl_error($ch);
            }
        }

        curl_close($ch);
    }
}
