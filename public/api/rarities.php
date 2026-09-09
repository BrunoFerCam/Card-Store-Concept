<?php

declare(strict_types=1);



require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$games = [
    'magic'   => 'Magic: The Gathering',
    'pokemon' => 'Pokémon',
    'yugioh'  => 'Yu-Gi-Oh!',
];

$key = trim($_GET['game'] ?? '');

if (!isset($games[$key])) {
    http_response_code(404);
    echo json_encode(['error' => 'Card Game inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo  = getConnection();
$stmt = $pdo->prepare('SELECT id, name, color FROM rarities WHERE game = :game ORDER BY name, id');
$stmt->execute([':game' => $games[$key]]);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
