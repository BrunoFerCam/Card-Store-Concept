<?php

declare(strict_types=1);



require_once __DIR__ . '/../config/database.php';


$username = 'admin';
$password = 'admin123';

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = getConnection();


    $stmt = $pdo->prepare(
        'SELECT id FROM users WHERE username = :username LIMIT 1'
    );

    $stmt->execute([
        ':username' => $username,
    ]);

    $userExists = $stmt->fetchColumn() !== false;

    if ($userExists) {
        $stmt = $pdo->prepare(
            'UPDATE users
             SET password_hash = :password_hash
             WHERE username = :username'
        );

        $stmt->execute([
            ':username'      => $username,
            ':password_hash' => $passwordHash,
        ]);

        echo "Usuário '{$username}' atualizado.\n";
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, password_hash)
             VALUES (:username, :password_hash)'
        );

        $stmt->execute([
            ':username'      => $username,
            ':password_hash' => $passwordHash,
        ]);

        echo "Usuário '{$username}' criado.\n";
    }


    $pdo->exec('DELETE FROM editions');

    $editions = [

        ['Magic: The Gathering', 'lea',   'Limited Edition Alpha'],
        ['Magic: The Gathering', 'leb',   'Limited Edition Beta'],
        ['Magic: The Gathering', '2ed',   'Unlimited Edition'],
        ['Magic: The Gathering', 'dom',   'Dominaria'],
        ['Magic: The Gathering', 'war',   'War of the Spark'],
        ['Magic: The Gathering', 'eld',   'Throne of Eldraine'],
        ['Magic: The Gathering', 'hob',   'The Hobbit'],
        ['Magic: The Gathering', 'msh',   'Marvel Super Heroes'],

        ['Pokémon', 'base1', 'Base Set'],
        ['Pokémon', 'swsh1', 'Sword & Shield'],
        ['Pokémon', 'sv1',   'Scarlet & Violet'],
        ['Pokémon', '30c',   '30th Celebration'],
        ['Pokémon', 'cri',   'Chaos Rising'],

        ['Yu-Gi-Oh!', 'lob',  'Legend of Blue Eyes White Dragon'],
        ['Yu-Gi-Oh!', 'mrd',  'Metal Raiders'],
        ['Yu-Gi-Oh!', 'sdy',  'Starter Deck: Yugi'],
        ['Yu-Gi-Oh!', 'rotd', 'Rise of the Duelist'],
        ['Yu-Gi-Oh!', 'blzd', 'Blazing Dominion'],
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO editions (game, code, name)
         VALUES (:game, :code, :name)'
    );

    foreach ($editions as $edition) {
        $stmt->execute([
            ':game' => $edition[0],
            ':code' => $edition[1],
            ':name' => $edition[2],
        ]);
    }

    echo count($editions) . " edições inseridas.\n";


    $pdo->exec('DELETE FROM rarities');

    

    $rarities = [

        ['Magic: The Gathering', 'Common',      '#6B7280'],
        ['Magic: The Gathering', 'Uncommon',    '#9CA3AF'],
        ['Magic: The Gathering', 'Rare',        '#D4AF37'],
        ['Magic: The Gathering', 'Mythic Rare', '#D97706'],

        ['Pokémon', 'Common',      '#6B7280'],
        ['Pokémon', 'Uncommon',    '#9CA3AF'],
        ['Pokémon', 'Rare',        '#2563EB'],
        ['Pokémon', 'Rare Holo',   '#EAB308'],
        ['Pokémon', 'Ultra Rare',  '#9333EA'],
        ['Pokémon', 'Secret Rare', '#10B981'],

        ['Yu-Gi-Oh!', 'Common',      '#6B7280'],
        ['Yu-Gi-Oh!', 'Rare',        '#60A5FA'],
        ['Yu-Gi-Oh!', 'Super Rare',  '#A78BFA'],
        ['Yu-Gi-Oh!', 'Ultra Rare',  '#F59E0B'],
        ['Yu-Gi-Oh!', 'Secret Rare', '#E5E7EB'],
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO rarities (game, name, color)
         VALUES (:game, :name, :color)'
    );

    foreach ($rarities as $rarity) {
        $stmt->execute([
            ':game'  => $rarity[0],
            ':name'  => $rarity[1],
            ':color' => $rarity[2],
        ]);
    }

    echo count($rarities) . " raridades inseridas.\n";


    $pdo->exec('TRUNCATE TABLE cards');

    

    $cards = [


    [
        'name_en'      => 'Black Lotus',
        'name_pt'      => 'Lótus Negra',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Black%20Lotus&set=lea&format=image',
        'rarity'       => 'Rare',
    ],

    [
        'name_en'      => 'Lightning Bolt',
        'name_pt'      => 'Raio',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Lightning%20Bolt&set=lea&format=image',
        'rarity'       => 'Common',
    ],

    [
        'name_en'      => 'Counterspell',
        'name_pt'      => 'Contramágica',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Counterspell&set=lea&format=image',
        'rarity'       => 'Common',
    ],

    [
        'name_en'      => 'Shivan Dragon',
        'name_pt'      => 'Dragão Shivan',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Shivan%20Dragon&set=lea&format=image',
        'rarity'       => 'Rare',
    ],

    [
        'name_en'      => 'Serra Angel',
        'name_pt'      => 'Anjo de Serra',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Serra%20Angel&set=lea&format=image',
        'rarity'       => 'Uncommon',
    ],

    [
        'name_en'      => 'Llanowar Elves',
        'name_pt'      => 'Elfos de Llanowar',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Llanowar%20Elves&set=lea&format=image',
        'rarity'       => 'Common',
    ],

    [
        'name_en'      => 'Swords to Plowshares',
        'name_pt'      => 'Espadas em Arados',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Swords%20to%20Plowshares&set=lea&format=image',
        'rarity'       => 'Uncommon',
    ],

    [
        'name_en'      => 'Wrath of God',
        'name_pt'      => 'Cólera de Deus',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Wrath%20of%20God&set=lea&format=image',
        'rarity'       => 'Rare',
    ],

    [
        'name_en'      => 'Birds of Paradise',
        'name_pt'      => 'Aves do Paraíso',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Birds%20of%20Paradise&set=lea&format=image',
        'rarity'       => 'Rare',
    ],

    [
        'name_en'      => 'Mox Sapphire',
        'name_pt'      => 'Mox de Safira',
        'game'         => 'Magic: The Gathering',
        'edition_id'   => 'lea',
        'edition_name' => 'Limited Edition Alpha',
        'image'        => 'https://api.scryfall.com/cards/named?exact=Mox%20Sapphire&set=lea&format=image',
        'rarity'       => 'Rare',
    ],


    [
        'name_en'      => 'Charizard',
        'name_pt'      => 'Charizard',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/4_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Blastoise',
        'name_pt'      => 'Blastoise',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/2_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Venusaur',
        'name_pt'      => 'Venusaur',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/15_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Pikachu',
        'name_pt'      => 'Pikachu',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/58_hires.png',
        'rarity'       => 'Common',
    ],

    [
        'name_en'      => 'Mewtwo',
        'name_pt'      => 'Mewtwo',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/10_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Alakazam',
        'name_pt'      => 'Alakazam',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/1_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Raichu',
        'name_pt'      => 'Raichu',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/14_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Zapdos',
        'name_pt'      => 'Zapdos',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/16_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Ninetales',
        'name_pt'      => 'Ninetales',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/12_hires.png',
        'rarity'       => 'Rare Holo',
    ],

    [
        'name_en'      => 'Poliwrath',
        'name_pt'      => 'Poliwrath',
        'game'         => 'Pokémon',
        'edition_id'   => 'base1',
        'edition_name' => 'Base Set',
        'image'        => 'https://images.pokemontcg.io/base1/13_hires.png',
        'rarity'       => 'Rare Holo',
    ],


    [
        'name_en'      => 'Blue-Eyes White Dragon',
        'name_pt'      => 'Dragão Branco de Olhos Azuis',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/89631139.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Dark Magician',
        'name_pt'      => 'Mago Negro',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/46986414.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Red-Eyes Black Dragon',
        'name_pt'      => 'Dragão Negro de Olhos Vermelhos',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/74677422.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Exodia the Forbidden One',
        'name_pt'      => 'Exodia, o Proibido',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/33396948.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Dark Hole',
        'name_pt'      => 'Buraco Negro',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/53129443.jpg',
        'rarity'       => 'Rare',
    ],

    [
        'name_en'      => 'Monster Reborn',
        'name_pt'      => 'Renascido pelo Monstro',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://images.ygoprodeck.com/images/cards/83764718.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Gaia The Fierce Knight',
        'name_pt'      => 'Gaia, o Cavaleiro Feroz',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'lob',
        'edition_name' => 'Legend of Blue Eyes White Dragon',
        'image'        => 'https://img.mypcards.com/cdn-cgi/image/h=425,fit=contain,f=auto/img/3/979/yugioh_ygld-ena05/yugioh_ygld-ena05_pt.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Summoned Skull',
        'name_pt'      => 'Caveira Invocada',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'mrd',
        'edition_name' => 'Metal Raiders',
        'image'        => 'https://images.ygoprodeck.com/images/cards/70781052.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Mirror Force',
        'name_pt'      => 'Força Espelho',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'mrd',
        'edition_name' => 'Metal Raiders',
        'image'        => 'https://images.ygoprodeck.com/images/cards/44095762.jpg',
        'rarity'       => 'Ultra Rare',
    ],

    [
        'name_en'      => 'Heavy Storm',
        'name_pt'      => 'Tempestade Pesada',
        'game'         => 'Yu-Gi-Oh!',
        'edition_id'   => 'mrd',
        'edition_name' => 'Metal Raiders',
        'image'        => 'https://images.ygoprodeck.com/images/cards/19613556.jpg',
        'rarity'       => 'Ultra Rare',
    ],
];


    $stmt = $pdo->prepare(
        'INSERT INTO cards
            (
                name_en,
                name_pt,
                game,
                edition_id,
                edition_name,
                image,
                rarity
            )
         VALUES
            (
                :name_en,
                :name_pt,
                :game,
                :edition_id,
                :edition_name,
                :image,
                :rarity
            )'
    );

    $inserted = 0;

    foreach ($cards as $card) {
        $stmt->execute([
            ':name_en'      => $card['name_en'],
            ':name_pt'      => $card['name_pt'],
            ':game'         => $card['game'],
            ':edition_id'   => $card['edition_id'],
            ':edition_name' => $card['edition_name'],
            ':image'        => $card['image'],
            ':rarity'       => $card['rarity'],
        ]);

        $inserted++;
    }

    echo "{$inserted} cartas inseridas.\n";


    echo "Seed concluído com sucesso.\n";

} catch (PDOException $e) {
    fwrite(
        STDERR,
        'Erro no seed: ' . $e->getMessage() . PHP_EOL
    );

    exit(1);
}