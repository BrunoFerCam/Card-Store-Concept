<?php

declare(strict_types=1);

require_once __DIR__ . '/Auth.php';


final class Flash
{
    
    public static function set(string $type, string $message): void
    {
        Auth::start();
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    
    public static function get(): ?array
    {
        Auth::start();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
