<?php

class ModeloProgresso extends ModeloBase
{
    private $diretorio = APP_ROOT . '/users/';

    public function obterProgresso(string $usuarioId): array
    {
        $arquivo = 'user_' . $usuarioId . '_progresso.json';
        $caminho = $this->diretorio . $arquivo;

        return carregarDadosJson($caminho) ?? ['user_id' => $usuarioId, 'progressos' => []];
    }

    public function salvarProgresso(string $usuarioId, array $dadosProgresso): bool
    {
        $arquivo = 'user_' . $usuarioId . '_progresso.json';
        $caminho = $this->diretorio . $arquivo;

        // 1. Carregar dados existentes
        $progressoExistente = $this->obterProgresso($usuarioId);

        // 2. Lógica de atualização (similar ao exemplo anterior)
        $encontrado = false;
        foreach ($progressoExistente['progressos'] as $key => $item) {
            if ($item['content_id'] === $dadosProgresso['content_id']) {
                $progressoExistente['progressos'][$key] = array_merge($item, $dadosProgresso);
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            $progressoExistente['progressos'][] = $dadosProgresso;
        }

        // 3. Salvar
        return salvarDadosJson($caminho, $progressoExistente);
    }
}

?>