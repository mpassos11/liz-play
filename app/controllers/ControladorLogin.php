<?php
/**
 * CONTROLADORLOGIN.PHP
 * Lida com o processo de login e logout.
 */

class ControladorLogin extends ControladorBase
{

    private ModeloAuth $modeloAuth;

    public function __construct()
    {
        // Carrega o modelo de autenticação
        $this->modeloAuth = new ModeloAuth();
    }

    /**
     * Exibe a tela de login.
     */
    public function index()
    {
        if (ModeloAuth::estaAutenticado()) {
            // Se já está logado, redireciona para a home ou TV
            header('Location: ' . getenv('URL_BASE') . '/home');
            exit;
        }

        // Carrega o formulário de login
        $this->renderizar('login');
    }

    /**
     * Processa o envio do formulário de login.
     */
    public function logar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . getenv('URL_BASE') . '/login');
            exit;
        }

        $usuario = $_POST['username'] ?? '';
        $senha = $_POST['password'] ?? '';

        if ($this->modeloAuth->autenticar($usuario, $senha)) {
            // Sucesso no login, redireciona para a tela principal
            header('Location: ' . getenv('URL_BASE') . '/home');
            exit;
        } else {
            // Falha, recarrega a tela de login com erro
            $dados = ['erro' => 'Usuário ou senha incorretos.'];
            $this->renderizar('login', $dados);
        }
    }

    /**
     * Encerra a sessão do usuário.
     */
    public function sair()
    {
        ModeloAuth::logout();
        header('Location: ' . getenv('URL_BASE') . '/login');
        exit;
    }
}