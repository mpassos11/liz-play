<?php

class API
{
    private IPTV $iptv;
    private ModeloProgresso $modeloProgresso;
    private $userId;

    public function __construct()
    {
        $this->iptv = new IPTV();
        $this->modeloProgresso = new ModeloProgresso();
        $this->checkAuth();
    }

    private function checkAuth()
    {
        $token = $_ENV['API_TOKEN'];
        $receivedToken = '';

        // Tenta obter o token do cabeçalho de autorização (Bearer)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $receivedToken = $matches[1];
        }

        // Se não estiver no cabeçalho, tenta obter dos dados POST
        if (empty($receivedToken) && isset($_POST['api_token'])) {
            $receivedToken = $_POST['api_token'];
        }

        if (empty($receivedToken) || $receivedToken !== $token) {
            $this->sendResponse(['error' => 'Acesso não autorizado'], 401);
        }

        $this->userId = $_POST['user_id'] ?? null;
        if (empty($this->userId)) {
            $this->sendResponse(['error' => 'USER_ID é obrigatório'], 400);
        }
    }

    private function sendResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function conteudos($tipoConteudo)
    {
        $arquivo = $this->getArquivoPorTipo($tipoConteudo);
        if (!$arquivo) {
            $this->sendResponse(['error' => "Categoria '{$tipoConteudo}' não encontrada."], 404);
        }

        $conteudo = $this->iptv->obterPorTipo($arquivo);
        $conteudoTransformado = $this->transformarConteudo($tipoConteudo, $conteudo);
        $this->sendResponse($conteudoTransformado);
    }

    public function assistir($tipo, $id)
    {
        $stream = $this->iptv->obterDetalhes($tipo, $id);
        if (empty($stream)) {
            $this->sendResponse(['error' => 'Conteúdo não encontrado'], 404);
        }

        $stream['tipo'] = $tipo;
        $episodios = ($tipo === 'series') ? $this->iptv->obterEpisodiosSeries($id) : [];
        $progresso = $this->modeloProgresso->buscarProgressoPorId($id) ?: [];

        if ($tipo === 'tv') {
            $stream['stream_link'] = str_replace('.ts', '.m3u8', $stream['stream_link']);
        }

        $this->sendResponse([
            'stream' => $stream,
            'episodios' => $episodios,
            'progresso' => $progresso,
        ]);
    }

    public function episodiosSeries($serieID)
    {
        $episodios = $this->iptv->obterEpisodiosSeries($serieID);
        if (empty($episodios)) {
            $this->sendResponse(['error' => 'Episódios não encontrados'], 404);
        }

        $retorno = ['seasons' => []];
        foreach ($episodios as $temporada => $eps) {
            $retorno['seasons'][] = [
                'id' => $temporada,
                'name' => "Temporada {$temporada}",
                'episodes' => $eps
            ];
        }
        $this->sendResponse($retorno);
    }

    public function salvarProgresso()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['error' => 'Método não permitido'], 405);
        }

        $dados = $_POST;
        if (!isset($dados['content_id'], $dados['tempo'], $dados['completo'])) {
            $this->sendResponse(['error' => 'Dados de progresso incompletos'], 400);
        }

        $dadosParaSalvar = [
            'content_id' => $dados['content_id'],
            'ultimo_tempo_assistido' => (int)$dados['tempo'],
            'completo' => $dados['completo'],
            'ultima_atualizacao' => date('c'),
            'tipo' => $dados['tipo'],
        ];

        if (isset($dados['ep_id'])) {
            $dadosParaSalvar['ep_id'] = $dados['ep_id'];
            $dadosParaSalvar['tp_id'] = $dados['tp_id'];
        }

        $sucesso = $this->modeloProgresso->salvarProgresso($this->userId, $dadosParaSalvar);

        if ($sucesso) {
            $this->sendResponse(['sucesso' => true, 'mensagem' => 'Progresso salvo com sucesso.']);
        } else {
            $this->sendResponse(['sucesso' => false, 'mensagem' => 'Erro ao salvar o progresso.'], 500);
        }
    }
    
    public function ultimosAssistidos()
    {
        $progresso = $this->modeloProgresso->obterProgresso($this->userId);
        $this->sendResponse($progresso['progressos'] ?? []);
    }

    private function getArquivoPorTipo($tipo)
    {
        switch ($tipo) {
            case 'filmes': return 'filmes.json';
            case 'series': return 'series.json';
            case 'tv': return 'tv.json';
            default: return null;
        }
    }

    private function transformarConteudo($tipo, $conteudo)
    {
        if ($tipo === 'tv') {
            return $this->iptv->ordenarCanaisTV($conteudo);
        }

        $conteudoAExibir = [];
        $categorias = $this->iptv->obterPorTipo('categorias.json');
        shuffle($conteudo);

        foreach ($conteudo as $item) {
            $categoria = array_search($item['category_id'], array_column($categorias, 'category_id'));
            if ($categoria !== false) {
                $conteudoAExibir[$categorias[$categoria]['category_name']][] = $item;
            }
        }

        return $conteudoAExibir;
    }
}
