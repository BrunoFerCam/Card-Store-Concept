<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';


final class Rarity
{
    public static function all(): array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT * FROM rarities ORDER BY game, name, id');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function byGame(string $game): array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT id, name, color FROM rarities WHERE game = :game ORDER BY name, id');
        $stmt->execute([':game' => $game]);

        return $stmt->fetchAll();
    }

    public static function existsForGame(string $game, string $name): bool
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'SELECT 1 FROM rarities WHERE game = :game AND name = :name LIMIT 1'
        );
        $stmt->execute([':game' => $game, ':name' => $name]);

        return $stmt->fetchColumn() !== false;
    }

    
    public static function colorMap(): array
    {
        $map = [];
        foreach (self::all() as $rarity) {
            $map[$rarity['game']][$rarity['name']] = $rarity['color'];
        }
        return $map;
    }

    public static function create(string $game, string $name, string $color): void
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('INSERT INTO rarities (game, name, color) VALUES (:game, :name, :color)');
        $stmt->execute([':game' => $game, ':name' => $name, ':color' => $color]);
    }

    public static function delete(int $id): void
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('DELETE FROM rarities WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
