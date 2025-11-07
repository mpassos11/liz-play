<?php

define('COMMON_PATH', __DIR__ . '/../');
define('COMMON_URL', getBaseCommonURL());

function carregarEnv()
{
    $caminhoEnv = COMMON_PATH . '/.env';
    $linhas = file($caminhoEnv, FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        // --- AJUSTE PHP 7.4: Substituindo str_starts_with ---
        // Ignorar comentários (linhas que começam com #)
        if (substr($linha, 0, 1) === '#') {
            continue;
        }

        // --- AJUSTE PHP 7.4: Substituindo str_contains ---
        // Verifica se a linha contém o sinal de igual
        if (strpos($linha, '=') !== false) {
            // Usa explode (compatível)
            list($chave, $valor) = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);

            // Remove aspas
            $valor = trim($valor, "\"' \t\n\r\0\x0B");

            // Define a variável de ambiente
            putenv("{$chave}={$valor}");
            $_ENV[$chave] = $valor;
        }
    }
}

function atualizarConfigs($novasConfigs)
{
    $caminhoEnv = COMMON_PATH . '/.env';
    $conteudo = file_get_contents($caminhoEnv);
    foreach ($novasConfigs as $key => $value) {
        // Se a senha for deixada em branco, NÃO atualiza a senha atual
        if ($key === 'IPTV_PASSWORD' && empty($value)) {
            continue;
        }

        $value = trim($value);

        // Trata as aspas para valores que contenham espaços ou caracteres especiais
        $valorFormatado = (strpos($value, ' ') !== false || empty($value)) ? '"' . $value . '"' : $value;

        // Expressão Regular para encontrar e substituir a linha
        $conteudo = preg_replace(
            "/^$key=.*$/m",
            "$key=$valorFormatado",
            $conteudo
        );
    }

    file_put_contents($caminhoEnv, $conteudo);
    carregarEnv();
}

/**
 * Carrega e decodifica um arquivo JSON.
 * @param string $caminhoArquivo O caminho absoluto ou relativo (com base em APP_ROOT) para o arquivo JSON.
 * @return array|null Dados decodificados como array associativo, ou null em caso de falha.
 */
function carregarDadosJson(string $caminhoArquivo): ?array
{

    // Se o caminho não for absoluto, assume que é relativo a APP_ROOT
    $caminhoCompleto = $caminhoArquivo;

    if (!file_exists($caminhoCompleto)) {
        // Log ou tratamento de erro: Arquivo JSON não encontrado
        return null;
    }

    // Tenta ler o conteúdo do arquivo
    $conteudo = @file_get_contents($caminhoCompleto);

    if ($conteudo === false) {
        // Log ou tratamento de erro: Falha ao ler o arquivo
        return null;
    }

    // Decodifica o JSON para um array associativo (true)
    $dados = json_decode($conteudo, true);

    // Verifica se a decodificação foi bem-sucedida
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Log ou tratamento de erro: JSON inválido
        return null;
    }

    return $dados;
}

/**
 * Salva e codifica dados em um arquivo JSON.
 * Garante que o diretório de destino exista e usa travamento de arquivo para atomicidade.
 * * @param string $caminhoArquivo O caminho absoluto ou relativo para o arquivo JSON.
 * @param array $dados Os dados a serem salvos.
 * @return bool True se a escrita for bem-sucedida, False caso contrário.
 */
function salvarDadosJson(string $caminhoArquivo, array $dados): bool
{
    // 2. Codifica os dados em JSON (JSON_PRETTY_PRINT para legibilidade durante o desenvolvimento)
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        // Log ou tratamento de erro: Falha na codificação JSON
        return false;
    }

    // 3. Escreve o conteúdo no arquivo de forma segura (LOCK_EX)
    // O LOCK_EX evita que múltiplos processos escrevam no arquivo simultaneamente,
    // prevenindo corrupção do JSON, o que é crucial para o progresso do usuário.
    return file_put_contents($caminhoArquivo, $json, LOCK_EX) !== false;
}

function getBaseCommonURL()
{
    if (php_sapi_name() == "cli") {
        return '';
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    return $protocol . $domainName . $path . '/';
}

function redirect($url)
{
    header('Location: ' . COMMON_URL . $url);
    exit;
}

function view($view, $data = [], $js = [], $css = [])
{
    if (!empty($data)) {
        extract($data);
    }

    $js = array_merge([
        'bootstrap',
        'fontawesome.min',
        'jquery-3.6.0.min',
        'jquery.lazy.min',
        'slick',
        'app',
    ], $js);

    $css = array_merge([
        'bootstrap',
        'slick',
        'slick-theme',
        'style'
    ], $css);

    include_once COMMON_PATH . 'views/partials/header.php';
    include_once COMMON_PATH . 'views/' . $view . '.php';
    include_once COMMON_PATH . 'views/partials/footer.php';
    die();
}

function api($data, $code = 200)
{
    http_response_code($code);
    die(json_encode($data));
}

function get_post($name)
{
    return $_POST[$name] ?? '';
}

function base_url($url = '')
{
    return COMMON_URL . $url;
}

function base_path($path)
{
    return COMMON_PATH . $path;
}

function print_rr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function ehCelular(): bool
{
    $mobile_browser = '0';

    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
    }

    if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        $mobile_browser++;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');

    if (in_array($mobile_ua,$mobile_agents)) {
        $mobile_browser++;
    }

    if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
        $mobile_browser++;
    }

    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
        $mobile_browser = 0;
    }

    return $mobile_browser > 0;
}
