<?php
/**
 * CONTROLADORUSUARIO.PHP
 * * Gerencia a lógica de sessões e progresso do usuário (geralmente via API/AJAX).
 */

class ControladorUsuario extends ControladorBase
{
    private $modeloProgresso;

    public function __construct()
    {
        $this->modeloProgresso = new ModeloProgresso();
    }

    /**
     * Rota: /api/salvar-progresso (Método: POST)
     * Recebe a requisição AJAX do player para salvar o tempo assistido.
     */
    public function salvarProgresso(): void
    {

        // 1. Configura a resposta como JSON
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
            return;
        }

        // Recebe e decodifica o corpo da requisição JSON (do player_tracking.js)
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!isset($dados['content_id'], $dados['tempo'], $dados['duracao'], $dados['tipo_conteudo'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados de progresso incompletos']);
            return;
        }

        // 2. Prepara os dados para o Model
        $dadosParaSalvar = [
            'content_id' => $dados['content_id'],
            'content_type' => $dados['tipo_conteudo'],
            'ultimo_tempo_assistido' => (int)$dados['tempo'],
            'duracao_total' => (int)$dados['duracao'],
            'ultima_atualizacao' => date('c')
        ];

        // 3. Executa a lógica de salvamento no Model
        $sucesso = $this->modeloProgresso->salvarProgresso(USUARIO_LOGADO_ID, $dadosParaSalvar);

        // 4. Retorna a resposta ao Front-end
        if ($sucesso) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Progresso salvo com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao salvar o progresso.']);
        }
    }
}