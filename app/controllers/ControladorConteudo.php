<?php
/**
 * CONTROLADORCONTEUDO.PHP
 * * Gerencia a lógica de visualização e player.
 */

class ControladorConteudo extends ControladorBase
{
    private $modeloConteudo;
    private $modeloProgresso;

    private const USUARIO_ID_EXEMPLO = '12345';

    public function __construct()
    {
        $this->modeloConteudo = new ModeloConteudo();
        $this->modeloProgresso = new ModeloProgresso();
    }

    /**
     * Rota: /assistir/{tipo}/{id} (ex: /assistir/filme/m1001)
     * Prepara a página do player, injetando o progresso inicial.
     * * @param string $tipo Tipo de conteúdo ('filme', 'serie', 'tv').
     * @param string $id ID do item (m1001, s2001e01, tv4001).
     */
    public function assistir(string $tipo, string $id): void
    {

        // 1. Carregar Conteúdo (pode precisar de lógica de busca mais complexa no Model)
        $detalhesConteudo = $this->modeloConteudo->obterDetalhes($tipo, $id);

        if (!$detalhesConteudo) {
            http_response_code(404);
            $this->renderizar('erro/404', ['mensagem' => 'Conteúdo não encontrado.']);
            return;
        }

        // 2. Carregar Progresso do Usuário
        $progressoUsuario = $this->modeloProgresso->buscarProgressoPorId(self::USUARIO_ID_EXEMPLO, $id);

        // Define o tempo inicial de reprodução
        $tempoInicial = $progressoUsuario['ultimo_tempo_assistido'] ?? 0;

        $dados = [
            'tituloPagina' => 'Assistindo: ' . $detalhesConteudo['title'],
            'conteudo' => $detalhesConteudo,
            'tempoInicial' => $tempoInicial,
            'usuarioId' => self::USUARIO_ID_EXEMPLO
        ];

        // 3. Renderizar o Player
        $this->renderizar('conteudo/assistir', $dados);
    }
}