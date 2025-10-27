<?php
/**
 * MODELOBASE.PHP
 * * Define a estrutura base para todos os modelos da aplicação.
 * * Garante acesso a funções gerais (como carregar JSON).
 */

class ModeloBase
{

    /**
     * @var string Diretório base onde os arquivos de conteúdo JSON estão localizados.
     */
    protected $diretorioConteudo = APP_ROOT . '/content/';

    /**
     * @var string Diretório base onde os arquivos de progresso do usuário JSON estão localizados.
     */
    protected $diretorioUsuarios = APP_ROOT . '/users/';

    public function __construct()
    {
        // Garantir que as funções globais (FuncoesGerais.php) estejam disponíveis
        // Embora FuncoesGerais.php seja incluído em index.php, é bom manter a clareza.
    }

    /**
     * Carrega dados de um arquivo JSON de conteúdo.
     * @param string $nomeArquivo O nome do arquivo JSON (ex: 'filmes.json').
     * @return array Array de dados ou array vazio se falhar.
     */
    protected function carregarConteudoJson(string $nomeArquivo): array
    {
        // A função carregarDadosJson() está em FuncoesGerais.php
        $dados = carregarDadosJson($this->diretorioConteudo . $nomeArquivo);
        return $dados ?? [];
    }
}