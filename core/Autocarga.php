<?php
/**
 * AUTOCARGA.PHP
 * * Responsável pelo carregamento automático das classes da aplicação (Controllers, Models, core).
 */

spl_autoload_register(function ($nomeClasse) {
    // 1. Define os diretórios base onde as classes estão localizadas (em inglês, conforme o pedido)
    $diretorios = [
        'app/Controllers/',
        'app/Models/',
        'core/'
    ];

    // 2. Itera sobre os diretórios para encontrar o arquivo da classe
    foreach ($diretorios as $diretorio) {
        // Constrói o caminho completo do arquivo
        $caminhoArquivo = APP_ROOT . '/' . $diretorio . $nomeClasse . '.php';

        // Verifica se o arquivo existe e o inclui
        if (file_exists($caminhoArquivo)) {
            require_once $caminhoArquivo;
            // Se a classe foi encontrada e incluída, para a busca
            return;
        }
    }

    // Opcional: Para depuração, se uma classe não for encontrada
    // echo "Aviso: Classe '{$nomeClasse}' não encontrada em nenhum diretório. Caminho buscado: " . APP_ROOT . "/...\n";
});

// Nota: APP_ROOT deve estar definido em public/index.php antes de incluir este arquivo.
// Exemplo: define('APP_ROOT', dirname(__DIR__));