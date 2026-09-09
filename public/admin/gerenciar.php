<?php

declare(strict_types=1);



require_once __DIR__ . '/../../src/Controllers/ManageController.php';

Auth::requireLogin('../login.php');

$controller = new ManageController();
$controller->index();
