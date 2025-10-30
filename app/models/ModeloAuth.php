<?php
/**
 * MODELOAUTH.PHP
 * Lógica para autenticação de usuário simples.
 */

class ModeloAuth
{

    /**
     * Tenta autenticar o usuário com base nas credenciais do .env
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function autenticar(string $username, string $password): bool
    {
        // Pega as credenciais do ambiente
        $expectedUser = getenv('AUTH_USERNAME');
        $expectedPass = getenv('AUTH_PASSWORD');

        // Compara as credenciais
        if ($username === $expectedUser && $password === $expectedPass) {
            // Inicia a sessão (se já não estiver iniciada)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Registra a sessão de login
            $_SESSION['autenticado'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['ultima_atividade'] = time();

            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário está autenticado e se a sessão não expirou.
     * @return bool
     */
    public static function estaAutenticado(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
            return false;
        }

        // Verifica o tempo de expiração da sessão (lifetime)
        $lifetimeMinutos = (int)getenv('AUTH_LIFETIME');
        $lifetimeSegundos = $lifetimeMinutos * 60;

        if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > $lifetimeSegundos)) {
            // Sessão expirou
            self::logout();
            return false;
        }

        // Atualiza o tempo de atividade para estender a sessão
        $_SESSION['ultima_atividade'] = time();

        return true;
    }

    /**
     * Encerra a sessão do usuário.
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        session_destroy();
    }
}