<?php

class ControladorConfig extends ControladorBase
{

    // Método que exibe o formulário de configurações
    public function index()
    {
        // Assume que as variáveis já foram carregadas pelo DotEnv
        $dados = [
            'iptv_m3u_url' => getenv('IPTV_M3U_URL'),
            'iptv_api_url' => getenv('IPTV_API_URL'),
            'iptv_username' => getenv('IPTV_USERNAME'),
            // Não exibimos a senha por segurança, mas o campo estará lá
            'mensagem' => $_SESSION['config_mensagem'] ?? null
        ];
        unset($_SESSION['config_mensagem']); // Limpa a mensagem após exibir

        $this->renderizar('config', $dados);
    }

    // Método que processa a atualização das credenciais
    public function atualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /config');
            exit;
        }

        // Pega os dados do POST (limpeza e validação seriam ideais em um projeto real)
        $novasConfigs = [
            'IPTV_M3U_URL' => $_POST['iptv_m3u_url'] ?? '',
            'IPTV_API_URL' => $_POST['iptv_api_url'] ?? '',
            'IPTV_USERNAME' => $_POST['iptv_username'] ?? '',
            'IPTV_PASSWORD' => $_POST['iptv_password'] ?? '' // Pegamos a senha nova (se preenchida)
        ];

        // --- Lógica de Atualização do .env ---
        $caminhoEnv = APP_ROOT . '/.env'; // Ajuste o caminho conforme sua estrutura

        if (file_exists($caminhoEnv)) {
            $conteudo = file_get_contents($caminhoEnv);

            foreach ($novasConfigs as $key => $value) {
                // Se a senha for deixada em branco, NÃO atualiza a senha atual
                if ($key === 'IPTV_PASSWORD' && empty($value)) {
                    continue;
                }

                $value = trim($value);

                // Trata as aspas para valores que contenham espaços ou caracteres especiais
                $valorFormatado = (strpos($value, ' ') !== false || empty($value)) ? '"' . $value . '"' : $value;

                // Expressão Regular para encontrar e substituir a linha
                $conteudo = preg_replace(
                    "/^$key=.*$/m",
                    "$key=$valorFormatado",
                    $conteudo
                );
            }

            file_put_contents($caminhoEnv, $conteudo);

            // Recarrega as variáveis de ambiente (necessário para a sessão atual)
            carregarEnv($caminhoEnv);

            $_SESSION['config_mensagem'] = 'Configurações IPTV atualizadas com sucesso!';

        } else {
            $_SESSION['config_mensagem'] = 'Erro: Arquivo .env não encontrado.';
        }

        header('Location: /config');
        exit;
    }
}