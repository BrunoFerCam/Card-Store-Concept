<?php

declare(strict_types=1);



require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$games = [
    'magic'   => 'Magic: The Gathering',
    'pokemon' => 'Pokémon',
    'yugioh'  => 'Yu-Gi-Oh!',
];

$keyByGame = array_flip($games);

$pdo  = getConnection();
$rows = $pdo->query('SELECT game, code, name FROM editions ORDER BY game, id')->fetchAll();

$grouped = [];
foreach ($rows as $row) {
    $key = $keyByGame[$row['game']] ?? null;
    if ($key === null) {
        continue;
    }
    $grouped[$key][] = ['id' => $row['code'], 'name' => $row['name']];
}

$game = trim($_GET['game'] ?? '');

if ($game === '') {
    echo json_encode($grouped, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!isset($games[$game])) {
    http_response_code(404);
    echo json_encode(['error' => 'Card Game inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($grouped[$game] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
