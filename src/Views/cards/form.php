<?php


$pageTitle = $title;
$base      = '..';

$breadcrumbs = [
    ['Dashboard', 'index.php'],
    ['Cartas', 'cards.php'],
    [$title, null],
];

$val = function (string $key) use ($data): string {
    return htmlspecialchars((string) ($data[$key] ?? ''), ENT_QUOTES, 'UTF-8');
};
$err = function (string $key) use ($errors): ?string {
    return $errors[$key] ?? null;
};

require __DIR__ . '/../layout/header.php';
?>
<div class="page-head">
    <h1><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?></h1>
    <a class="btn btn-link" href="cards.php">Voltar</a>
</div>

<?php if ($errors !== []): ?>
    <div class="alert alert-error" data-dismissible>Verifique os campos destacados e tente novamente.</div>
<?php endif; ?>

<form class="card-form" method="post" action="<?= htmlspecialchars((string) $action, ENT_QUOTES, 'UTF-8') ?>">
    <div class="grid">
        <div class="field">
            <label for="name_en">Nome em inglês <span class="req">*</span></label>
            <input type="text" id="name_en" name="name_en" value="<?= $val('name_en') ?>" maxlength="120" required<?= $err('name_en') !== null ? ' class="input-error"' : '' ?>>
            <?php if ($err('name_en') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('name_en'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="name_pt">Nome em português</label>
            <input type="text" id="name_pt" name="name_pt" value="<?= $val('name_pt') ?>" maxlength="120"<?= $err('name_pt') !== null ? ' class="input-error"' : '' ?>>
            <?php if ($err('name_pt') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('name_pt'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="game">Card Game <span class="req">*</span></label>
            <select id="game" name="game" required<?= $err('game') !== null ? ' class="input-error"' : '' ?>>
                <option value="">Selecione…</option>
                <?php foreach ($games as $game): ?>
                    <option value="<?= htmlspecialchars((string) $game, ENT_QUOTES, 'UTF-8') ?>"
                        data-game-key="<?= htmlspecialchars((string) ($gameKeys[$game] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($data['game'] ?? '') === $game ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $game, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($err('game') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('game'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        
        <div class="field" id="edition-field">
            <label for="edition_id">Edição <span class="req">*</span></label>
            <select id="edition_id" name="edition_id" data-current="<?= $val('edition_id') ?>" required disabled<?= $err('edition_name') !== null ? ' class="input-error"' : '' ?>>
                <option value="">Selecione o Card Game primeiro</option>
            </select>
            <input type="hidden" id="edition_name" name="edition_name" value="<?= $val('edition_name') ?>">
            <?php if ($err('edition_name') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('edition_name'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="image">Imagem da carta</label>
            <input type="text" id="image" name="image" value="<?= $val('image') ?>" maxlength="255"
                   placeholder="URL ou caminho da imagem"<?= $err('image') !== null ? ' class="input-error"' : '' ?>>
            <p class="hint">Informe a URL (http…/https…/caminho) da imagem.</p>
            <div class="image-preview" id="image-preview">Sem pré-visualização</div>
            <?php if ($err('image') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('image'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="rarity">Raridade</label>
            <input type="text" id="rarity" name="rarity" value="<?= $val('rarity') ?>" maxlength="40"
                   list="rarity-options"<?= $err('rarity') !== null ? ' class="input-error"' : '' ?>>
            <datalist id="rarity-options">
                <?php foreach ($rarities as $rarity): ?>
                    <option value="<?= htmlspecialchars((string) $rarity['name'], ENT_QUOTES, 'UTF-8') ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <?php if ($err('rarity') !== null): ?>
                <p class="field-error"><?= htmlspecialchars((string) $err('rarity'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">Salvar</button>
        <a class="btn btn-secondary" href="cards.php">Cancelar</a>
    </div>
</form>

<script src="../assets/js/editions.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
