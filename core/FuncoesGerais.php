<?php
/**
 * FUNCOESGERAIS.PHP
 * * Contém funções utilitárias globais para manipulação de arquivos JSON e outras tarefas.
 */

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

// -------------------------------------------------------------------------
// Funções Auxiliares de Visualização (ControladorBase pode usar)
// -------------------------------------------------------------------------

/**
 * Renderiza uma View, injetando dados.
 * @param string $nomeView Nome do arquivo da View (ex: 'home/index').
 * @param array $dados Dados a serem injetados na View.
 */
function renderizarView(string $nomeView, array $dados = []): void
{

    // Converte as chaves do array de dados em variáveis (ex: $dados['filmes'] vira $filmes)
    // Isso torna o acesso aos dados mais limpo na View.
    extract($dados);

    $caminhoView = APP_ROOT . '/app/Views/' . $nomeView . '.php';

    if (file_exists($caminhoView)) {
        // Inclui o arquivo, que agora tem acesso às variáveis extraídas ($filmes, $progresso, etc.)
        require $caminhoView;
    } else {
        // Log ou erro: View não encontrada
        http_response_code(500);
        echo "Erro: View '{$nomeView}' não encontrada.";
    }
}

function baseUrl(string $path = ''): string
{
    return getenv('URL_BASE') . $path;
}

?>