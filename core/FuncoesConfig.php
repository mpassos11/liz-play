<?php
/**
 * FUNCOESCONFIG.PHP
 * * Funções para carregar variáveis de ambiente de um arquivo .env.
 * * Essencial para separar configurações sensíveis do código.
 */

/**
 * Carrega as variáveis de ambiente do arquivo .env e as define no $_ENV e getenv().
 * @param string $caminhoArquivo O caminho completo para o arquivo .env.
 */
function carregarEnv(string $caminhoArquivo): void
{
    if (!file_exists($caminhoArquivo)) {
        error_log("Aviso: Arquivo .env não encontrado em: " . $caminhoArquivo);
        return;
    }

    // FILE_IGNORE_EMPTY_LINES | FILE_SKIP_EMPTY_LINES é compatível com PHP 7.4
    $linhas = file($caminhoArquivo, FILE_SKIP_EMPTY_LINES);

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