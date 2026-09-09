<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';


final class Edition
{
    public static function all(): array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT * FROM editions ORDER BY game, name, id');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function findByGameAndCode(string $game, string $code): ?array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'SELECT code, name FROM editions WHERE game = :game AND code = :code LIMIT 1'
        );
        $stmt->execute([':game' => $game, ':code' => $code]);
        $edition = $stmt->fetch();

        return $edition === false ? null : $edition;
    }

    public static function create(string $game, string $code, string $name): void
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO editions (game, code, name) VALUES (:game, :code, :name)'
        );
        $stmt->execute([':game' => $game, ':code' => $code, ':name' => $name]);
    }

    public static function delete(int $id): void
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('DELETE FROM editions WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
