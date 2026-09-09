<?php

declare(strict_types=1);

require_once __DIR__ . '/../Auth.php';
require_once __DIR__ . '/../Flash.php';
require_once __DIR__ . '/../Models/Card.php';
require_once __DIR__ . '/../Models/Edition.php';
require_once __DIR__ . '/../Models/Rarity.php';


final class ManageController
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

    private string $activeTab = 'cartas';
    private string $openModal = '';
    private string $cardMode  = 'create';
    private int    $cardId    = 0;
    private array  $cardData  = [];
    private array  $cardErrors = [];
    private array  $editionData = [];
    private array  $editionErrors = [];
    private array  $rarityData = [];
    private array  $rarityErrors = [];

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $tab = $_GET['tab'] ?? 'cartas';
        $this->activeTab = in_array($tab, ['cartas', 'edicoes', 'raridades'], true) ? $tab : 'cartas';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $nova = $_GET['nova'] ?? '';

            if ($nova === 'edicao') {
                $this->openModal  = 'edition';
                $this->activeTab  = 'edicoes';
            } elseif ($nova === 'raridade') {
                $this->openModal = 'rarity';
                $this->activeTab = 'raridades';
            } elseif ($nova === 'carta') {
                $this->openModal = 'card';
                $this->cardMode  = 'create';
                $this->activeTab = 'cartas';
            }

            if (isset($_GET['edit'])) {
                $card = Card::find((int) $_GET['edit']);
                if ($card !== null) {
                    $this->openModal = 'card';
                    $this->cardMode  = 'edit';
                    $this->cardId    = (int) $card['id'];
                    $this->cardData  = $this->cardFromDb($card);
                    $this->activeTab = 'cartas';
                }
            }
        }

        $cards        = Card::all();
        $editions     = Edition::all();
        $rarities     = Rarity::all();
        $rarityColors = Rarity::colorMap();
        $flash        = Flash::get();
        $user         = Auth::user();
        $games        = self::GAMES;
        $gameKeys     = self::GAME_KEYS;

        $activeTab     = $this->activeTab;
        $openModal     = $this->openModal;
        $cardMode      = $this->cardMode;
        $cardId        = $this->cardId;
        $cardData      = $this->cardData;
        $cardErrors    = $this->cardErrors;
        $editionData   = $this->editionData;
        $editionErrors = $this->editionErrors;
        $rarityData    = $this->rarityData;
        $rarityErrors  = $this->rarityErrors;

        require __DIR__ . '/../Views/manage/index.php';
    }

    private function handlePost(): void
    {
        $form = $_POST['form'] ?? '';

        switch ($form) {
            case 'card_create':
                $this->saveCard(0);
                break;
            case 'card_update':
                $this->saveCard((int) ($_POST['card_id'] ?? 0));
                break;
            case 'edition':
                $this->saveEdition();
                break;
            case 'rarity':
                $this->saveRarity();
                break;
            case 'delete':
                $this->deleteRow($_POST['kind'] ?? '', (int) ($_POST['id'] ?? 0));
                break;
        }
    }


    private function cardInput(): array
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

    private function cardErrors(array $d): array
    {
        $e = [];

        if ($d['name_en'] === '') {
            $e['name_en'] = 'Informe o nome em inglês.';
        } elseif ($this->len($d['name_en']) > 120) {
            $e['name_en'] = 'Máximo de 120 caracteres.';
        }

        if (!in_array($d['game'], self::GAMES, true)) {
            $e['game'] = 'Selecione um Card Game válido.';
        }

        if ($d['edition_name'] === '') {
            $e['edition_name'] = 'Selecione a edição.';
        } elseif ($this->len($d['edition_name']) > 120) {
            $e['edition_name'] = 'Máximo de 120 caracteres.';
        } elseif ($d['edition_id'] === '') {
            $e['edition_name'] = 'Selecione uma edição válida.';
        } else {
            $edition = Edition::findByGameAndCode($d['game'], $d['edition_id']);
            if ($edition === null || $edition['name'] !== $d['edition_name']) {
                $e['edition_name'] = 'A edição não pertence ao Card Game selecionado.';
            }
        }

        if ($d['name_pt'] !== '' && $this->len($d['name_pt']) > 120) {
            $e['name_pt'] = 'Máximo de 120 caracteres.';
        }
        if ($d['image'] !== '' && $this->len($d['image']) > 255) {
            $e['image'] = 'Máximo de 255 caracteres.';
        }
        if ($d['rarity'] !== '' && $this->len($d['rarity']) > 40) {
            $e['rarity'] = 'Máximo de 40 caracteres.';
        } elseif ($d['rarity'] !== '' && !Rarity::existsForGame($d['game'], $d['rarity'])) {
            $e['rarity'] = 'A raridade não pertence ao Card Game selecionado.';
        }

        return $e;
    }

    private function cardToDb(array $d): array
    {
        return [
            ':name_en'      => $d['name_en'],
            ':name_pt'      => $d['name_pt'] === '' ? null : $d['name_pt'],
            ':game'         => $d['game'],
            ':edition_id'   => $d['edition_id'] === '' ? null : $d['edition_id'],
            ':edition_name' => $d['edition_name'],
            ':image'        => $d['image'] === '' ? null : $d['image'],
            ':rarity'       => $d['rarity'] === '' ? null : $d['rarity'],
        ];
    }

    private function cardFromDb(array $card): array
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

    private function saveCard(int $id): void
    {
        $data   = $this->cardInput();
        $errors = $this->cardErrors($data);

        if ($errors !== []) {
            $this->openModal   = 'card';
            $this->cardMode    = $id > 0 ? 'edit' : 'create';
            $this->cardId      = $id;
            $this->cardData    = $data;
            $this->cardErrors  = $errors;
            $this->activeTab   = 'cartas';
            return;
        }

        if ($id > 0) {
            if (Card::find($id) === null) {
                Flash::set('error', 'Carta não encontrada.');
            } else {
                Card::update($id, $this->cardToDb($data));
                Flash::set('success', 'Carta atualizada com sucesso.');
            }
        } else {
            Card::create($this->cardToDb($data));
            Flash::set('success', 'Carta adicionada com sucesso.');
        }

        $this->go('gerenciar.php?tab=cartas');
    }


    private function saveEdition(): void
    {
        $game = trim($_POST['game'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');

        $this->editionData = ['game' => $game, 'code' => $code, 'name' => $name];

        if (!in_array($game, self::GAMES, true)) {
            $this->editionErrors['game'] = 'Selecione um Card Game válido.';
        }
        if ($code === '') {
            $this->editionErrors['code'] = 'Informe o código.';
        } elseif ($this->len($code) > 20) {
            $this->editionErrors['code'] = 'Máximo de 20 caracteres.';
        }
        if ($name === '') {
            $this->editionErrors['name'] = 'Informe o nome.';
        } elseif ($this->len($name) > 120) {
            $this->editionErrors['name'] = 'Máximo de 120 caracteres.';
        }

        if ($this->editionErrors !== []) {
            $this->openModal = 'edition';
            $this->activeTab = 'edicoes';
            return;
        }

        try {
            Edition::create($game, $code, $name);
            Flash::set('success', 'Edição adicionada com sucesso.');
        } catch (PDOException $e) {
            Flash::set('error', 'Já existe uma edição com esse código para o game.');
        }

        $this->go('gerenciar.php?tab=edicoes');
    }


    private function saveRarity(): void
    {
        $game  = trim($_POST['game'] ?? '');
        $name  = trim($_POST['name'] ?? '');
        $color = trim($_POST['color'] ?? '');

        $this->rarityData = ['game' => $game, 'name' => $name, 'color' => $color];

        if (!in_array($game, self::GAMES, true)) {
            $this->rarityErrors['game'] = 'Selecione um Card Game válido.';
        }
        if ($name === '') {
            $this->rarityErrors['name'] = 'Informe o nome.';
        } elseif ($this->len($name) > 40) {
            $this->rarityErrors['name'] = 'Máximo de 40 caracteres.';
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $this->rarityErrors['color'] = 'Cor inválida.';
        }

        if ($this->rarityErrors !== []) {
            $this->openModal = 'rarity';
            $this->activeTab = 'raridades';
            return;
        }

        try {
            Rarity::create($game, $name, $color);
            Flash::set('success', 'Raridade adicionada com sucesso.');
        } catch (PDOException $e) {
            Flash::set('error', 'Essa raridade já existe para o game.');
        }

        $this->go('gerenciar.php?tab=raridades');
    }


    private function deleteRow(string $kind, int $id): void
    {
        switch ($kind) {
            case 'card':
                if ($id > 0) {
                    Card::delete($id);
                    Flash::set('success', 'Carta excluída com sucesso.');
                    $this->go('gerenciar.php?tab=cartas');
                }
                break;
            case 'edition':
                if ($id > 0) {
                    Edition::delete($id);
                    Flash::set('success', 'Edição excluída com sucesso.');
                    $this->go('gerenciar.php?tab=edicoes');
                }
                break;
            case 'rarity':
                if ($id > 0) {
                    Rarity::delete($id);
                    Flash::set('success', 'Raridade excluída com sucesso.');
                    $this->go('gerenciar.php?tab=raridades');
                }
                break;
        }
    }


    private function len(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    private function go(string $target): void
    {
        header('Location: ' . $target);
        exit;
    }
}
