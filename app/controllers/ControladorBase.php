<?php
/**
 * CONTROLADORBASE.PHP
 * * Define a estrutura básica e métodos comuns para todos os controladores.
 */

class ControladorBase
{

    /**
     * @var string Diretório base para as Views (app/Views/).
     */
    protected $diretorioBaseViews = APP_ROOT . '/app/Views/';

    /**
     * Renderiza uma View, aplicando o layout (cabeçalho e rodapé).
     * * @param string $nomeView Nome do arquivo da View (ex: 'home/index').
     * @param array $dados Dados a serem injetados na View.
     */
    protected function renderizar(string $nomeView, array $dados = []): void
    {

        // Converte as chaves do array de dados em variáveis para uso na View
        extract($dados);

        // Define os caminhos das partes da View
        $caminhoCabecalho = $this->diretorioBaseViews . 'layout/cabecalho.php';
        $caminhoView = $this->diretorioBaseViews . $nomeView . '.php';
        $caminhoRodape = $this->diretorioBaseViews . 'layout/rodape.php';

        // 1. Incluir Cabeçalho
        if (file_exists($caminhoCabecalho)) {
            require $caminhoCabecalho;
        }

        // 2. Incluir o Conteúdo da View
        if (file_exists($caminhoView)) {
            require $caminhoView;
        } else {
            // Em caso de View não encontrada, gera um erro 404/500
            http_response_code(404);
            echo "<h1>Erro 404</h1><p>A View '{$nomeView}' não foi encontrada.</p>";
            return;
        }

        // 3. Incluir Rodapé
        if (file_exists($caminhoRodape)) {
            require $caminhoRodape;
        }
    }
}