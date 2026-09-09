<?php

declare(strict_types=1);



require_once __DIR__ . '/../src/Controllers/CardController.php';

$controller = new CardController();
$controller->catalog();
