<?php

declare(strict_types=1);

require_once __DIR__ . '/../Auth.php';
require_once __DIR__ . '/../Flash.php';
require_once __DIR__ . '/../Models/Card.php';
require_once __DIR__ . '/../Models/Edition.php';
require_once __DIR__ . '/../Models/Rarity.php';


final class CardController
{
    
    public const GAMES = [
        'Magic: The Gathering',
        'Pokémon',
        'Yu-Gi-Oh!',
    ];

    
    public const GAME_KEYS = [
        'Magic: The Gathering' => 'magic',
        'Pokémon'              => 'pokemon',
        'Yu-Gi-Oh!'            => 'yugioh',
    ];

    
    public function index(): void
    {
        $cards = Card::all();
        $flash = Flash::get();
        $user  = Auth::user();

        require __DIR__ . '/../Views/cards/index.php';
    }

    
    public function create(): void
    {
        $data = $this->emptyData();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->inputData();
            $errors = $this->validate($data);

            if ($errors === []) {
                Card::create($this->toDb($data));
                Flash::set('success', 'Carta adicionada com sucesso.');
                $this->redirect('cards.php');
            }
        }

        $this->renderForm('Nova carta', 'cards-create.php', null, $data, $errors);
    }

    
    public function edit(?int $id): void
    {
        $card = $id === null ? null : Card::find($id);

        if ($card === null) {
            Flash::set('error', 'Carta não encontrada.');
            $this->redirect('cards.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->inputData();
            $errors = $this->validate($data);

            if ($errors === []) {
                Card::update((int) $card['id'], $this->toDb($data));
                Flash::set('success', 'Carta atualizada com sucesso.');
                $this->redirect('cards.php');
            }
        } else {
            $data   = $this->fromDb($card);
            $errors = [];
        }

        $this->renderForm(
            'Editar carta',
            'cards-edit.php?id=' . (int) $card['id'],
            (int) $card['id'],
            $data,
            $errors
        );
    }

    
    public function destroy(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cards.php');
        }

        $card = $id === null ? null : Card::find($id);

        if ($card === null) {
            Flash::set('error', 'Carta não encontrada.');
            $this->redirect('cards.php');
        }

        Card::delete((int) $card['id']);
        Flash::set('success', 'Carta excluída com sucesso.');
        $this->redirect('cards.php');
    }

    
    public function catalog(): void
    {
        $isLogged     = Auth::check();
        $user         = Auth::user();
        $rarityColors = Rarity::colorMap();

        $game    = trim((string) ($_GET['game'] ?? ''));
        $rarity  = trim((string) ($_GET['rarity'] ?? ''));
        $edition = trim((string) ($_GET['edition'] ?? ''));

        if ($game === '' && $edition !== '') {
            $g = Card::gamesForValue('edition_name', $edition);
            if (count($g) === 1) {
                $game = $g[0];
            }
        }
        if ($game === '' && $rarity !== '') {
            $g = Card::gamesForValue('rarity', $rarity);
            if (count($g) === 1) {
                $game = $g[0];
            }
        }

        $options = Card::filterOptions($game);

        if ($game !== '') {
            if ($rarity !== '' && !in_array($rarity, $options['rarities'], true)) {
                $rarity = '';
            }
            if ($edition !== '' && !in_array($edition, $options['editions'], true)) {
                $edition = '';
            }
        } else {
            $allRar  = Card::filterOptions();
            if ($rarity !== '' && !in_array($rarity, $allRar['rarities'], true)) {
                $rarity = '';
            }
            if ($edition !== '' && !in_array($edition, $allRar['editions'], true)) {
                $edition = '';
            }
        }

        $sorts = [
            'name_asc'  => 'name_en ASC, id ASC',
            'name_desc' => 'name_en DESC, id ASC',
            'game'      => 'game ASC, name_en ASC, id ASC',
            'rarity'    => 'rarity ASC, name_en ASC, id ASC',
            'edition'   => 'edition_name ASC, name_en ASC, id ASC',
        ];
        $sortKey = trim((string) ($_GET['sort'] ?? 'name_asc'));
        $sortKey = isset($sorts[$sortKey]) ? $sortKey : 'name_asc';

        $cards = Card::filtered(
            ['game' => $game, 'rarity' => $rarity, 'edition_name' => $edition],
            $sorts[$sortKey]
        );

        $filter = [
            'game'    => $game,
            'rarity'  => $rarity,
            'edition' => $edition,
            'sort'    => $sortKey,
            'options' => $options,
        ];

        require __DIR__ . '/../Views/cards/catalog.php';
    }


    private function emptyData(): array
    {
        return [
            'name_en'      => '',
            'name_pt'      => '',
            'game'         => '',
            'edition_id'   => '',
            'edition_name' => '',
            'image'        => '',
            'rarity'       => '',
        ];
    }

    private function inputData(): array
    {
        return [
            'name_en'      => trim($_POST['name_en'] ?? ''),
            'name_pt'      => trim($_POST['name_pt'] ?? ''),
            'game'         => trim($_POST['game'] ?? ''),
            'edition_id'   => trim($_POST['edition_id'] ?? ''),
            'edition_name' => trim($_POST['edition_name'] ?? ''),
            'image'        => trim($_POST['image'] ?? ''),
            'rarity'       => trim($_POST['rarity'] ?? ''),
        ];
    }

    
    private function fromDb(array $card): array
    {
        return [
            'name_en'      => (string) ($card['name_en'] ?? ''),
            'name_pt'      => (string) ($card['name_pt'] ?? ''),
            'game'         => (string) ($card['game'] ?? ''),
            'edition_id'   => $card['edition_id'] !== null ? (string) $card['edition_id'] : '',
            'edition_name' => (string) ($card['edition_name'] ?? ''),
            'image'        => (string) ($card['image'] ?? ''),
            'rarity'       => (string) ($card['rarity'] ?? ''),
        ];
    }

    
    private function toDb(array $data): array
    {
        return [
            ':name_en'      => $data['name_en'],
            ':name_pt'      => $data['name_pt'] === '' ? null : $data['name_pt'],
            ':game'         => $data['game'],
            ':edition_id'   => $data['edition_id'] === '' ? null : $data['edition_id'],
            ':edition_name' => $data['edition_name'],
            ':image'        => $data['image'] === '' ? null : $data['image'],
            ':rarity'       => $data['rarity'] === '' ? null : $data['rarity'],
        ];
    }

    
    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['name_en'] === '') {
            $errors['name_en'] = 'Informe o nome em inglês.';
        } elseif ($this->length($data['name_en']) > 120) {
            $errors['name_en'] = 'O nome em inglês deve ter no máximo 120 caracteres.';
        }

        if ($data['game'] === '') {
            $errors['game'] = 'Selecione um Card Game.';
        } elseif (!in_array($data['game'], self::GAMES, true)) {
            $errors['game'] = 'Card Game inválido.';
        }

        if ($data['edition_name'] === '') {
            $errors['edition_name'] = 'Informe a edição.';
        } elseif ($this->length($data['edition_name']) > 120) {
            $errors['edition_name'] = 'A edição deve ter no máximo 120 caracteres.';
        } elseif ($data['edition_id'] === '') {
            $errors['edition_name'] = 'Selecione uma edição válida.';
        } else {
            $edition = Edition::findByGameAndCode($data['game'], $data['edition_id']);
            if ($edition === null || $edition['name'] !== $data['edition_name']) {
                $errors['edition_name'] = 'A edição não pertence ao Card Game selecionado.';
            }
        }

        if ($data['name_pt'] !== '' && $this->length($data['name_pt']) > 120) {
            $errors['name_pt'] = 'O nome em português deve ter no máximo 120 caracteres.';
        }

        if ($data['image'] !== '' && $this->length($data['image']) > 255) {
            $errors['image'] = 'O campo imagem deve ter no máximo 255 caracteres.';
        }

        if ($data['rarity'] !== '' && $this->length($data['rarity']) > 40) {
            $errors['rarity'] = 'A raridade deve ter no máximo 40 caracteres.';
        } elseif ($data['rarity'] !== '' && !Rarity::existsForGame($data['game'], $data['rarity'])) {
            $errors['rarity'] = 'A raridade não pertence ao Card Game selecionado.';
        }

        return $errors;
    }

    private function renderForm(
        string $title,
        string $action,
        ?int $cardId,
        array $data,
        array $errors
    ): void {
        $games    = self::GAMES;
        $gameKeys = self::GAME_KEYS;
        $rarities = Rarity::all();
        $user     = Auth::user();

        require __DIR__ . '/../Views/cards/form.php';
    }

    private function redirect(string $target): void
    {
        header('Location: ' . $target);
        exit;
    }
}
