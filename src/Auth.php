<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';


final class Auth
{
    
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }
    }

    
    public static function attempt(string $username, string $password): bool
    {
        self::start();

        $pdo = getConnection();

        $stmt = $pdo->prepare(
            'SELECT id, username, password_hash
               FROM users
              WHERE username = :username
              LIMIT 1'
        );
        $stmt->execute([':username' => $username]);

        $user = $stmt->fetch();

        if ($user === false) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['username'] = $user['username'];

        return true;
    }

    
    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    
    public static function user(): ?array
    {
        self::start();

        if (!self::check()) {
            return null;
        }

        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
        ];
    }

    
    public static function requireLogin(string $redirectTo = 'login.php'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    
    public static function logout(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
