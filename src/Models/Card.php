<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';


final class Card
{
    
    public static function all(): array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT * FROM cards ORDER BY created_at DESC, id DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public static function filtered(array $where, string $orderBy): array
    {
        $pdo        = getConnection();
        $conditions = [];
        $params     = [];

        foreach (['game' => 'game', 'rarity' => 'rarity', 'edition_name' => 'edition_name'] as $key => $col) {
            $val = trim((string) ($where[$key] ?? ''));
            if ($val !== '') {
                $conditions[] = $col . ' = :' . $key;
                $params[':' . $key] = $val;
            }
        }

        $sql = 'SELECT * FROM cards';
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ' . $orderBy;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    
    public static function filterOptions(string $game = ''): array
    {
        $pdo = getConnection();

        $stmt   = $pdo->query("SELECT DISTINCT game FROM cards WHERE game <> '' ORDER BY game");
        $games  = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $where  = [];
        $params = [];
        if ($game !== '') {
            $where[]        = 'game = :game';
            $params[':game'] = $game;
        }

        return [
            'games'    => $games,
            'rarities' => self::distinctCol('rarity', $where, $params),
            'editions' => self::distinctCol('edition_name', $where, $params),
        ];
    }

    
    private static function distinctCol(string $col, array $where, array $params): array
    {
        $pdo    = getConnection();
        $conds  = $where;
        $conds[] = $col . " <> ''";

        $sql  = 'SELECT DISTINCT ' . $col . ' FROM cards WHERE ' . implode(' AND ', $conds);
        $sql .= ' ORDER BY ' . $col;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    
    public static function gamesForValue(string $col, string $value): array
    {
        $allowed = ['rarity' => 'rarity', 'edition_name' => 'edition_name'];

        if (!isset($allowed[$col])) {
            return [];
        }

        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'SELECT DISTINCT game FROM cards
              WHERE ' . $col . ' = :v AND game <> \'\''
        );
        $stmt->execute([':v' => $value]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    
    public static function find(int $id): ?array
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT * FROM cards WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $card = $stmt->fetch();

        return $card === false ? null : $card;
    }

    
    public static function create(array $data): int
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO cards
                (name_en, name_pt, game, edition_id, edition_name, image, rarity)
             VALUES
                (:name_en, :name_pt, :game, :edition_id, :edition_name, :image, :rarity)'
        );
        $stmt->execute($data);

        return (int) $pdo->lastInsertId();
    }

    
    public static function update(int $id, array $data): bool
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            'UPDATE cards
                SET name_en      = :name_en,
                    name_pt      = :name_pt,
                    game         = :game,
                    edition_id   = :edition_id,
                    edition_name = :edition_name,
                    image        = :image,
                    rarity       = :rarity
              WHERE id = :id'
        );

        $data[':id'] = $id;
        $stmt->execute($data);

        return true;
    }

    
    public static function delete(int $id): bool
    {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('DELETE FROM cards WHERE id = :id');
        $stmt->execute([':id' => $id]);

        return true;
    }
}
