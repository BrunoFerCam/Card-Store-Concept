<?php

declare(strict_types=1);



require_once __DIR__ . '/../../src/Controllers/AuthController.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new AuthController();
$controller->loginJson();
