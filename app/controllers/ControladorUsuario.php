<?php
// O usuário precisa estar logado para saber seu ID. Aqui usaremos um ID de exemplo.
define('USUARIO_LOGADO_ID', '12345');

class ControladorUsuario extends ControladorBase
{
    private $modeloProgresso;

    public function __construct()
    {
        $this->modeloProgresso = new ModeloProgresso();
    }

    public function salvarProgresso(): void
    {
        // Verifica se a requisição é um POST e tem dados JSON
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
            return;
        }

        $dados = json_decode(file_get_contents('php://input'), true);

        if (!isset($dados['content_id'], $dados['tempo'], $dados['duracao'], $dados['tipo_conteudo'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            return;
        }

        $dadosParaSalvar = [
            'content_id' => $dados['content_id'],
            'content_type' => $dados['tipo_conteudo'],
            'ultimo_tempo_assistido' => (int)$dados['tempo'],
            'duracao_total' => (int)$dados['duracao'],
            'ultima_atualizacao' => date('c')
        ];

        $sucesso = $this->modeloProgresso->salvarProgresso(USUARIO_LOGADO_ID, $dadosParaSalvar);

        if ($sucesso) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Progresso salvo.']);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar o arquivo de progresso.']);
        }
    }
}

?>