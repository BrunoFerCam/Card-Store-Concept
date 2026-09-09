<?php

declare(strict_types=1);



require_once __DIR__ . '/../src/Controllers/AuthController.php';

header('Content-Type: text/html; charset=utf-8');

$controller = new AuthController();

if (Auth::check() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    header('Location: index.php?login=1');
    exit;
}
