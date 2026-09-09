<?php

declare(strict_types=1);

require_once __DIR__ . '/../Auth.php';


final class AuthController
{
    
    public function showLoginForm(?string $error = null, string $username = ''): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    
    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->showLoginForm('Informe usuário e senha.', $username);
            return;
        }

        if (Auth::attempt($username, $password)) {
            header('Location: admin/index.php');
            exit;
        }

        $this->showLoginForm('Usuário ou senha inválidos.', $username);
    }

    
    public function loginJson(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'message' => 'Método não permitido.']);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            echo json_encode(['ok' => false, 'message' => 'Informe usuário e senha.']);
            return;
        }

        if (Auth::attempt($username, $password)) {
            echo json_encode(['ok' => true]);
            return;
        }

        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Usuário ou senha inválidos.']);
    }

    
    public function logout(): void
    {
        Auth::logout();

        header('Location: index.php');
        exit;
    }
}
