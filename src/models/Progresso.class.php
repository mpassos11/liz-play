<?php
/**
 * MODELOPROGRESSO.PHP
 * * Lógica para ler e salvar o progresso de visualização do usuário.
 */

class ModeloProgresso
{

    private $diretorioUsuarios = COMMON_PATH . '/users/';

    public function __construct()
    {
        if (!is_dir($this->diretorioUsuarios)) {
            // Cria o diretório recursivamente com permissão 0777 (para fins de desenvolvimento)
            @mkdir($this->diretorioUsuarios, 0777, true);
        }
    }

    /**
     * Constrói o nome do arquivo de progresso para um dado usuário.
     * @param string $usuarioId ID do usuário.
     * @return string O caminho completo para o arquivo JSON.
     */
    private function obterCaminhoArquivo(string $usuarioId): string
    {
        $arquivo = 'user_' . $usuarioId . '_progresso.json';
        return $this->diretorioUsuarios . $arquivo;
    }

    /**
     * Obtém todos os dados de progresso de um usuário.
     * @param string $usuarioId ID do usuário logado.
     * @return array Retorna o array de progresso do usuário ou a estrutura básica se não existir.
     */
    public function obterProgresso(string $usuarioId): array
    {
        $caminho = $this->obterCaminhoArquivo($usuarioId);

        $progresso = carregarDadosJson($caminho);

        if ($progresso) {
            foreach ($progresso['progressos'] as $k => $item) {
                $progresso['progressos'][$k] = array_merge($item, $GLOBALS['IPTV']->obterDetalhes($item['tipo'], $item['content_id']));
            }
        }

        // Se não existir ou falhar, retorna a estrutura inicial
        return $progresso ?? ['user_id' => $usuarioId, 'progressos' => []];
    }

    /**
     * Busca o progresso de um item de conteúdo específico.
     * @param string $contentId ID do conteúdo (filme, episódio, etc.).
     * @return array|null Dados de progresso, ou null se não houver registro.
     */
    public function buscarProgressoPorId(string $contentId): ?array
    {
        $usuarioId = isset($_SESSION['perfil_id']) ? $_SESSION['perfil_id'] : null;
        if (!$usuarioId) {
            return null;
        }

        $progressoGeral = $this->obterProgresso($usuarioId);

        if (empty($progressoGeral['progressos'])) {
            return null;
        }

        foreach ($progressoGeral['progressos'] as $item) {
            if ($item['content_id'] === $contentId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Salva ou atualiza o progresso de visualização de um item.
     * @param string $usuarioId ID do usuário logado.
     * @param array $dadosProgresso Array com ['content_id', 'content_type', 'ultimo_tempo_assistido', 'duracao_total', ...].
     * @return bool True se o salvamento foi bem-sucedido.
     */
    public function salvarProgresso(string $usuarioId, array $dadosProgresso): bool
    {
        $caminho = $this->obterCaminhoArquivo($usuarioId);

        // 1. Carregar dados existentes
        $progressoExistente = $this->obterProgresso($usuarioId);
        $progressos = $progressoExistente['progressos'];
        $contentId = $dadosProgresso['content_id'];

        // 2. Lógica de atualização ou adição
        $encontrado = false;
        foreach ($progressos as $key => $item) {
            if ($item['content_id'] === $contentId) {
                // Atualiza o item mesclando os dados (útil se houver mais campos)
                $progressos[$key] = array_merge($item, $dadosProgresso);
                $encontrado = true;
                break;
            }
        }

        // Se não encontrou, adiciona como novo
        if (!$encontrado) {
            $progressos[] = $dadosProgresso;
        }

        // 3. Salva a estrutura completa de volta no arquivo
        $progressoExistente['progressos'] = $progressos;

        // A função salvarDadosJson() está em FuncoesGerais.php
        return salvarDadosJson($caminho, $progressoExistente);
    }
}