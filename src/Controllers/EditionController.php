<?php

declare(strict_types=1);

require_once __DIR__ . '/../Auth.php';
require_once __DIR__ . '/../Flash.php';
require_once __DIR__ . '/../Models/Edition.php';


final class EditionController
{
    public const GAMES = [
        'Magic: The Gathering',
        'Pokémon',
        'Yu-Gi-Oh!',
    ];

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'delete') {
                $this->destroy((int) ($_POST['id'] ?? 0));
            } else {
                $this->store();
            }
            return;
        }

        $editions = Edition::all();
        $flash    = Flash::get();
        $user     = Auth::user();

        require __DIR__ . '/../Views/editions/index.php';
    }

    private function store(): void
    {
        $game = trim($_POST['game'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if (!in_array($game, self::GAMES, true)) {
            Flash::set('error', 'Selecione um Card Game válido.');
            $this->redirect();
        }

        if ($code === '' || $name === '') {
            Flash::set('error', 'Informe o código e o nome da edição.');
            $this->redirect();
        }

        try {
            Edition::create($game, $code, $name);
            Flash::set('success', 'Edição adicionada com sucesso.');
        } catch (PDOException $e) {
            Flash::set('error', 'Já existe uma edição com esse código para o game.');
        }

        $this->redirect();
    }

    private function destroy(int $id): void
    {
        if ($id > 0) {
            Edition::delete($id);
            Flash::set('success', 'Edição excluída com sucesso.');
        }

        $this->redirect();
    }

    private function redirect(): void
    {
        header('Location: editions.php');
        exit;
    }
}
