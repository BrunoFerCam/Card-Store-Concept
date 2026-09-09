<?php

declare(strict_types=1);

require_once __DIR__ . '/../Auth.php';
require_once __DIR__ . '/../Flash.php';
require_once __DIR__ . '/../Models/Rarity.php';


final class RarityController
{
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

        $rarities = Rarity::all();
        $flash    = Flash::get();
        $user     = Auth::user();

        require __DIR__ . '/../Views/rarities/index.php';
    }

    private function store(): void
    {
        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            Flash::set('error', 'Informe o nome da raridade.');
            $this->redirect();
        }

        try {
            Rarity::create($name);
            Flash::set('success', 'Raridade adicionada com sucesso.');
        } catch (PDOException $e) {
            Flash::set('error', 'Essa raridade já existe.');
        }

        $this->redirect();
    }

    private function destroy(int $id): void
    {
        if ($id > 0) {
            Rarity::delete($id);
            Flash::set('success', 'Raridade excluída com sucesso.');
        }

        $this->redirect();
    }

    private function redirect(): void
    {
        header('Location: rarities.php');
        exit;
    }
}
